<?= $this->extend('Back/layout/main') ?>

<?= $this->section('css') ?>
<!-- DataTables CSS -->
<link href="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-users text-primary mr-2"></i>Clientes
    </h1>
    <a href="<?= route_to('super.clients.new') ?>" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Novo Cliente
    </a>
</div>

<!-- Alertas -->
<?= view('Back/layout/partials/alerts') ?>

<!-- Stats Cards -->
<div class="row mb-4">
    <!-- Total de Clientes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total de Clientes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clientes Ativos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Ativos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['active'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clientes Inativos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Inativos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['inactive'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-times fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aniversariantes Hoje -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Aniversariantes Hoje
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['birthdays_today'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-birthday-cake fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list mr-1"></i>Lista de Clientes
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Cliente</th>
                        <th width="15%">Contato</th>
                        <th width="12%">Localização</th>
                        <th width="12%">Agendamentos</th>
                        <th width="10%">Status</th>
                        <th width="10%">Cadastro</th>
                        <th width="15%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= esc($client->id) ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= $client->avatarUrl(40) ?>" alt="<?= esc($client->name) ?>" 
                                     class="rounded-circle mr-2" width="40" height="40">
                                <div>
                                    <div class="font-weight-bold"><?= esc($client->name) ?></div>
                                    <small class="text-muted"><?= esc($client->email) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($client->phone): ?>
                                <div><i class="fas fa-phone text-muted mr-1"></i><?= esc($client->phoneFormatted()) ?></div>
                            <?php endif; ?>
                            <?php if ($client->cpf): ?>
                                <small class="text-muted"><i class="fas fa-id-card mr-1"></i><?= esc($client->cpfFormatted()) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($client->city): ?>
                                <?= esc($client->city) ?><?php if ($client->state): ?>/<?= esc($client->state) ?><?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $count = $client->appointmentsCount();
                            if ($count === 0): ?>
                                <span class="badge badge-secondary">Nenhum</span>
                            <?php else: ?>
                                <span class="badge badge-<?= $count > 5 ? 'success' : 'info' ?>">
                                    <?= $count ?> agendamento<?= $count > 1 ? 's' : '' ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?= $client->statusBadge() ?></td>
                        <td><?= $client->created_at->format('d/m/Y') ?></td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= route_to('super.clients.show', $client->id) ?>" 
                                   class="btn btn-info btn-sm" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= route_to('super.clients.edit', $client->id) ?>" 
                                   class="btn btn-primary btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= route_to('super.clients.action', $client->id) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="PUT">
                                    <button type="submit" class="btn btn-<?= $client->isActive() ? 'warning' : 'success' ?> btn-sm" 
                                            title="<?= $client->isActive() ? 'Desativar' : 'Ativar' ?>">
                                        <i class="fas fa-<?= $client->isActive() ? 'ban' : 'check' ?>"></i>
                                    </button>
                                </form>
                                <form action="<?= route_to('super.clients.delete', $client->id) ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este cliente?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- DataTables JS -->
<script src="<?= base_url('back/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        order: [[1, 'asc']],
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: [7] }
        ]
    });
});
</script>
<?= $this->endSection() ?>
