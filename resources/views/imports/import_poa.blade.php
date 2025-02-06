<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar POA desde Excel</title>
</head>
<body>
    <h1>Importar POA desde Excel</h1>
    
    <!-- Formulario para cargar el archivo Excel -->
    <form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label for="unidad_id">Selecciona una Unidad:</label>
    <select name="unidad_id" id="unidad_id" required>
        <option value="">Seleccione una unidad</option>
        @foreach($unidades as $unidad)
            <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
        @endforeach
    </select>
    <br><br>

    <label for="anio">Selecciona un Año:</label>
    <input type="number" name="anio" id="anio" required min="2000" max="{{ date('Y') }}">
    <br><br>

    <label for="file">Selecciona un archivo Excel:</label>
    <input type="file" name="file" id="file" required>
    <br><br>

    <button type="submit">Subir y Mostrar Datos</button>
</form>

    
    
    <!-- Mostrar datos del archivo si los hay -->
    @if(isset($formattedData) && count($formattedData) > 0)
    <h2>Datos del Archivo</h2>
    <form action="{{ route('excel.save') }}" method="POST">
    @csrf
    <input type="hidden" name="anio" value="{{ old('anio') }}">
    <input type="hidden" name="unidad_id" value="{{ old('unidad_id') }}">
    
    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>Código POA</th>
                <th>Acción Corto Plazo</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formattedData as $data)
                <tr>
                    <td>
                        <input type="text" name="data[{{ $loop->index }}][codigo_poa]" value="{{ $data['codigo_poa'] }}">
                    </td>
                    <td>
                        <input type="text" name="data[{{ $loop->index }}][accion_corto_plazo]" value="{{ $data['accion_corto_plazo'] }}">
                    </td>
                    <td>
                        <input type="text" name="data[{{ $loop->index }}][descripcion]" value="{{ $data['descripcion'] }}">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <button type="submit">Guardar Datos</button>
</form>

    @endif
</body>
</html>
