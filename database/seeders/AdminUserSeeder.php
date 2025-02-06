<?php
namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Administrador',
            'cedula_identidad' => '99999999',  // Puedes usar un valor único
            'nombre_usuario' => 'admin',
            'password' => Hash::make('admin123'), // Cambia por una contraseña segura
            'telefono' => '123456789', // Opcional
            'rol' => 'planificador',  // Definir el rol de administrador
            'area_id' => null,
        ]);
    }
}



