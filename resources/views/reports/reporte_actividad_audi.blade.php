<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual de Actividades de Auditorio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Reporte de Actividades de Auditorio - Mes: {{ $mes }} / {{ $year }}</h2>

    <table>
        <thead>
            <tr>
                <th>Unidad/Área</th>
                <th>Código POA</th>
                <th>Operación</th>
                <th>Fecha</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>lugar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($actividades as $actividad)
            <tr>
                <td>{{ $actividad->usuario->rol == 'responsable_area' ? $actividad->usuario->area->nombre : $actividad->usuario->unidad->nombre }}</td>
                <td>{{ $actividad->poa->codigo_poa }}</td>
                <td>{{ optional($actividad->poa->operaciones->find($actividad->detalle_operacion))->descripcion }}</td>
                <td>{{ $actividad->fecha }}</td>
                <td>{{ $actividad->hora_inicio }}</td>
                <td>{{ $actividad->hora_fin }}</td>
                <td>{{ $actividad->plataforma }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
