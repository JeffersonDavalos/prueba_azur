<?php

namespace App\Http\Services;
use App\Models\status;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\DB; 

class TaskService
{
    public function obtenerEstados(): JsonResponse
    {
        try {
            $estados = status::all();
            return response()->json([
                'success' => true,
                'data' => $estados
            ], 200);
        } catch (QueryException $e) {
            Log::error('Error al obtener los estados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener los estados: ' . $e->getMessage()
            ], 500);
        }
    }

    public function obtenertareas(): JsonResponse
    {
        try {
            $estados = Task::all();
            return response()->json([
                'success' => true,
                'data' => $estados
            ], 200);
        } catch (QueryException $e) {
            Log::error('Error al obtener los estados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener los estados: ' . $e->getMessage()
            ], 500);
        }
    }
    public function agregarTarea($data): JsonResponse
    {
        try {
            Log::info('Datos recibidos en TaskService', ['data' => $data]);
    
            $tarea = Task::create([
                'id_apiextrena' => null,
                'title'       => $data['title'],
                'description' => $data['descripcion'] ?? null,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
    
            Log::info('Tarea creada con éxito', ['task' => $tarea]);
    
            return response()->json([
                'success' => true,
                'message' => 'Tarea agregada correctamente',
                'data'    => $tarea
            ], 201);
    
        } catch (QueryException $e) {
            Log::error('Error al agregar tarea en TaskService', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
    
            return response()->json([
                'success' => false,
                'error'   => 'Error al agregar tarea: ' . $e->getMessage()
            ], 500);
        }
    }
    
}
