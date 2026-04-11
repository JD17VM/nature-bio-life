<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesBase64Files
{
    /**
     * Decodifica una cadena base64 y la guarda como archivo físico.
     *
     * @param string $base64String La cadena base64 (ej: data:image/png;base64,...)
     * @param string $folder Carpeta dentro de 'public' donde se guardará.
     * @return string|null La ruta relativa del archivo guardado o null si falla.
     */
    public function saveBase64File(string $base64String, string $folder): ?string
    {
        try {
            // 1. Extraer la información del formato y la data
            // Formato esperado: data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...
            if (!preg_match('/^data:(\w+\/[-+.\w]+);base64,(.+)$/', $base64String, $matches)) {
                return null;
            }

            $mimeType = $matches[1]; // ej: image/png
            $data = base64_decode($matches[2]);

            // 2. Determinar la extensión sugerida
            $extension = explode('/', $mimeType)[1] ?? 'bin';
            
            // Caso especial para algunos mimes conocidos si es necesario
            $extensionsMap = [
                'jpeg' => 'jpg',
                'plain' => 'txt',
                'vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                'pdf' => 'pdf'
            ];
            
            $extension = $extensionsMap[$extension] ?? $extension;

            // 3. Generar nombre único
            $fileName = Str::random(40) . '.' . $extension;
            $fullPath = $folder . '/' . $fileName;

            // 4. Guardar en el disco público
            Storage::disk('public')->put($fullPath, $data);

            return $fullPath;
        } catch (\Exception $e) {
            \Log::error("Error decodificando Base64: " . $e->getMessage());
            return null;
        }
    }
}
