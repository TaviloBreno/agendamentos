<?= $this->extend('Back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-dollar-sign fa-fw"></i> <?= esc($title) ?>
    </h1>
    <div>
        <a href="<?= route_to('super.reports.export') ?>?type=financial&date_start=<?= $filters['date_start'] ?>&date_end=<?= $filters['date_end'] ?><?= $filters['unit_id'] ? '&unit_id=' . $filters['unit_id'] : '' ?>" 
           class="btn btn-sm btn-success">
            <i class="fas fa-file-csv fa-fw"></i> Exportar CSV
        </a>
        <a href="<?= route_to('super.reports') ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-fw"></i> Voltar
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter fa-fw"></i> Filtros
        </h6>
    </div>
    <div class="card-body">
        <form method="get" class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Data Início</label>
                <input type="date" name="date_start" class="form-control" 
                       value="<?= esc($filters['date_start']) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Data Fim</label>
                <input type="date" name="date_end" class="form-control" 
                       value="<?= esc($filters['date_end']) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Unidade</label>
                <select name="unit_id" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?= $unit->id ?>" <?= $filters['unit_id'] == $unit->id ? 'selected' : '' ?>>
                            <?= esc($unit->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary mr-2">
                    <i class="fas fa-search fa-fw"></i> Filtrar
                </button>
                <a href="<?= route_to('super.reports.financial') ?>" class="btn btn-secondary">
                    <i class="fas fa-times fa-fw"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Resumo Financeiro -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Faturamento Total</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            R$ <?= number_format($revenueData['total_revenue'] ?? 0, 2, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Atendimentos Concluídos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $revenueData['total_appointments'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Faturamento por Serviço -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-concierge-bell fa-fw"></i> Faturamento por Serviço
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($revenueByService)): ?>
                    <p class="text-center text-muted">Nenhum dado disponível</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Serviço</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-right">Faturamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revenueByService as $service): ?>
                                    <tr>
                                        <td><?= esc($service['service_name']) ?></td>
                                        <td class="text-center"><?= $service['quantity'] ?></td>
                                        <td class="text-right font-weight-bold text-success">
                                            R$ <?= number_format($service['revenue'], 2, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th>Total</th>
                                    <th class="text-center"><?= array_sum(array_column($revenueByService, 'quantity')) ?></th>
                                    <th class="text-right text-success">
                                        R$ <?= number_format(array_sum(array_column($revenueByService, 'revenue')), 2, ',', '.') ?>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <canvas id="serviceChart" height="200" class="mt-3"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Faturamento por Profissional -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-tie fa-fw"></i> Faturamento por Profissional
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($revenueByProfessional)): ?>
                    <p class="text-center text-muted">Nenhum dado disponível</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Profissional</th>
                                    <th class="text-center">Atendimentos</th>
                                    <th class="text-right">Faturamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revenueByProfessional as $prof): ?>
                                    <tr>
                                        <td><?= esc($prof['professional_name']) ?></td>
                                        <td class="text-center"><?= $prof['quantity'] ?></td>
                                        <td class="text-right font-weight-bold text-success">
                                            R$ <?= number_format($prof['revenue'], 2, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th>Total</th>
                                    <th class="text-center"><?= array_sum(array_column($revenueByProfessional, 'quantity')) ?></th>
                                    <th class="text-right text-success">
                                        R$ <?= number_format(array_sum(array_column($revenueByProfessional, 'revenue')), 2, ',', '.') ?>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <canvas id="professionalChart" height="200" class="mt-3"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Evolução Mensal -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-line fa-fw"></i> Evolução do Faturamento
        </h6>
    </div>
    <div class="card-body">
        <canvas id="monthlyChart" height="100"></canvas>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = [
        'rgba(78, 115, 223, 0.8)',
        'rgba(28, 200, 138, 0.8)',
        'rgba(54, 185, 204, 0.8)',
        'rgba(246, 194, 62, 0.8)',
        'rgba(231, 74, 59, 0.8)',
        'rgba(133, 135, 150, 0.8)'
    ];

    <?php if (!empty($revenueByService)): ?>
    // Gráfico de Serviços
    const serviceData = <?= json_encode($revenueByService) ?>;
    const serviceCtx = document.getElementById('serviceChart').getContext('2d');
    new Chart(serviceCtx, {
        type: 'doughnut',
        data: {
            labels: serviceData.map(d => d.service_name),
            datasets: [{
                data: serviceData.map(d => d.revenue),
                backgroundColor: colors.slice(0, serviceData.length)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    <?php endif; ?>

    <?php if (!empty($revenueByProfessional)): ?>
    // Gráfico de Profissionais
    const profData = <?= json_encode($revenueByProfessional) ?>;
    const profCtx = document.getElementById('professionalChart').getContext('2d');
    new Chart(profCtx, {
        type: 'doughnut',
        data: {
            labels: profData.map(d => d.professional_name),
            datasets: [{
                data: profData.map(d => d.revenue),
                backgroundColor: colors.slice(0, profData.length)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    <?php endif; ?>

    // Gráfico Mensal
    fetch('<?= route_to('super.reports.chart') ?>?type=revenue_by_month&date_start=<?= $filters['date_start'] ?>&date_end=<?= $filters['date_end'] ?><?= $filters['unit_id'] ? '&unit_id=' . $filters['unit_id'] : '' ?>')
        .then(r => r.json())
        .then(response => {
            if (response.success && response.data.length > 0) {
                const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
                new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: response.data.map(d => {
                            const [year, month] = d.month.split('-');
                            return new Date(year, month - 1).toLocaleDateString('pt-BR', { month: 'short', year: '2-digit' });
                        }),
                        datasets: [{
                            label: 'Faturamento (R$)',
                            data: response.data.map(d => d.revenue),
                            borderColor: 'rgba(28, 200, 138, 1)',
                            backgroundColor: 'rgba(28, 200, 138, 0.1)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR');
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'R$ ' + context.raw.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
});
</script>
<?= $this->endSection() ?>
