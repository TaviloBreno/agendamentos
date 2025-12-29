<?= $this->extend('Back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-calendar-alt fa-fw"></i> <?= esc($title) ?>
    </h1>
    <div>
        <a href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['export' => 'csv'])) ?>&type=appointments" 
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
            <div class="col-md-3 mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Todos</option>
                    <option value="scheduled" <?= $filters['status'] === 'scheduled' ? 'selected' : '' ?>>Agendado</option>
                    <option value="confirmed" <?= $filters['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmado</option>
                    <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Concluído</option>
                    <option value="cancelled" <?= $filters['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                    <option value="no_show" <?= $filters['status'] === 'no_show' ? 'selected' : '' ?>>Não Compareceu</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search fa-fw"></i> Filtrar
                </button>
                <a href="<?= route_to('super.reports.appointments') ?>" class="btn btn-secondary">
                    <i class="fas fa-times fa-fw"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body py-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body py-2">
                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Agendados</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['scheduled'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body py-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Confirmados</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['confirmed'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body py-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Concluídos</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['completed'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body py-2">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Cancelados</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['cancelled'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body py-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Não Compareceu</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['no_show'] ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie fa-fw"></i> Por Status
                </h6>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line fa-fw"></i> Por Dia
                </h6>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Agendamentos -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list fa-fw"></i> Lista de Agendamentos
        </h6>
    </div>
    <div class="card-body">
        <?php if (empty($appointments)): ?>
            <p class="text-center text-muted">Nenhum agendamento encontrado para o período selecionado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="appointmentsTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Cliente</th>
                            <th>Profissional</th>
                            <th>Serviço</th>
                            <th>Unidade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $apt): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($apt->date)) ?></td>
                                <td><?= substr($apt->start_time, 0, 5) ?> - <?= substr($apt->end_time, 0, 5) ?></td>
                                <td><?= esc($apt->getClient()->name ?? '-') ?></td>
                                <td><?= esc($apt->getProfessional()->name ?? '-') ?></td>
                                <td><?= esc($apt->getService()->name ?? '-') ?></td>
                                <td><?= esc($apt->getUnit()->name ?? '-') ?></td>
                                <td>
                                    <span class="badge badge-<?= getStatusBadgeClass($apt->status) ?>">
                                        <?= getStatusLabel($apt->status) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Status
    const statsData = <?= json_encode($stats) ?>;
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Agendados', 'Confirmados', 'Concluídos', 'Cancelados', 'Não Compareceu'],
            datasets: [{
                data: [
                    statsData.scheduled,
                    statsData.confirmed,
                    statsData.completed,
                    statsData.cancelled,
                    statsData.no_show
                ],
                backgroundColor: ['#6c757d', '#36b9cc', '#1cc88a', '#e74a3b', '#f6c23e']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico Diário - Carregar via AJAX
    fetch('<?= route_to('super.reports.chart') ?>?type=appointments_by_day&date_start=<?= $filters['date_start'] ?>&date_end=<?= $filters['date_end'] ?><?= $filters['unit_id'] ? '&unit_id=' . $filters['unit_id'] : '' ?>')
        .then(r => r.json())
        .then(response => {
            if (response.success) {
                const dailyCtx = document.getElementById('dailyChart').getContext('2d');
                new Chart(dailyCtx, {
                    type: 'line',
                    data: {
                        labels: response.data.map(d => {
                            const date = new Date(d.date);
                            return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                        }),
                        datasets: [{
                            label: 'Agendamentos',
                            data: response.data.map(d => d.total),
                            borderColor: 'rgba(78, 115, 223, 1)',
                            backgroundColor: 'rgba(78, 115, 223, 0.1)',
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
                                ticks: { stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
});
</script>
<?= $this->endSection() ?>

<?php
function getStatusBadgeClass(string $status): string {
    return match($status) {
        'scheduled' => 'secondary',
        'confirmed' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
        'no_show' => 'warning',
        default => 'secondary'
    };
}

function getStatusLabel(string $status): string {
    return match($status) {
        'scheduled' => 'Agendado',
        'confirmed' => 'Confirmado',
        'completed' => 'Concluído',
        'cancelled' => 'Cancelado',
        'no_show' => 'Não Compareceu',
        default => $status
    };
}
?>
