<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Area;
use App\Models\Unidad;

class AuthController extends Controller
{
    // Método para registrar usuarios
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula_identidad' => 'required|string|max:255|unique:usuarios',
            'nombre_usuario' => 'required|string|max:255|unique:usuarios',
            'password' => 'required|string|min:6|confirmed',  // Confirmar que las contraseñas coincidan
            'rol' => 'required|string|in:responsable_area,responsable_unidad,planificador',
            'area_id' => 'nullable|exists:areas,id',
            'unidad_id' => 'nullable|exists:unidades,id',
        ]);

        $usuario = Usuario::create([
            'nombre' => $validatedData['nombre'],
            'apellido' => $validatedData['apellido'],
            'cedula_identidad' => $validatedData['cedula_identidad'],
            'nombre_usuario' => $validatedData['nombre_usuario'],
            'password' => Hash::make($validatedData['password']),
            'rol' => $validatedData['rol'], // Usar el rol especificado en la solicitud
            //'rol' => 'responsable_area', // Por defecto, asignar un rol (ajusta según tus necesidades)
            'area_id' => $validatedData['area_id'],
            'unidad_id' => $validatedData['unidad_id'],
        ]);

        // Crear un token para el usuario
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // Método para iniciar sesión
    public function login(Request $request)
    {
        $credentials = $request->only('nombre_usuario', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $usuario = Auth::user();
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // Método para cerrar sesión
    public function logout(Request $request) {
        $user = $request->user();
        $user->currentAccessToken()->delete();
    
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
    

    // Método para obtener el usuario autenticado
    /* public function me()
    {
        return response()->json(Auth::user());
    } */
    public function user()
    {
        return response()->json(auth()->user());
    }
    
    // En AuthController o UsuarioController
    public function getAllUsers()
    {
        // Obtener todos los usuarios de la base de datos con sus relaciones
        $usuarios = Usuario::with(['area', 'unidad'])->get();
    
        // Mapear los usuarios para agregar la información de área o unidad
        $usuariosConRelacion = $usuarios->map(function ($usuario) {
            $lugar = null;
    
            // Determina si el usuario tiene un área asignada
            if ($usuario->area) {
                $lugar = $usuario->area->nombre;  // Asumiendo que 'nombre' es el campo en la tabla 'areas'
            } elseif ($usuario->unidad) {
                $lugar = $usuario->unidad->nombre; // Asumiendo que 'nombre' es el campo en la tabla 'unidades'
            }
    
            // Agregar el campo 'lugar' al usuario
            $usuario->lugar = $lugar;
    
            return $usuario;
        });
    
        // Retornar los usuarios con la nueva propiedad 'lugar'
        return response()->json($usuariosConRelacion);
    }
    // Método para actualizar un usuario
    public function updateUsuario(Request $request, $id)
    {
        // Validar los datos que llegan en la solicitud
        $validatedData = $request->validate([
            'nombre' => 'nullable|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'cedula_identidad' => 'nullable|string|max:255|unique:usuarios,cedula_identidad,' . $id,
            'nombre_usuario' => 'nullable|string|max:255|unique:usuarios,nombre_usuario,' . $id,
            'password' => 'nullable|string|min:6|confirmed',  // Si quieres permitir la actualización de la contraseña
            'rol' => 'nullable|string|in:responsable_area,responsable_unidad,planificador',
            'area_id' => 'nullable|exists:areas,id',
            'unidad_id' => 'nullable|exists:unidades,id',
        ]);

        // Buscar el usuario por su ID
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Actualizar solo los campos que fueron enviados
        $usuario->update($validatedData);

        // Si se actualiza la contraseña, también debes actualizarla
        if ($request->has('password')) {
            $usuario->password = Hash::make($request->input('password'));
            $usuario->save();
        }

        return response()->json($usuario);
    }
    // Método para obtener un usuario por su ID
    public function getUsuario($id)
    {
        // Buscar el usuario por su ID
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }

}

