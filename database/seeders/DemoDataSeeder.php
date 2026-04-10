<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Enums\RolUsuarioEnum;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\CategoriaPremio;
use App\Models\Premio;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear PATROCINADOR (Vendedor)
        $patrocinador = User::create([
            'nombre_completo' => 'Patrocinador Master',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'codigo_referido' => 'REF-MASTER',
            'dni' => '12345678',
            'activo' => true,
            'rol' => RolUsuarioEnum::ADMIN
        ]);

        // 2. Crear CLIENTE (Referido)
        $cliente = User::create([
            'nombre_completo' => 'Cliente Comprador',
            'email' => 'cliente@test.com',
            'password' => Hash::make('password123'),
            'codigo_referido' => 'REF-CLIENTE',
            'patrocinador_id' => $patrocinador->id,
            'dni' => '87654321',
            'activo' => true,
            'rol' => RolUsuarioEnum::VENDEDOR
        ]);

        // 3. Categoría y Productos
        $cat = Categoria::create(['nombre' => 'Tecnología', 'descripcion' => 'Gadgets', 'activa' => true]);

        Producto::create([
            'nombre' => 'Laptop Gamer',
            'descripcion' => 'Alta potencia',
            'precio' => 1500.00,
            'stock' => 10,
            'puntos' => 100, // Gana 100 puntos por compra
            'categoria_id' => $cat->id,
            'activo' => true
        ]);

        Producto::create([
            'nombre' => 'Mouse RGB',
            'descripcion' => 'Luces led',
            'precio' => 50.00,
            'stock' => 50,
            'puntos' => 10, // Gana 10 puntos por compra
            'categoria_id' => $cat->id,
            'activo' => true
        ]);

        // 4. Premios para canjear
        $catPremio = CategoriaPremio::create(['nombre' => 'Merchandising', 'descripcion' => 'Cosas gratis']);

        Premio::create([
            'nombre' => 'Gorra Oficial',
            'puntos_requeridos' => 50, // Cuesta 50 puntos
            'stock' => 20,
            'categoria_premio_id' => $catPremio->id,
            'disponible' => true
        ]);
    }
}