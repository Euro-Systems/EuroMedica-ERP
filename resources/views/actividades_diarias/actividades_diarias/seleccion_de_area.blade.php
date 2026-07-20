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
        <div>
            <a href="/" class="btn-regresar">
                <i class="bi bi-arrow-left"></i> Regresar
            </a>
        </div>
    </div>



    <!-- Areas cards grid -->
    <div class="areas-grid">
        <!-- Administrativos -->
        <a href="{{ route('actividades.area.select', 1) }}" class="area-card" style="background: #2563eb;">
            <div style="display:flex; flex-direction:column; align-items:center; flex-grow:1;">
                <div class="area-icon">💼</div>
                <h3 class="area-name">Administrativos</h3>
                <p class="area-desc">Gestión y control del personal administrativo, finanzas y operaciones generales.</p>
            </div>
            <div class="btn-entrar" style="color: #2563eb;">Entrar al área</div>
        </a>

        <!-- Sistemas -->
        <a href="{{ route('actividades.area.select', 2) }}" class="area-card" style="background: #0d9488;">
            <div style="display:flex; flex-direction:column; align-items:center; flex-grow:1;">
                <div class="area-icon">💻</div>
                <h3 class="area-name">Sistemas</h3>
                <p class="area-desc">Soporte técnico, desarrollo de software, mantenimiento de infraestructura y base de datos.</p>
            </div>
            <div class="btn-entrar" style="color: #0d9488;">Entrar al área</div>
        </a>

        <!-- Marketing -->
        <a href="{{ route('actividades.area.select', 3) }}" class="area-card" style="background: #e11d48;">
            <div style="display:flex; flex-direction:column; align-items:center; flex-grow:1;">
                <div class="area-icon">📢</div>
                <h3 class="area-name">Marketing</h3>
                <p class="area-desc">Publicidad, diseño gráfico, gestión de redes sociales y estrategias de crecimiento.</p>
            </div>
            <div class="btn-entrar" style="color: #e11d48;">Entrar al área</div>
        </a>

        <!-- Administración de empresas -->
        <a href="{{ route('actividades.area.select', 4) }}" class="area-card" style="background: #6d28d9;">
            <div style="display:flex; flex-direction:column; align-items:center; flex-grow:1;">
                <div class="area-icon">📊</div>
                <h3 class="area-name">Administración de empresas</h3>
                <p class="area-desc">Planeación estratégica, optimización de recursos y coordinación de procesos directivos.</p>
            </div>
            <div class="btn-entrar" style="color: #6d28d9;">Entrar al área</div>
        </a>

        <!-- Análisis de datos -->
        <a href="{{ route('actividades.area.select', 5) }}" class="area-card" style="background: #4f46e5;">
            <div style="display:flex; flex-direction:column; align-items:center; flex-grow:1;">
                <div class="area-icon">📈</div>
                <h3 class="area-name">Análisis de datos</h3>
                <p class="area-desc">Procesamiento de información, modelos estadísticos y análisis de indicadores clave.</p>
            </div>
            <div class="btn-entrar" style="color: #4f46e5;">Entrar al área</div>
        </a>

        <!-- Recursos Humanos -->
        <a href="{{ route('actividades.area.select', 6) }}" class="area-card" style="background: #ea580c;">
            <div style="display:flex; flex-direction:column; align-items:center; flex-grow:1;">
                <div class="area-icon">👥</div>
                <h3 class="area-name">Recursos Humanos</h3>
                <p class="area-desc">Reclutamiento, gestión del talento, capacitación y control de asistencia laboral.</p>
            </div>
            <div class="btn-entrar" style="color: #ea580c;">Entrar al área</div>
        </a>
    </div>
</div>

</body>
</html>
