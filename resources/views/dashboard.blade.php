@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4 align-items-center">
        <div class="col-md-5">
            <h1 class="h3 text-dark font-weight-bold mb-1">Dashboard de Produtividade</h1>
            <p class="text-muted mb-0">Período: Janeiro de 2026 | Unidade: Planta A</p>
        </div>
        
        <div class="col-md-7 mt-3 mt-md-0 d-flex justify-content-md-end align-items-center">
            
            <button type="button" class="btn-gemini mr-3" data-toggle="modal" data-target="#aiSimulationModal">
                <span class="btn-gemini-content">
                    Análise Preditiva
            </button>

            <form method="GET" action="{{ url('/dashboard') }}" class="form-inline">
                <div class="form-group mr-2">
                    <select name="product_line" id="product_line" class="form-control bg-white border-secondary-50">
                        <option value="">Todas as linhas juntas</option>
                        @foreach($availableLines as $line)
                            <option value="{{ $line }}" {{ request('product_line') == $line ? 'selected' : '' }}>
                                {{ $line }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-dark px-4">Filtrar</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="productivityTable">
                            <thead class="bg-light text-secondary border-bottom">
                                <tr>
                                    <th class="border-0 px-4 py-3">ID</th>
                                    <th class="border-0 py-3">Data Fab.</th>
                                    <th class="border-0 py-3">Linha de Produto</th>
                                    <th class="border-0 py-3 text-center">Qtd Produzida</th>
                                    <th class="border-0 py-3 text-center">Qtd Defeitos</th>
                                    <th class="border-0 px-4 py-3 text-center">Eficiência</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productivities as $item)
                                    <tr class="productivity-row">
                                        <td class="px-4 py-3 align-middle text-muted">{{ $item->id }}</td>
                                        <td class="py-3 align-middle text-muted">{{ date('d/m/Y', strtotime($item->production_date)) }}</td>
                                        <td class="py-3 align-middle font-weight-bold">{{ $item->product_line }}</td>
                                        <td class="py-3 align-middle text-center produced-value">{{ $item->produced_quantity }}</td>
                                        <td class="py-3 align-middle text-center defect-value">{{ $item->defect_count }}</td>
                                        <td class="px-4 py-3 align-middle text-center efficiency-value font-weight-bold">
                                            <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                                <span class="sr-only">Calculando...</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @if($productivities->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            Nenhum registro encontrado para os filtros selecionados.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-center border-top">
                    {{ $productivities->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="aiSimulationModal" tabindex="-1" aria-labelledby="aiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold" id="aiModalLabel">✨ Assistente Inteligente</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small">Insira a meta volumétrica desejada. A Inteligência Artifical cruzará os dados do banco de dados para antecipar gargalos e emitir pareceres de risco.</p>
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Produto para Projeção</label>
                            <select id="sim_product" class="form-control">
                                @foreach($availableLines as $line)
                                    <option value="{{ $line }}">{{ $line }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Volume Desejado</label>
                            <input type="number" id="sim_quantity" class="form-control" value="10000" min="1">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <meta name="csrf-token" content="{{ csrf_token() }}">
                            <button id="btnSimulate" class="btn-gemini w-100 shadow-sm">
                            <span class="btn-gemini-content py-2 font-weight-bold">
                                Analisar
                            </span>
        </button>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div id="aiResponse" class="p-3 bg-light rounded text-dark border" style="min-height: 120px; font-size: 0.95rem; line-height: 1.6;">
                                <span class="text-muted italic">Insira os parâmetros acima e ative a simulação preditiva...</span>
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
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('.productivity-row');
        rows.forEach(row => {
            const produced = parseInt(row.querySelector('.produced-value').textContent) || 0;
            const defects = parseInt(row.querySelector('.defect-value').textContent) || 0;
            const efficiencyCell = row.querySelector('.efficiency-value');

            let efficiency = 0;
            if (produced > 0) {
                efficiency = ((produced - defects) / produced) * 100;
            }
            if (efficiency < 0) efficiency = 0;

            efficiencyCell.innerHTML = efficiency.toFixed(1) + '%';
            
            if (efficiency >= 95) {
                efficiencyCell.classList.add('text-success');
            } else if (efficiency >= 85) {
                efficiencyCell.classList.add('text-warning');
            } else {
                efficiencyCell.classList.add('text-danger');
            }
        });
    });

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
            responseBox.innerHTML = '<strong>Análise Gemini:</strong><br>' + data.response;
            document.getElementById('btnSimulate').disabled = false;
        })
        .catch(error => {
            responseBox.innerHTML = '<span class="text-danger">Erro ao gerar simulação. Tente novamente.</span>';
            document.getElementById('btnSimulate').disabled = false;
        });
    });
</script>
@endpush