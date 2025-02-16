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
            background-color: #d4edda; /* Verde claro para la cabecera */
        }
        /* Estilo para los logos */
        .logo {
            position: absolute;
            top: 10px;
            width: 75px;
        }
        #logo1 {
            left: 50px;
        }
        #logo2 {
            right: 10px;
        }
        h2 {
            text-align: center;
        }
        td.fecha {
            width: 70px;
            text-align: center;
        }
        /* Estilo para el mensaje de "NO CUENTA CON ACTIVIDADES PARA ESTE MES" */
        .sin-actividades {
            color: red;
        }
        /* Estilo para las áreas */
        .area {
            color: #808080; /* Gris */
            padding-left: 20px;
        }
        .area::before {
            content: "- "; /* Agrega un guion antes del nombre del área */
        }
        /* Estilo para el área de firmas */
        .firma {
            border: 2px solid black;
        }
        #firma-izquierda {
            text-align: left;
            margin-top: 90px;
            margin-left: 15%;
        }
        #firma-derecha {
            text-align: right;
            margin-top: -40px;
            margin-right: 15%;
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
            @foreach($unidades as $unidad)
                @php
                    $actividadesUnidad = $actividades->filter(function ($actividad) use ($unidad) {
                        return $actividad->usuario->rol == 'responsable_unidad' && $actividad->usuario->unidad_id == $unidad->id;
                    });
                @endphp
                <tr>
                    <td colspan="7"><strong>{{ $unidad->nombre }} 
                        @if($actividadesUnidad->isEmpty()) <span class="sin-actividades">- NO CUENTA CON ACTIVIDADES PARA ESTE MES</span> @endif
                    </strong></td>
                </tr>
                @foreach($actividadesUnidad as $actividad)
                    <tr>
                        <td>{{ $unidad->nombre }}</td>
                        <td>{{ $actividad->poa->codigo_poa }}</td>
                        <td>{{ optional($actividad->poa->operaciones->find($actividad->detalle_operacion))->descripcion }}</td>
                        <td class="fecha">{{ \Carbon\Carbon::parse($actividad->fecha_inicio)->format('d-m-Y') }}</td>
                        <td class="fecha">{{ \Carbon\Carbon::parse($actividad->fecha_fin)->format('d-m-Y') }}</td>
                        <td>{{ $actividad->municipio->nombre }}</td>
                        <td>{{ $actividad->lugar }}</td>
                    </tr>
                @endforeach
        
                @foreach($unidad->areas as $area)
                    @php
                        $actividadesArea = $actividades->filter(function ($actividad) use ($area) {
                            return $actividad->usuario->rol == 'responsable_area' && $actividad->usuario->area_id == $area->id;
                        });
                    @endphp
                    <tr>
                        <td colspan="7" class="area"><strong>{{ $area->nombre }} 
                            @if($actividadesArea->isEmpty()) <span class="sin-actividades">- NO CUENTA CON ACTIVIDADES PARA ESTE MES</span> @endif
                        </strong></td>
                    </tr>
                    @foreach($actividadesArea as $actividad)
                        <tr>
                            <td>{{ $area->nombre }}</td>
                            <td>{{ $actividad->poa->codigo_poa }}</td>
                            <td>{{ optional($actividad->poa->operaciones->find($actividad->detalle_operacion))->descripcion }}</td>
                            <td class="fecha">{{ \Carbon\Carbon::parse($actividad->fecha_inicio)->format('d-m-Y') }}</td>
                            <td class="fecha">{{ \Carbon\Carbon::parse($actividad->fecha_fin)->format('d-m-Y') }}</td>
                            <td>{{ $actividad->municipio->nombre }}</td>
                            <td>{{ $actividad->lugar }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
    
    <!-- Área de firmas -->
    <div class="firma">
        <h6 id="firma-izquierda">JEFE DE UNIDAD DE PLANIFICACION Y PROYECTOS</h6>
        <h6 id="firma-derecha">DIRECTOR TECNICO SEDES-POTOSI</h6>
        <h2>Reporte de Actividades Aprobadas sin Vehículo - Mes: {{ $mes }} / {{ $year }}</h2>
    </div>
</body>
</html>
