<?= $this->extend('Back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-users fa-fw"></i> <?= esc($title) ?>
    </h1>
    <div>
        <a href="<?= route_to('super.reports.export') ?>?type=clients" class="btn btn-sm btn-success">
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
            <div class="col-md-4 mb-3">
                <label class="form-label">Data Início</label>
                <input type="date" name="date_start" class="form-control" 
                       value="<?= esc($filters['date_start']) ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Data Fim</label>
                <input type="date" name="date_end" class="form-control" 
                       value="<?= esc($filters['date_end']) ?>">
            </div>
            <div class="col-md-4 mb-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary mr-2">
                    <i class="fas fa-search fa-fw"></i> Filtrar
                </button>
                <a href="<?= route_to('super.reports.clients') ?>" class="btn btn-secondary">
                    <i class="fas fa-times fa-fw"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Resumo -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total de Clientes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalClients ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Clientes -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-star fa-fw"></i> Clientes Mais Frequentes
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($topClients)): ?>
                    <p class="text-center text-muted">Nenhum dado disponível</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th class="text-center">Agendamentos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topClients as $i => $client): ?>
                                    <tr>
                                        <td>
                                            <?php if ($i < 3): ?>
                                                <span class="badge badge-<?= ['warning', 'secondary', 'info'][$i] ?>">
                                                    <?= $i + 1 ?>º
                                                </span>
                                            <?php else: ?>
                                                <?= $i + 1 ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= route_to('super.clients.show', $client['id']) ?>">
                                                <?= esc($client['name']) ?>
                                            </a>
                                        </td>
                                        <td><?= esc($client['email']) ?></td>
                                        <td><?= esc($client['phone']) ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-primary badge-pill">
                                                <?= $client['total_appointments'] ?>
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
    </div>

    <!-- Novos Clientes por Mês -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-plus fa-fw"></i> Novos Clientes por Mês
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($newClientsByMonth)): ?>
                    <p class="text-center text-muted">Nenhum dado disponível</p>
                <?php else: ?>
                    <canvas id="newClientsChart" height="250"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($newClientsByMonth)): ?>
    const newClientsData = <?= json_encode($newClientsByMonth) ?>;
    
    const ctx = document.getElementById('newClientsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: newClientsData.map(d => {
                const [year, month] = d.month.split('-');
                return new Date(year, month - 1).toLocaleDateString('pt-BR', { month: 'short', year: '2-digit' });
            }),
            datasets: [{
                label: 'Novos Clientes',
                data: newClientsData.map(d => d.total),
                backgroundColor: 'rgba(28, 200, 138, 0.8)',
                borderColor: 'rgba(28, 200, 138, 1)',
                borderWidth: 1
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
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
