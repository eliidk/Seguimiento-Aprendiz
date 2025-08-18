<?php

namespace App\Http\Controllers;

use App\Models\Ficha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FichaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            // Admin ve todas las fichas
            $fichas = Ficha::all();
        } else {
            // Instructor ve solo sus fichas
            $fichas = Ficha::select("fichas.*")
                ->join("ficha_instructor", "fichas.id", "=", "ficha_instructor.ficha_id")
                ->where('ficha_instructor.instructor_id', $user->id)
                ->get();
        }
        return response()->json($fichas);
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
        $data = $request->validate([
        'nombre'         => ['required','string','max:120'],
        'numero_ficha'   => ['required','string','max:50','unique:fichas,numero_ficha'],
        'duracion_meses' => ['required','integer','min:1','max:255'],
        ]);
        $ficha = Ficha::create($data);
    return response()->json($ficha, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ficha  $ficha
     * @return \Illuminate\Http\Response
     */
    public function show(Ficha $ficha)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ficha  $ficha
     * @return \Illuminate\Http\Response
     */
    public function edit(Ficha $ficha)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ficha  $ficha
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ficha $ficha)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ficha  $ficha
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ficha $ficha)
    {
        //
    }
    /**
     * Asigna una ficha a un instructor.
     * Espera: ficha_id, instructor_id (POST)
     */
    public function asignarFichaInstructor(Request $request)
    {
        $request->validate([
            'ficha_id' => 'required|exists:fichas,id',
            'instructor_id' => 'required|exists:users,id',
        ]);

        // Evitar duplicados
        $existe = \App\Models\FichaInstructor::where('ficha_id', $request->ficha_id)
            ->where('instructor_id', $request->instructor_id)
            ->first();
        if ($existe) {
            return response()->json(['message' => 'Ya existe la asignación.'], 409);
        }

        $fichaInstructor = \App\Models\FichaInstructor::create([
            'ficha_id' => $request->ficha_id,
            'instructor_id' => $request->instructor_id,
        ]);

        return response()->json(['message' => 'Ficha asignada correctamente', 'data' => $fichaInstructor], 201);
    }
}
