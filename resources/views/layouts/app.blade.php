<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <style>
        .bg-custom { background-color: #f0ece4; }
        body { background-color: #f8f9fa; }
    </style>
    
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <header class="bg-custom py-3 shadow-sm">
        <div class="container">
            <img src="{{ asset('img/logo.svg') }}" alt="Logo da Empresa" height="40">
        </div>
    </header>

    <main class="flex-grow-1 py-5">
        @yield('content')
    </main>

    <footer class="bg-custom py-4 mt-auto border-top">
        <div class="container text-center">
            <span class="text-muted small">
                &copy; {{ date('Y') }} Controle de Produção. Todos os direitos reservados.
            </span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

<style>
        .bg-custom { background-color: #f0ece4; }
        body { background-color: #f8f9fa; }

        /* DESIGN DO BOTÃO GEMINI MULTICOLOR */
        .btn-gemini {
            background: linear-gradient(45deg, #4285F4, #9B51E0, #FF6B6B, #3498db);
            padding: 2px; /* Largura da borda colorida */
            border-radius: 30px;
            border: none;
            display: inline-block;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(155, 81, 224, 0.2);
        }

        .btn-gemini:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(155, 81, 224, 0.35);
        }

        .btn-gemini:active {
            transform: translateY(1px);
        }

        .btn-gemini-content {
            background: #ffffff;
            color: #512da8;
            padding: 8px 24px;
            border-radius: 28px;
            display: block;
            font-weight: bold;
            font-size: 0.95rem;
            transition: background 0.2s;
        }

        .btn-gemini:hover .btn-gemini-content {
            background: #fcfbfa; 
        }
    </style>