@php
    setlocale(LC_TIME, 'es_ES.UTF-8');
    use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual de Actividades</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            text-align: center;
            color: #444;
            margin-bottom: 10px;
        }

        .info {
            text-align: left;
            font-size: 1.1em;
            margin-bottom: 20px;
        }
        .usuario-info {
            font-size: 1.2em; /* Ajusta el tamaño según sea necesario */
            font-weight: bold;
            text-align: left;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white; /* Fondo blanco para la tabla */
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
            font-size: 11px;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .no-actividades {
            text-align: center;
            font-style: italic;
            color: #666;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Reporte Personal de ACTIVIDADES CON VEHÍCULO</h2>
    
        <!-- Datos del usuario -->
        <h2 class="usuario-info">{{ $usuario->nombre }} {{ $usuario->apellido }} - CI: {{ $usuario->cedula_identidad }} | {{ $usuario->unidad->nombre }}</h2>
        <!-- Fila con el rol -->
        <p class="info">
            <strong>Rol:</strong> 
            {{ $usuario->rol === 'responsable_area' ? 'Responsable de Área: ' . $usuario->area->nombre : 'Responsable de Unidad: ' . $usuario->unidad->nombre }}
        </p>

        <h3>Actividades Aprobadas en {{ ucfirst(Carbon::parse($anio.'-'.$mes.'-01')->locale('es')->monthName) }}/{{ $anio }}</h3>

    
        @if($actividades->isEmpty())
            <p class="no-actividades">No se encontraron actividades aprobadas para este periodo.</p>
        @else
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código POA</th>
                            <th>Operación</th>
                            <th>Resultado Esperado</th>
                            <th>Municipio</th>
                            <th>Lugar</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actividades as $actividad)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $actividad->poa->codigo_poa }}</td>
                                <td>{{ optional($actividad->poa->operaciones->find($actividad->detalle_operacion))->descripcion }}</td>
                                <td>{{ $actividad->resultados_esperados }}</td>
                                <td>{{ $actividad->centroSalud->municipio->nombre ?? 'N/A' }}</td>
                                <td>{{ $actividad->centroSalud->nombre }}</td>
                                <td>{{ date('d/m/Y', strtotime($actividad->fecha_inicio)) }}</td>
                                <td>{{ date('d/m/Y', strtotime($actividad->fecha_fin)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    

</body>
</html>
