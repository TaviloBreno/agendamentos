<?= $this->extend('Back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-line fa-fw"></i> <?= esc($title) ?>
    </h1>
</div>

<!-- Resumo Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Agendamentos Hoje</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['today_appointments'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Concluídos (Mês)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['month_completed'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total de Clientes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['total_clients'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Cancelados (Mês)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['month_cancelled'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Gráfico semanal -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar fa-fw"></i> Agendamentos - Últimos 7 Dias
                </h6>
            </div>
            <div class="card-body">
                <canvas id="weeklyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Agendamentos de hoje -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-check fa-fw"></i> Agenda de Hoje
                </h6>
                <span class="badge badge-primary"><?= count($todayAppointments) ?></span>
            </div>
            <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                <?php if (empty($todayAppointments)): ?>
                    <p class="text-center text-muted mb-0">
                        <i class="fas fa-calendar-times fa-2x mb-2"></i><br>
                        Nenhum agendamento para hoje
                    </p>
                <?php else: ?>
                    <?php foreach ($todayAppointments as $apt): ?>
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                            <div class="mr-3">
                                <span class="badge badge-<?= getStatusBadgeClass($apt->status) ?>">
                                    <?= substr($apt->start_time, 0, 5) ?>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold"><?= esc($apt->getClient()->name ?? '-') ?></div>
                                <small class="text-muted">
                                    <?= esc($apt->getService()->name ?? '-') ?> • 
                                    <?= esc($apt->getProfessional()->name ?? '-') ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Serviços -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-star fa-fw"></i> Serviços Mais Populares (Mês)
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($topServices)): ?>
                    <p class="text-center text-muted">Nenhum dado disponível</p>
                <?php else: ?>
                    <?php 
                    $maxTotal = max(array_column($topServices, 'total'));
                    foreach ($topServices as $service): 
                        $percentage = $maxTotal > 0 ? ($service['total'] / $maxTotal) * 100 : 0;
                    ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><?= esc($service['service_name']) ?></span>
                                <span class="text-primary font-weight-bold"><?= $service['total'] ?></span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                     style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Profissionais -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-tie fa-fw"></i> Profissionais Mais Ativos (Mês)
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($topProfessionals)): ?>
                    <p class="text-center text-muted">Nenhum dado disponível</p>
                <?php else: ?>
                    <?php 
                    $maxTotal = max(array_column($topProfessionals, 'total'));
                    foreach ($topProfessionals as $professional): 
                        $percentage = $maxTotal > 0 ? ($professional['total'] / $maxTotal) * 100 : 0;
                    ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><?= esc($professional['professional_name']) ?></span>
                                <span class="text-success font-weight-bold"><?= $professional['total'] ?></span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Links para relatórios detalhados -->
<div class="row">
    <div class="col-lg-4 mb-4">
        <a href="<?= route_to('super.reports.appointments') ?>" class="card bg-primary text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar-alt fa-3x mr-3"></i>
                    <div>
                        <h5 class="mb-1">Relatório de Agendamentos</h5>
                        <small>Análise detalhada de agendamentos</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 mb-4">
        <a href="<?= route_to('super.reports.clients') ?>" class="card bg-success text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users fa-3x mr-3"></i>
                    <div>
                        <h5 class="mb-1">Relatório de Clientes</h5>
                        <small>Análise de clientes e frequência</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 mb-4">
        <a href="<?= route_to('super.reports.financial') ?>" class="card bg-info text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-dollar-sign fa-3x mr-3"></i>
                    <div>
                        <h5 class="mb-1">Relatório Financeiro</h5>
                        <small>Faturamento por serviço e profissional</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados do gráfico semanal
    const weeklyData = <?= json_encode($weeklyStats) ?>;
    
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: weeklyData.map(d => d.day_name),
            datasets: [{
                label: 'Agendamentos',
                data: weeklyData.map(d => d.count),
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>

<?php
function getStatusBadgeClass(string $status): string {
    return match($status) {
        'scheduled' => 'secondary',
        'confirmed' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger',
        'no_show' => 'warning',
        default => 'secondary'
    };
}
?>
