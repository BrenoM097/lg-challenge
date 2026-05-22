@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-5">
            <h1 class="h3 text-lg-gray font-weight-bold mb-1">Dashboard de Produtividade</h1>
            <p class="text-muted mb-0">Período: Janeiro de 2026 | Unidade: Planta A</p>
        </div>
        
        <div class="col-md-7 mt-3 mt-md-0 d-flex justify-content-md-end align-items-center gap-3">
            <button type="button" class="btn-gemini" data-toggle="modal" data-target="#aiSimulationModal">
                <span class="btn-gemini-content">
                    Análise Preditiva
                </span>
            </button>
        </div>
    </div>

    <!-- Filtro Section -->
    <div class="row mb-4">
        <div class="col-12">
            <form method="GET" action="{{ url('/dashboard') }}" class="filter-form">
                <div class="row align-items-end">
    <div class="col-md-2 form-group">
        <label for="year">Ano</label>
        <select name="year" id="year" class="form-control form-control-modern">
            @for($y = 2024; $y <= 2027; $y++)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>
    </div>

    <div class="col-md-2 form-group">
    <label for="monthPicker">Mês</label>
    <select name="month" id="monthPicker" class="form-control form-control-modern">
        <option value="" {{ empty($month) ? 'selected' : '' }}>Todos</option>
        
        <option value="01" {{ $month == '01' ? 'selected' : '' }}>Janeiro</option>
        <option value="02" {{ $month == '02' ? 'selected' : '' }}>Fevereiro</option>
        <option value="03" {{ $month == '03' ? 'selected' : '' }}>Março</option>
        <option value="04" {{ $month == '04' ? 'selected' : '' }}>Abril</option>
        <option value="05" {{ $month == '05' ? 'selected' : '' }}>Maio</option>
        <option value="06" {{ $month == '06' ? 'selected' : '' }}>Junho</option>
        <option value="07" {{ $month == '07' ? 'selected' : '' }}>Julho</option>
        <option value="08" {{ $month == '08' ? 'selected' : '' }}>Agosto</option>
        <option value="09" {{ $month == '09' ? 'selected' : '' }}>Setembro</option>
        <option value="10" {{ $month == '10' ? 'selected' : '' }}>Outubro</option>
        <option value="11" {{ $month == '11' ? 'selected' : '' }}>Novembro</option>
        <option value="12" {{ $month == '12' ? 'selected' : '' }}>Dezembro</option>
    </select>
