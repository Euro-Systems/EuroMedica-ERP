<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Áreas de la Empresa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background: #f1f5f9;
            font-family: 'Poppins', 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            padding: 50px 20px;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        .header-title {
            font-size: 32px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .header-subtitle {
            font-size: 15px;
            color: #64748b;
            margin: 5px 0 0 0;
        }
        .btn-regresar {
            background: #ffffff;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            padding: 8px 24px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        .btn-regresar:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        .areas-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .area-card {
            border-radius: 16px;
            padding: 35px 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 340px;
            text-align: center;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: #ffffff;
        }
        .area-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.12), 0 10px 10px -5px rgba(0,0,0,0.06);
            color: #ffffff;
        }
        .area-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .area-name {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }
        .area-desc {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.5;
            margin: 0 0 24px 0;
            flex-grow: 1;
        }
        .btn-entrar {
            width: 100%;
            background: #ffffff;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-entrar:hover {
            background: #f8fafc;
        }
        .btn-ver-todas-areas {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border: 1.5px solid #334155;
            padding: 9px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13.5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(15,23,42,0.2);
            transition: all 0.25s ease;
        }
        .btn-ver-todas-areas:hover {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #ffffff;
            border-color: #38bdf8;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(56,189,248,0.25);
        }
    </style>
</head>
<body>

<div class="container" style="max-width: 1200px; margin: 0 auto;">
    <!-- Title and back button -->
    <div class="header-container">
        <div>
            <h1 class="header-title">Áreas de la Empresa</h1>
            <p class="header-subtitle">Gestione las actividades, flujos de trabajo y personal por departamento</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <!-- BOTÓN DE TODAS LAS ÁREAS ACTUALIZADO -->
            <a href="{{ route('actividades.area.select', 'todas') }}" class="btn-ver-todas-areas">
                <i class="bi bi-globe-americas" style="font-size: 16px; color: #38bdf8;"></i>
                <span>Ver Todas las Áreas!</span>
            </a>
            <a href="{{ url('/') }}" class="btn-regresar">
                <i class="bi bi-arrow-left"></i> Volver al Inicio
            </a>
        </div>
    </div>



    <!-- Areas cards grid -->
    <div class="areas-grid">
        @php
            $areaColors = [
                'administrativos' => ['color' => '#2563eb', 'icon' => '💼', 'desc' => 'Gestión y control del personal administrativo, finanzas y operaciones generales.'],
                'sistemas' => ['color' => '#0d9488', 'icon' => '💻', 'desc' => 'Soporte técnico, desarrollo de software, mantenimiento de infraestructura y base de datos.'],
                'marketing' => ['color' => '#e11d48', 'icon' => '📢', 'desc' => 'Publicidad, diseño gráfico, gestión de redes sociales y estrategias de crecimiento.'],
                'mkt' => ['color' => '#e11d48', 'icon' => '📢', 'desc' => 'Publicidad, diseño gráfico, gestión de redes sociales y estrategias de crecimiento.'],
                'administración de empresas' => ['color' => '#6d28d9', 'icon' => '📊', 'desc' => 'Planeación estratégica, optimización de recursos y coordinación de procesos directivos.'],
                'análisis de datos' => ['color' => '#4f46e5', 'icon' => '📈', 'desc' => 'Procesamiento de información, modelos estadísticos y análisis de indicadores clave.'],
                'recursos humanos' => ['color' => '#ea580c', 'icon' => '👥', 'desc' => 'Reclutamiento, gestión del talento, capacitación y control de asistencia laboral.'],
                'nómina' => ['color' => '#059669', 'icon' => '💵', 'desc' => 'Cálculo de sueldos, percepciones, deducciones y pagos del personal.'],
                'enfermería' => ['color' => '#dc2626', 'icon' => '🩺', 'desc' => 'Atención médica, cuidado de pacientes y gestión de salud ocupacional.'],
                'add' => ['color' => '#0284c7', 'icon' => '📂', 'desc' => 'Administración y procesamiento digital de documentos.'],
                'ade' => ['color' => '#7c3aed', 'icon' => '🏢', 'desc' => 'Administración de la estructura empresarial y procesos internos.'],
                'operaciones' => ['color' => '#d97706', 'icon' => '⚙️', 'desc' => 'Coordinación, ejecución y supervisión de las operaciones operativas diarias.']
            ];
        @endphp

        @foreach($areas as $area)
            @if(Auth::user()->canViewArea($area->id))
                @php
                    $key = strtolower(trim($area->nombre));
                    $meta = $areaColors[$key] ?? ['color' => '#3b82f6', 'icon' => '🏢', 'desc' => 'Gestión de actividades y flujo de trabajo del área ' . $area->nombre];
                @endphp
                <a href="{{ route('actividades.area.select', $area->id) }}" class="area-card" style="background: {{ $meta['color'] }};">
                    <div style="display:flex; flex-direction:column; align-items:center; flex-grow:1;">
                        <div class="area-icon">{{ $meta['icon'] }}</div>
                        <h3 class="area-name">{{ $area->nombre }}</h3>
                        <p class="area-desc">{{ $meta['desc'] }}</p>
                    </div>
                    <div class="btn-entrar" style="color: {{ $meta['color'] }};">Entrar al área</div>
                </a>
            @endif
        @endforeach
    </div>
</div>

</body>
</html>
