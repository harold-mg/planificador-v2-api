<?php
namespace App\Http\Controllers\ApiExcel;

use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;

class ExcelDataController extends Controller
{
    public function obtenerDatosDesdeExcel()
    {
        // Ruta del archivo Excel (asegúrate de que el archivo esté en esta ubicación o ajusta el camino)
        //$filePath = storage_path('app/public/tasa-mortalidad-100mil-habitantes-2007-1.xlsx');
        $filePath = storage_path('app/public/tasa-mortalidad-100mil-habitantes-2007-1.xlsx');

        // Cargar el archivo de Excel
        $spreadsheet = IOFactory::load($filePath);
        
        // Seleccionar la primera hoja de cálculo
        $sheet = $spreadsheet->getActiveSheet();
        
        // Obtener los datos de la hoja como array
        $resultados = [];
        foreach ($sheet->getRowIterator() as $index => $row) {
            // Ignorar la primera fila si contiene encabezados
            if ($index === 1) continue;

            // Obtener celdas de cada fila
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            $fila = [];
            foreach ($cellIterator as $cell) {
                $fila[] = $cell->getValue();
            }

            // Agregar los datos de la fila al array de resultados
            $resultados[] = [
                'id' => $fila[0],
                'municipio' => $fila[1],
                'total_casos' => $fila[2],
                'poblacion' => $fila[3],
                'tgm' => $fila[4],
            ];
        }

        // Retornar los datos en formato JSON
        return response()->json($resultados);
    }
}