</div>

    <div class="col-md-3 form-group">
        <label for="product_line">Linha de Produto</label>
        <select name="product_line" id="product_line" class="form-control form-control-modern">
            <option value="">Todas as linhas</option>
            @foreach($availableLines as $line)
                <option value="{{ $line }}" {{ request('product_line') == $line ? 'selected' : '' }}>
                    {{ $line }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 form-group">
        <label>Visualização</label>
        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
            <label class="btn btn-outline-secondary {{ $viewMode === 'detailed' ? 'active' : '' }}" style="flex: 1; padding-left: 0; padding-right: 0;">
                <input type="radio" name="view_mode" value="detailed" {{ $viewMode === 'detailed' ? 'checked' : '' }}>
                Detalhado
            </label>
            <label class="btn btn-outline-secondary {{ $viewMode === 'summary' ? 'active' : '' }}" style="flex: 1; padding-left: 0; padding-right: 0;">
                <input type="radio" name="view_mode" value="summary" {{ $viewMode === 'summary' ? 'checked' : '' }}>
                Por Total
            </label>
        </div>
    </div>

    <div class="col-md-2 form-group">
        <button type="submit" class="btn btn-modern w-100">Filtrar</button>
    </div>
</div>
                
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        @if($viewMode === 'summary')
                            <!-- Tabela de Resumo por Total -->
                            <table class="table table-modern mb-0" id="productivityTable">
                                <thead>
                                    <tr>
                                        <th class="text-left">Linha de Produto</th>
                                        <th class="text-center">Total Produzido</th>
                                        <th class="text-center">Total de Defeitos</th>
                                        <th class="text-center">Eficiência</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productivities as $item)
                                        <tr class="productivity-row-summary">
                                            <td class="font-weight-bold">{{ $item->product_line }}</td>
                                            <td class="text-center produced-value">{{ $item->produced_quantity }}</td>
                                            <td class="text-center defect-value">{{ $item->defect_count }}</td>
                                            <td class="text-center efficiency-value font-weight-bold">
                                                <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                                    <span class="sr-only">Calculando...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    @if($productivities->isEmpty())
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-5">
                                                Nenhum registro encontrado para os filtros selecionados.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        @else
                            <!-- Tabela de Detalhamento -->
                            <table class="table table-modern mb-0" id="productivityTable">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-column="production_date">Data Fab.</th>
                                        <th class="sortable" data-column="product_line">Linha de Produto</th>
                                        <th class="sortable text-center" data-column="produced_quantity">Qtd Produzida</th>
                                        <th class="sortable text-center" data-column="defect_count">Qtd Defeitos</th>
                                        <th class="text-center">Eficiência</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productivities as $item)
                                        <tr class="productivity-row">
                                            <td>{{ date('d/m/Y', strtotime($item->production_date)) }}</td>
                                            <td class="font-weight-bold">{{ $item->product_line }}</td>
                                            <td class="text-center produced-value">{{ $item->produced_quantity }}</td>
                                            <td class="text-center defect-value">{{ $item->defect_count }}</td>
                                            <td class="text-center efficiency-value font-weight-bold">
                                                <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                                    <span class="sr-only">Calculando...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    @if($productivities->isEmpty())
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
                                                Nenhum registro encontrado para os filtros selecionados.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                <!-- Paginação -->
                @if(!$productivities->isEmpty())
                    <div class="card-footer bg-white d-flex justify-content-center border-top">
                        {{ $productivities->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal: Assistente Inteligente -->
<div class="modal fade" id="aiSimulationModal" tabindex="-1" aria-labelledby="aiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header modal-header-lg">
                <h5 class="modal-title font-weight-bold" id="aiModalLabel">Assistente Inteligente</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body p-4">
                <!-- Aviso de validação da API key -->
                <div id="apiKeyAlert" class="alert alert-api-key alert-dismissible fade show" role="alert" style="display: none;">
                    <strong>Configuração Necessária:</strong> A chave da API Gemini não está configurada. Por favor, adicione <code>GEMINI_API_KEY</code> ao arquivo <code>.env</code> para usar a análise preditiva.
                </div>

                <p class="text-muted small">Insira a meta volumétrica desejada. A Inteligência Artificial cruzará os dados do banco de dados para antecipar gargalos e emitir pareceres de risco.</p>
                
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Produto para Projeção</label>
                        <select id="sim_product" class="form-control form-control-modern">
                            @foreach($availableLines as $line)
                                <option value="{{ $line }}">{{ $line }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Volume Desejado</label>
                        <input type="number" id="sim_quantity" class="form-control form-control-modern" value="10000" min="1">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <button id="btnSimulate" class="btn-gemini w-100 shadow-sm" style="display: block;">
                            <span class="btn-gemini-content py-2 font-weight-bold">
                                Analisar
                            </span>
                        </button>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div id="aiResponse" class="p-3 bg-light rounded text-dark border" style="min-height: 120px; font-size: 0.95rem; line-height: 1.6; border-color: var(--lg-border-gray) !important;">
                            <span class="text-muted">Insira os parâmetros acima e ative a simulação preditiva...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
        calculateEfficiency();
        setupSortableColumns();
        setupApiKeyValidation();
        setupSimulation();

    /**
     * Calcula e exibe a eficiência para cada linha
     */
    function calculateEfficiency() {
        const rows = document.querySelectorAll('.productivity-row, .productivity-row-summary');
        rows.forEach(row => {
            const produced = parseInt(row.querySelector('.produced-value').textContent) || 0;
            const defects = parseInt(row.querySelector('.defect-value').textContent) || 0;
            const efficiencyCell = row.querySelector('.efficiency-value');

            let efficiency = 0;
            if (produced > 0) {
                efficiency = ((produced - defects) / produced) * 100;
            }
            if (efficiency < 0) efficiency = 0;

            const efficiencyPercent = efficiency.toFixed(1) + '%';
            
            let badgeClass = 'efficiency-critical';
            if (efficiency >= 95) {
                badgeClass = 'efficiency-excellent';
            } else if (efficiency >= 85) {
                badgeClass = 'efficiency-warning';
            }

            efficiencyCell.innerHTML = `<span class="efficiency-badge ${badgeClass}">${efficiencyPercent}</span>`;
        });
    }

    /**
     * Setup para colunas ordenáveis
     */
    function setupSortableColumns() {
        const currentSort = '{{ $sort }}';
        const currentDirection = '{{ $direction }}';

        if (currentSort) {
            const thElement = document.querySelector(`th[data-column="${currentSort}"]`);
            if (thElement) {
                thElement.classList.add(`sort-${currentDirection}`);
            }
        }

        document.querySelectorAll('th.sortable').forEach(th => {
            th.addEventListener('click', function() {
                const column = this.getAttribute('data-column');
                const currentColumn = '{{ $sort }}';
                let newDirection = 'asc';

                if (column === currentColumn) {
                    newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                }

                const params = new URLSearchParams(window.location.search);
                params.set('sort', column);
                params.set('direction', newDirection);
                
                window.location.href = `{{ url('/dashboard') }}?${params.toString()}`;
            });
        });
    }

    /**
     * Validação da GEMINI_API_KEY ao abrir o modal
     */
    function setupApiKeyValidation() {
        const modal = document.getElementById('aiSimulationModal');
        const apiKeyAlert = document.getElementById('apiKeyAlert');
        const btnSimulate = document.getElementById('btnSimulate');

        modal.addEventListener('show.bs.modal', function() {
            fetch('{{ url("/dashboard/check-api-key") }}')
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        apiKeyAlert.style.display = 'block';
                        btnSimulate.disabled = true;
                        btnSimulate.style.opacity = '0.5';
                        btnSimulate.style.cursor = 'not-allowed';
                    } else {
                        apiKeyAlert.style.display = 'none';
                        btnSimulate.disabled = false;
                        btnSimulate.style.opacity = '1';
                        btnSimulate.style.cursor = 'pointer';
                    }
                })
                .catch(error => {
                    console.error('Erro ao verificar API key:', error);
                });
        });
    }

    /**
     * Setup da simulação preditiva
     */
    function setupSimulation() {
        document.getElementById('btnSimulate').addEventListener('click', function() {
            const product = document.getElementById('sim_product').value;
            const quantity = document.getElementById('sim_quantity').value;
            const responseBox = document.getElementById('aiResponse');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if(quantity <= 0) {
                alert('Insira uma quantidade válida.');
                return;
            }

            responseBox.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Analisando dados históricos e consultando a IA...';
            this.disabled = true;

            fetch('{{ url("/dashboard/simulate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    product_line: product,
                    simulated_quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                responseBox.innerHTML = '<strong>Análise Preditiva:</strong><br>' + data.response;
                document.getElementById('btnSimulate').disabled = false;
            })
            .catch(error => {
                responseBox.innerHTML = '<span class="text-danger">Erro ao gerar simulação. Tente novamente.</span>';
                document.getElementById('btnSimulate').disabled = false;
            });
        });
    }
</script>
@endpush

@push('styles')
<style>
</style>
@endpush