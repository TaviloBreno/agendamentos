<?= $this->extend('Back/layout/main') ?>

<?= $this->section('css') ?>
<!-- DataTables CSS -->
<link href="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-cog text-primary mr-2"></i>Usuários
    </h1>
    <a href="<?= route_to('super.users.new') ?>" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Novo Usuário
    </a>
</div>

<!-- Alertas -->
<?= view('Back/layout/partials/alerts') ?>

<!-- Stats Cards -->
<div class="row mb-4">
    <!-- Total de Usuários -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total de Usuários
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

    <!-- Super Admins -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Super Admins
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['by_role']['super'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admins -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Administradores
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['by_role']['admin'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usuários Comuns -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Usuários
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['by_role']['user'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
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
            <i class="fas fa-list mr-1"></i>Lista de Usuários
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Usuário</th>
                        <th width="20%">E-mail</th>
                        <th width="12%">Papel</th>
                        <th width="10%">Status</th>
                        <th width="12%">Cadastro</th>
                        <th width="15%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $currentUserId = session()->get('user_id');
                    foreach ($users as $user): 
                        $isSelf = $currentUserId && $currentUserId == $user->id;
                    ?>
                    <tr>
                        <td><?= esc($user->id) ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= $user->avatarUrl(40) ?>" alt="<?= esc($user->name) ?>" 
                                     class="rounded-circle mr-2" width="40" height="40">
                                <div>
                                    <div class="font-weight-bold">
                                        <?= esc($user->name) ?>
                                        <?php if ($isSelf): ?>
                                            <span class="badge badge-primary ml-1">Você</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?= esc($user->email) ?></td>
                        <td><?= $user->roleBadge() ?></td>
                        <td><?= $user->statusBadge() ?></td>
                        <td><?= $user->created_at->format('d/m/Y') ?></td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= route_to('super.users.show', $user->id) ?>" 
                                   class="btn btn-info btn-sm" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= route_to('super.users.edit', $user->id) ?>" 
                                   class="btn btn-primary btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (!$isSelf): ?>
                                <form action="<?= route_to('super.users.action', $user->id) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="PUT">
                                    <button type="submit" class="btn btn-<?= $user->isActive() ? 'warning' : 'success' ?> btn-sm" 
                                            title="<?= $user->isActive() ? 'Desativar' : 'Ativar' ?>">
                                        <i class="fas fa-<?= $user->isActive() ? 'ban' : 'check' ?>"></i>
                                    </button>
                                </form>
                                <form action="<?= route_to('super.users.delete', $user->id) ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
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
            { orderable: false, targets: [6] }
        ]
    });
});
</script>
<?= $this->endSection() ?>
