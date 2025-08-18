<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Matricula;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
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
    public function create(Request $request)
    {
        $request->validate([
        'matricula_id'  => 'required|exists:matriculas,id',
        'instructor_id' => 'required|exists:users,id',
        'estado'        => 'required|in:Presente,Ausente,Justificado,Tarde',
        'nota'          => 'nullable|string',
    ]);

    // Busca si ya existe una asistencia para hoy
    $asistencia = Asistencia::where('matricula_id', $request->matricula_id)
        ->where('instructor_id', $request->instructor_id)
        ->whereDate('created_at', now()->toDateString())
        ->first();

    if ($asistencia) {
        // Actualiza si ya existe
        $asistencia->estado = $request->estado;
        $asistencia->nota = $request->nota;
        $asistencia->save();
    } else {
        // Crea nueva asistencia
        $asistencia = Asistencia::create([
            'matricula_id'  => $request->matricula_id,
            'instructor_id' => $request->instructor_id,
            'estado'        => $request->estado,
            'nota'          => $request->nota,
        ]);
    }

    return response()->json([
        'success'    => true,
        'asistencia' => $asistencia
    ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ficha_id)
    {
        $aprendices = \App\Models\User::where('role', 'aprendiz')
            ->whereIn('id', function($query) use ($ficha_id) {
                $query->select('aprendiz_id')
                    ->from('matriculas')
                    ->where('ficha_id', $ficha_id)
                    ->where('estado', 'activa');
            })
            ->get()
            ->map(function($aprendiz) use ($ficha_id) {
                $matricula = \App\Models\Matricula::where('aprendiz_id', $aprendiz->id)
                    ->where('ficha_id', $ficha_id)
                    ->where('estado', 'activa')
                    ->first();

                $asistenciaHoy = null;
                if ($matricula) {
                    $asistenciaHoy = \App\Models\Asistencia::where('matricula_id', $matricula->id)
                        ->whereDate('created_at', now()->toDateString())
                        ->first();
                }

                $aprendiz->matricula_id = $matricula ? $matricula->id : null;
                $aprendiz->asistencia_tomada = $asistenciaHoy ? true : false;
                $aprendiz->estado_asistencia = $asistenciaHoy ? $asistenciaHoy->estado : null;

                return $aprendiz;
            });

        return response()->json($aprendices);
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
}
