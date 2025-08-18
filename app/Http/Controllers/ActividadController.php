<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadAprendiz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $request->validate([
        'descripcion' => 'required|string',
        'fecha_entrega' => 'required|date',
        'criterios_valoracion' => 'required|string',
        'ficha_id' => 'nullable|exists:fichas,id',
        'aprendices' => 'nullable|array',
        'aprendices.*' => 'exists:users,id',
    ]);

    // Crear la actividad
    $actividad = Actividad::create([
        'descripcion' => $request->descripcion,
        'fecha_entrega' => $request->fecha_entrega,
        'criterios_valoracion' => $request->criterios_valoracion,
        'ficha_id' => $request->ficha_id,
    ]);

    // Asignar la actividad
    if ($request->filled('aprendices')) {
        // Asignar a aprendices específicos (solo si tienen role 'aprendiz')
        foreach ($request->aprendices as $aprendiz_id) {
            $aprendiz = User::where('id', $aprendiz_id)->where('role', 'aprendiz')->first();
            if ($aprendiz) {
                ActividadAprendiz::create([
                    'actividad_id' => $actividad->id,
                    'aprendiz_id' => $aprendiz->id,
                ]);
            }
        }
    } elseif ($request->filled('ficha_id')) {
        // Obtener los IDs de los aprendices de la ficha desde la tabla matriculas
        $aprendices_ids = DB::table('matriculas')
            ->where('ficha_id', $request->ficha_id)
            ->pluck('aprendiz_id');

        // Obtener solo los usuarios con rol 'aprendiz'
        $aprendices = User::whereIn('id', $aprendices_ids)
            ->where('role', 'aprendiz')
            ->get();

        foreach ($aprendices as $aprendiz) {
            ActividadAprendiz::create([
                'actividad_id' => $actividad->id,
                'aprendiz_id' => $aprendiz->id,
            ]);
        }
    }

    return response()->json(['message' => 'Actividad creada y asignada correctamente'], 201);
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function aprendicesPorFicha($ficha_id)
    {
        $aprendices = \App\Models\User::where('role', 'aprendiz')
            ->whereIn('id', function($query) use ($ficha_id) {
                $query->select('aprendiz_id')
                    ->from('matriculas')
                    ->where('ficha_id', $ficha_id)
                    ->where('estado', 'activa');
            })
            ->get();

        return response()->json($aprendices);
    }

    /**
     * Devuelve las actividades asignadas a los aprendices de una ficha específica.
     *
     * @param int $ficha_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function actividadesPorFicha($ficha_id)
    {
        // Obtener los aprendices activos de la ficha
        $aprendices_ids = DB::table('matriculas')
            ->where('ficha_id', $ficha_id)
            ->where('estado', 'activa')
            ->pluck('aprendiz_id');

        // Obtener las asignaciones de actividades para esos aprendices
        $asignaciones = ActividadAprendiz::with(['actividad', 'aprendiz'])
            ->whereIn('aprendiz_id', $aprendices_ids)
            ->get()
            ->map(function ($asignacion) {
                return [
                    'actividad_id' => $asignacion->actividad_id,
                    'descripcion' => $asignacion->actividad->descripcion,
                    'fecha_entrega' => $asignacion->actividad->fecha_entrega,
                    'criterios_valoracion' => $asignacion->actividad->criterios_valoracion,
                    'aprendiz_id' => $asignacion->aprendiz_id,
                    'aprendiz_nombre' => $asignacion->aprendiz->name,
                    'aprendiz_email' => $asignacion->aprendiz->email,
                ];
            });

        return response()->json($asignaciones);
    }

    /**
     * Devuelve las actividades asignadas al aprendiz autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function actividadesAsignadasAprendiz()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'aprendiz') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $asignaciones = ActividadAprendiz::with('actividad')
            ->where('aprendiz_id', $user->id)
            ->get()
            ->map(function ($asignacion) {
                return [
                    'actividad_id' => $asignacion->actividad_id,
                    'descripcion' => $asignacion->actividad->descripcion,
                    'fecha_entrega' => $asignacion->actividad->fecha_entrega,
                    'criterios_valoracion' => $asignacion->actividad->criterios_valoracion,
                ];
            });

        return response()->json($asignaciones);
    }
}
