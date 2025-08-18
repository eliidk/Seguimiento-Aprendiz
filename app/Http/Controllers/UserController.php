<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Matricula;
use App\Models\Ficha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Crea un usuario con rol aprendiz y lo matricula en una ficha.
     */
    public function registrarAprendiz(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'ficha_id' => 'required|exists:fichas,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear usuario con rol aprendiz
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'aprendiz';
        $user->save();

        // Validar que no exista matrícula duplicada
        $existe = Matricula::where('ficha_id', $request->ficha_id)
            ->where('aprendiz_id', $user->id)
            ->exists();
        if ($existe) {
            return response()->json(['error' => 'El aprendiz ya está matriculado en esta ficha.'], 409);
        }

        // Crear matrícula
        $matricula = Matricula::create([
            'ficha_id' => $request->ficha_id,
            'aprendiz_id' => $user->id,
            'estado' => 'activa',
        ]);

        return response()->json([
            'user' => $user,
            'matricula' => $matricula,
        ], 201);
    }
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
        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'user' => $usuario
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if (!$user || !Hash::check($request['password'], $user->password)) {
            return response()->json([
                'message' => 'Acceso denegado'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Bienvenido ' . $user->name,
            'user' => $user,
            'token' => $token
        ], 201);
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
        $usuario = User::findOrFail($id);
        if ($request->has('name')) {
            $usuario->name = $request->name;
        }
        if ($request->has('email')) {
            $usuario->email = $request->email;
        }
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        if ($request->has('role')) {
            $usuario->role = $request->role;
        }
        $usuario->save();
        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user' => $usuario
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }
        $usuario->delete();
        return response()->json([
            'message' => 'Usuario eliminado exitosamente'
        ], 200);
    }
    /**
     * Mostrar los instructores.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showInstructors()
    {
        $instructors = User::where('role', 'Instructor')->get();
        return response()->json($instructors);
    }

    /**
     * Permite al aprendiz cambiar su contraseña.
     */
    public function cambiarContrasena(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'current_password' => 'required|string',
        'password' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Verifica si la contraseña actual coincide
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['error' => 'La contraseña actual no coincide.'], 403);
    }

    // Actualiza la contraseña
    $user->password = Hash::make($request->password);
    $user->save();

    return response()->json([
        'message' => 'Contraseña actualizada correctamente',
    ], 200);
}

    /**
     * Permite al aprendiz subir o actualizar su foto de perfil.
     */
    /* public function actualizarFotoPerfil(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|max:2048', // Máx 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nombreFoto = uniqid('perfil_') . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/fotos_perfil', $nombreFoto);

            $user->update([
                'foto' => 'storage/fotos_perfil/' . $nombreFoto
            ]);

            return response()->json([
                'message' => 'Foto de perfil actualizada correctamente',
                'user' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => 'No se envió ninguna foto.'
            ], 400);
        }
    } */

    /**
     * Cierra la sesión del usuario autenticado (revoca el token actual).
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            // Revoca solo el token actual
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Sesión cerrada correctamente'
            ], 200);
        }
        return response()->json([
            'message' => 'No hay usuario autenticado'
        ], 401);
    }
}
