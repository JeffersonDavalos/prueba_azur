<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; }
        .task-container { max-width: 600px; margin: auto; text-align: center; }
        .task { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ddd; }
        .task button { background-color: red; color: white; border: none; padding: 5px; cursor: pointer; }
        .success, .error { padding: 10px; margin: 10px; display: none; }
        .success { background: green; color: white; }
        .error { background: red; color: white; }
        .task-db { font-weight: bold; } 
        .task-api { font-style: italic; color: gray; } 
    </style>
</head>
<body>

    <div class="task-container">
        <h2>Lista de Tareas</h2>
        <div>
            <select id="estadoSelect">
                <option value="">Seleccionar estado</option>
            </select>
            <button id="listarPorEstado">Listar</button>
        </div>

        <br>
        <div>
            <input type="text" id="taskTitle" placeholder="Título de la tarea">
            <button id="addTask">Agregar</button>
        </div>

        <div id="message" class="success"></div>
        <div id="messageError" class="error"></div>

        <div id="taskList"></div>
    </div>

    <script>
        $(document).ready(function() {
            loadEstados();
            loadTasks();
            function loadEstados() {
                $.ajax({
                    url: "/api/estado",
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log("Estados recibidos:", response);
                        if (response.success) {
                            $("#estadoSelect").html('<option value="">Seleccionar estado</option>');
                            response.data.forEach(estado => {
                                $("#estadoSelect").append(`<option value="${estado.id_status}">${estado.descripcion}</option>`);
                            });
                        } else {
                            showError("No se pudieron cargar los estados.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al cargar los estados:", error);
                        showError("Error al cargar los estados.");
                    }
                });
            }
            function loadTasks(estadoId = null) {
                let url = "/api/tasks";
                if (estadoId) {
                    url += `?estado=${estadoId}`;
                }

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log("Tareas combinadas recibidas:", response);
                        if (!response.success || !Array.isArray(response.tareas)) {
                            showError("No se pudieron obtener las tareas.");
                            return;
                        }

                        $("#taskList").html("");

                        response.tareas.forEach(task => {
                            let taskHTML = `
                                <div class="task ${task.source === 'DB' ? 'task-db' : 'task-api'}">
                                    <span>${task.title} <small>(${task.source})</small></span>
                            `;
                            if (task.source === "DB") {
                                taskHTML += `<button onclick="deleteTask(${task.id})">Eliminar</button>`;
                            }

                            taskHTML += `</div>`;

                            $("#taskList").append(taskHTML);
                        });

                    },
                    error: function(xhr, status, error) {
                        console.error("Error al obtener las tareas:", error);
                        showError("No se pudieron obtener las tareas.");
                    }
                });
            }
            $("#listarPorEstado").click(function() {
                let estadoId = $("#estadoSelect").val();
                loadTasks(estadoId);
            });
            $("#addTask").click(function() {
                let title = $("#taskTitle").val().trim();

                if (title === "") {
                    showError("El título no puede estar vacío.");
                    return;
                }

                console.log("Enviando datos:", { title: title });

                $.ajax({
                    url: "/api/tareas", 
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({ title: title }), 
                })
                .done(function(response) {
                    console.log("Respuesta de la API:", response);
                    if (response.success) {
                        showMessage("Tarea agregada correctamente.");
                        $("#taskTitle").val("");
                        loadTasks();
                    } else {
                        showError(response.error || "No se pudo agregar la tarea.");
                    }
                })
                .fail(function(xhr) {
                    console.error("Error al agregar tarea:", xhr.responseText);
                    showError("Error inesperado al agregar la tarea.");
                });
            });
            window.deleteTask = function(id) {
                $.ajax({
                    url: `/api/tasks/${id}`,
                    type: "DELETE",
                    dataType: "json",
                    success: function(response) {
                        console.log("Tarea eliminada:", response);
                        showMessage("Tarea eliminada.");
                        loadTasks();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al eliminar tarea:", error);
                        showError("Error al eliminar la tarea.");
                    }
                });
            };
            function showMessage(msg) {
                $("#message").text(msg).fadeIn().delay(2000).fadeOut();
            }
            function showError(msg) {
                $("#messageError").text(msg).fadeIn().delay(4000).fadeOut();
            }
        });
    </script>

</body>
</html>
