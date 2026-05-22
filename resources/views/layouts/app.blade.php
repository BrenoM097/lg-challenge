<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <style>
        :root {
            --lg-red: #C20E1A;
            --lg-gray: #4A4A4A;
            --lg-light-gray: #f5f5f5;
            --lg-border-gray: #e0e0e0;
        }

        .bg-custom { background-color: #f0ece4; }
        body { background-color: #f8f9fa; }

        /* Cores LG */
        .text-lg-red { color: var(--lg-red); }
        .bg-lg-red { background-color: var(--lg-red); }
        .border-lg-red { border-color: var(--lg-red) !important; }
        
        .text-lg-gray { color: var(--lg-gray); }
        .bg-lg-gray { background-color: var(--lg-gray); }

        /* Tabela modernizada */
        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background-color: var(--lg-light-gray);
            color: var(--lg-gray);
            font-weight: 600;
            border: none;
            border-bottom: 2px solid var(--lg-border-gray);
            padding: 14px 16px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            user-select: none;
            transition: background-color 0.2s;
        }

        .table-modern thead th:hover {
            background-color: #eeeeee;
        }

        .table-modern thead th.sortable::after {
            content: ' ↕';
            color: #999;
            font-size: 0.85rem;
        }

        .table-modern thead th.sort-asc::after {
            content: ' ↑';
            color: var(--lg-red);
        }

        .table-modern thead th.sort-desc::after {
            content: ' ↓';
            color: var(--lg-red);
        }

        .table-modern tbody tr {
            border-bottom: 1px solid var(--lg-border-gray);
            transition: background-color 0.15s;
        }

        .table-modern tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .table-modern tbody tr:hover {
            background-color: #f0f0f0;
        }

        .table-modern tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            color: var(--lg-gray);
        }

        .efficiency-badge {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-block;
        }

        .efficiency-excellent {
            background-color: #d4edda;
            color: #155724;
        }

        .efficiency-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .efficiency-critical {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Filtro moderno */
        .filter-form {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .form-group-modern {
            flex: 1;
        }

        .form-group-modern label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--lg-gray);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control-modern {
            border: none;
            border-bottom: 2px solid var(--lg-border-gray);
            border-radius: 0;
            background-color: transparent;
            color: var(--lg-gray);
            padding: 8px 0;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-control-modern:focus {
            border-color: var(--lg-red);
            box-shadow: none;
            background-color: transparent;
            color: var(--lg-gray);
        }

        .form-control-modern::placeholder {
            color: #999;
        }

        /* Botão moderno */
        .btn-modern {
            background-color: var(--lg-red);
            color: white;
            border: none;
            padding: 10px 28px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-modern:hover {
            background-color: #a60c16;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(194, 14, 26, 0.2);
            color: white;
        }

        .btn-modern:active {
            transform: translateY(0);
        }

        .btn-modern:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .pagination {
            gap: 4px;
        }

        .page-link {
            color: var(--lg-gray);
            border: 1px solid var(--lg-border-gray);
            border-radius: 4px;
            margin: 0 2px;
        }

        .page-link:hover {
            background-color: var(--lg-light-gray);
            color: var(--lg-red);
        }

        .page-item.active .page-link {
            background-color: var(--lg-red);
            border-color: var(--lg-red);
            color: white;
        }

        .page-item.disabled .page-link {
            color: #ccc;
            background-color: transparent;
        }

        /* Modal moderno */
        .modal-header-lg {
            background-color: var(--lg-red);
            color: white;
        }

        .alert-api-key {
            border-left: 4px solid var(--lg-red);
            background-color: #fff5f5;
            color: var(--lg-red);
        }

        /* DESIGN DO BOTÃO GEMINI MULTICOLOR */
        .btn-gemini {
            background: linear-gradient(45deg, var(--lg-red), #9B51E0, #FF6B6B, #3498db);
            padding: 2px;
            border-radius: 30px;
            border: none;
            display: inline-block;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(194, 14, 26, 0.2);
        }

        .btn-gemini:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(194, 14, 26, 0.3);
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

        .btn-gemini:disabled .btn-gemini-content {
            color: #999;
            cursor: not-allowed;
        }
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