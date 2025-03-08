<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use App\Http\Services\TaskService;
use Illuminate\Routing\Controller; 
use Illuminate\Http\Request;

use Exception;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function getTasks(): JsonResponse
    {
        try {
            $response = Http::get('https://jsonplaceholder.typicode.com/todos');
            $tareasExternas = $response->successful() ? $response->json() : [];
            $tareasInternas = $this->taskService->obtenertareas();
            if (!$tareasInternas->getData()->success) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Error al obtener las tareas internas'
                ], 500);
            }
            $tareasInternasData = $tareasInternas->getData()->data;
            foreach ($tareasInternasData as &$tarea) {
                $tarea->source = "DB"; 
            }
    
            foreach ($tareasExternas as &$tarea) {
                $tarea['source'] = "API"; 
            }
            $tareasCombinadas = array_merge($tareasInternasData, $tareasExternas);
    
            return response()->json([
                'success' => true,
                'tareas'  => $tareasCombinadas
            ], 200);
    
        } catch (Exception $e) {
            Log::error('Error en el controlador getTasks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }
    

    public function estados(): JsonResponse
    {
        try {
            $resultado = $this->taskService->obtenerEstados();
            return $resultado;
        } catch (Exception $e) {
            Log::error('Error en el controlador obtenerEstados: ' . $e->getMessage());
            return response()->json([
                'message' => 'Ha ocurrido un error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function agregarTarea(Request $request): JsonResponse
    {
        try {
            Log::info('Solicitud recibida en agregarTarea', ['request_data' => $request->all()]);
            if (!$request->has('title') || empty($request->title)) {
                Log::warning('Fallo la validación: title es obligatorio.', ['request_data' => $request->all()]);
                return response()->json([
                    'success' => false,
                    'error'   => 'El campo title es obligatorio.'
                ], 400);
            }
    
            $response = $this->taskService->agregarTarea($request->all());
            Log::info('Respuesta del servicio agregarTarea', ['response' => $response]);
    
            return $response;
    
        } catch (Exception $e) {
            Log::error('Error en el controlador agregarTarea', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
    
            return response()->json([
                'success' => false,
                'error'   => 'Error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }
    

}
