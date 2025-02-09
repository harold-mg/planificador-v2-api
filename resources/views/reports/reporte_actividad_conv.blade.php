<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin-top: 60px;
            margin-left: 50px;
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
        /* Estilo para los logos */
        .logo {
            position: absolute;
            top: 10px;
            width: 75px;  /* Ajusta el tamaño del logo */
        }
        #logo1 {
            left: 50px;  /* Alineación a la izquierda */
        }
        #logo2 {
            right: 10px;  /* Alineación a la derecha */
        }
        /* Centrar el h2 */
        h2 {
            text-align: center;
        }
        /* Ajustar tamaño fijo para las celdas de fechas */
        td.fecha {
            width: 70px;  /* Ajusta el tamaño de las celdas de fecha */
            text-align: center;
        }
    </style>
</head>
<body>
    <img id="logo1" src="{{ public_path('images/LOGO-SEDES-ICONO.png') }}" alt="Logo SEDES" class="logo">
    <img id="logo2" src="{{ public_path('images/logo-gob-dep-potosi.png') }}" alt="Logo Gobierno Potosí" class="logo">

    <h2>Reporte de Actividades Aprobadas con Vehículo - Mes: {{ $mes }} / {{ $year }}</h2>

    <table>
        <thead>
            <tr>
                <th>Unidad/Área</th>
                <th>Código POA</th>
                <th>Operación</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Municipio</th>
                <th>Lugar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($actividades as $actividad)
            <tr>
                <td>{{ $actividad->usuario->rol == 'responsable_area' ? $actividad->usuario->area->nombre : $actividad->usuario->unidad->nombre }}</td>
                <td>{{ $actividad->poa->codigo_poa }}</td>
                <td>{{ optional($actividad->poa->operaciones->find($actividad->detalle_operacion))->descripcion }}</td>
                <td class="fecha">{{ \Carbon\Carbon::parse($actividad->fecha_inicio)->format('d-m-Y') }}</td>
                <td class="fecha">{{ \Carbon\Carbon::parse($actividad->fecha_fin)->format('d-m-Y') }}</td>
                <td>{{ $actividad->centroSalud->municipio->nombre }}</td>
                <td>{{ $actividad->centroSalud->nombre }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
