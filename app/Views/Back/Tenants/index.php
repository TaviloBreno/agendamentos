<?php
/**
 * View: Back/Tenants/index.php
 * 
 * Listagem de Empresas (Multi-tenant) com DataTables
 * 
 * @var string $title Título da página
 * @var string $pageHeading Título exibido na página
 * @var array $tenants Lista de empresas
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('css') ?>
<!-- DataTables CSS - Page Level -->
<link href="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?= esc($pageHeading ?? 'Empresas') ?>
    </h1>
    <a href="<?= route_to('super.tenants.new') ?>" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Nova Empresa
    </a>
</div>

<!-- Flash Messages -->
<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i><?= session('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i><?= session('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Total Empresas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total de Empresas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count($tenants ?? []) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empresas Ativas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Ativas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($tenants ?? [], fn($t) => $t->status === 'active')) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Em Trial -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Em Trial
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($tenants ?? [], fn($t) => $t->status === 'trial')) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspensas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Suspensas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($tenants ?? [], fn($t) => $t->status === 'suspended')) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-building mr-2"></i>Lista de Empresas
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th>Empresa</th>
                        <th width="10%">Slug</th>
                        <th width="10%">Plano</th>
                        <th width="10%">Status</th>
                        <th width="12%">Expira em</th>
                        <th width="15%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tenants)): ?>
                        <?php foreach ($tenants as $tenant): ?>
                            <tr>
                                <td class="text-center"><?= $tenant->id ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($tenant->logo): ?>
                                            <img src="<?= base_url('uploads/tenants/' . $tenant->logo) ?>" 
                                                 alt="Logo" class="rounded mr-2" width="32" height="32">
                                        <?php else: ?>
                                            <div class="rounded bg-primary text-white d-flex align-items-center justify-content-center mr-2" 
                                                 style="width: 32px; height: 32px; font-size: 12px;">
                                                <?= strtoupper(substr($tenant->name, 0, 2)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?= esc($tenant->name) ?></strong>
                                            <?php if ($tenant->email): ?>
                                                <br><small class="text-muted"><?= esc($tenant->email) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code><?= esc($tenant->slug) ?></code>
                                </td>
                                <td>
                                    <?= $tenant->getPlanBadge() ?>
                                </td>
                                <td>
                                    <?= $tenant->getStatusBadge() ?>
                                </td>
                                <td>
                                    <?php if ($tenant->plan_expires_at): ?>
                                        <?php 
                                        $expires = new DateTime($tenant->plan_expires_at);
                                        $now = new DateTime();
                                        $diff = $now->diff($expires);
                                        $expired = $expires < $now;
                                        ?>
                                        <span class="<?= $expired ? 'text-danger' : ($diff->days < 7 ? 'text-warning' : 'text-success') ?>">
                                            <?php if ($expired): ?>
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Expirado
                                            <?php else: ?>
                                                <?= $diff->days ?> dias
                                            <?php endif; ?>
                                        </span>
                                    <?php elseif ($tenant->trial_ends_at): ?>
                                        <?php 
                                        $trial = new DateTime($tenant->trial_ends_at);
                                        $now = new DateTime();
                                        $diff = $now->diff($trial);
                                        ?>
                                        <span class="text-info">
                                            <i class="fas fa-clock mr-1"></i>Trial: <?= $diff->days ?> dias
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= route_to('super.tenants.show', $tenant->id) ?>" 
                                           class="btn btn-info btn-sm" title="Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= route_to('super.tenants.edit', $tenant->id) ?>" 
                                           class="btn btn-primary btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Toggle Status Button -->
                                        <form action="<?= route_to('super.tenants.status', $tenant->id) ?>" 
                                              method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="PUT">
                                            <?php if ($tenant->status === 'active'): ?>
                                                <button type="submit" class="btn btn-warning btn-sm" 
                                                        title="Suspender" 
                                                        onclick="return confirm('Deseja suspender esta empresa?')">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-success btn-sm" 
                                                        title="Ativar"
                                                        onclick="return confirm('Deseja ativar esta empresa?')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                        
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                title="Excluir" 
                                                data-toggle="modal" 
                                                data-target="#deleteModal<?= $tenant->id ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?= $tenant->id ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmar Exclusão</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-left">
                                                    <p>Tem certeza que deseja excluir a empresa <strong><?= esc($tenant->name) ?></strong>?</p>
                                                    <div class="alert alert-danger">
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        Esta ação irá remover todos os dados desta empresa, incluindo usuários, unidades, agendamentos, etc.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                        Cancelar
                                                    </button>
                                                    <form action="<?= route_to('super.tenants.delete', $tenant->id) ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash mr-1"></i>Excluir
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- DataTables JS - Page Level -->
<script src="<?= base_url('back/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
<?= $this->endSection() ?>
