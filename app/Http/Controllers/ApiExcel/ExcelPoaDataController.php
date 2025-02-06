<?php

namespace App\Http\Controllers\ApiExcel;

use App\Http\Controllers\Controller;
use App\Models\Operacion;
use App\Models\Poa;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Unidad;

class ExcelPoaDataController extends Controller
{
    public function showImportForm()
    {
        // Obtener las unidades de la base de datos para mostrar en el formulario
        $unidades = Unidad::all();
        return view('imports.import_poa', compact('unidades'));
    }

    public function importExcel(Request $request)
    {
        // Validar si se subió un archivo
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
    
        // Obtener el archivo Excel
        $file = $request->file('file');
    
        // Procesar el archivo Excel
        $formattedData = $this->processExcel($file);
    
        // Obtener las unidades y áreas de la base de datos
        $unidades = Unidad::all();
    
        // Pasar los datos a la vista como tabla
        return view('imports.import_poa', compact('formattedData', 'unidades'));
    }

    private function processExcel($file)
    {
        // Aquí procesas el archivo Excel como ya lo tienes
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $formattedData = [];
        $lastCodigoPoa = '';
        $lastAccionCortoPlazo = '';

        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            $data[0] = $data[0] ?: $lastCodigoPoa;
            $data[1] = $data[1] ?: $lastAccionCortoPlazo;

            $lastCodigoPoa = $data[0];
            $lastAccionCortoPlazo = $data[1];

            $formattedData[] = [
                'codigo_poa' => $data[0] ?? '',
                'accion_corto_plazo' => $data[1] ?? '',
                'descripcion' => $data[2] ?? ''
            ];
        }

        return $formattedData;
    }
    public function storePoaData(Request $request)
    {
        // Verifica los datos que llegan
        dd($request->all());
        // Validar la entrada del formulario
        $request->validate([
            'anio' => 'required|integer|min:2000|max:' . date('Y'),
            'unidad_id' => 'required|exists:unidades,id',
            'data' => 'required|array',
            'data.*.codigo_poa' => 'required|string',
            'data.*.accion_corto_plazo' => 'nullable|string',
            'data.*.descripcion' => 'nullable|string',
        ]);
    
        foreach ($request->data as $item) {
            // Asegurarse de que la unidad exista
            $unidad = Unidad::find($request->unidad_id);
            if (!$unidad) {
                return back()->with('error', 'Unidad no encontrada');
            }
    
            // Crear o encontrar el POA
            $poa = Poa::firstOrCreate([
                'codigo_poa' => $item['codigo_poa'],
                'anio' => $request->anio,
                'unidad_id' => $request->unidad_id,
            ]);
    
            // Crear la operación asociada al POA
            Operacion::create([
                'poa_id' => $poa->id,
                'accion_corto_plazo' => $item['accion_corto_plazo'],
                'descripcion' => $item['descripcion'],
            ]);
        }
    
        return redirect()->route('excel.import')->with('success', 'Datos guardados con éxito');
    }
}
