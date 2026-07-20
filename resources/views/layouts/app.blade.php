<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Clínica')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>

        html, body{
            height:100%;
        }

        /* ====== FONDO GENERAL ====== */

        body{
            background:#eef2f6; /* gris suave elegante */
            font-family:'Poppins','Segoe UI',system-ui,sans-serif;
            margin:0;
        }



        /* ====== CONTENIDO ====== */

        .container{
            padding-top:40px;
            padding-bottom:60px;
        }

        /* ====== TARJETAS ====== */

        .card-menu{
            --hover-color:#1f2937;
            border:none;
            border-radius:18px;
            background:white;
            box-shadow:0 10px 25px rgba(0,0,0,0.06);
            transition:all .35s ease;
            position:relative;
            overflow:hidden;
        }

        .card-menu:hover{
            transform:translateY(-10px);
            box-shadow:0 20px 40px rgba(0,0,0,0.12);
        }

        .card-menu::before{
            content:"";
            position:absolute;
            top:0;
            left:0;
            height:5px;
            width:100%;
            background:linear-gradient(90deg,var(--hover-color),#ffffff);
            opacity:0;
            transition:opacity .3s ease;
        }

        .card-menu:hover::before{
            opacity:1;
        }

        .card-menu h1{
            font-size:3rem;
            transition:transform .3s ease,color .3s ease;
        }

        .card-menu h4{
            font-weight:600;
            color:#1f2937;
        }

        .card-menu p{
            font-size:.95rem;
            color:#6b7280;
        }

        .card-menu:hover h1,
        .card-menu:hover h4,
        .card-menu:hover p{
            color:var(--hover-color);
        }

        .card-morado{ --hover-color:#6f42c1; }
        .card-azul{ --hover-color:#0d6efd; }

    </style>

</head>

<body>



<div class="container">

    @yield('content')

</div>

</body>
</html>