<?php
/**
 * View: Back/Tenants/show.php
 * 
 * Exibe detalhes de uma Empresa (Tenant)
 * 
 * @var string $title Título da página
 * @var string $pageHeading Título exibido na página
 * @var object $tenant Dados da empresa
 * @var array $stats Estatísticas da empresa
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-building mr-2"></i><?= esc($tenant->name) ?>
    </h1>
    <div>
        <a href="<?= route_to('super.tenants.edit', $tenant->id) ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-edit fa-sm mr-1"></i> Editar
        </a>
        <a href="<?= route_to('super.tenants') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar
        </a>
    </div>
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

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Usuários -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Usuários
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['users'] ?? 0 ?> / <?= $tenant->max_users ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unidades -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Unidades
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['units'] ?? 0 ?> / <?= $tenant->max_units ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Agendamentos do Mês -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Agendamentos (Mês)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['appointments_month'] ?? 0 ?> / <?= $tenant->max_appointments_month ?: '∞' ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clientes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Clientes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['clients'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informações Principais -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-2"></i>Informações da Empresa
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="35%">Nome:</th>
                                <td><?= esc($tenant->name) ?></td>
                            </tr>
                            <tr>
                                <th>Slug:</th>
                                <td>
                                    <code>/t/<?= esc($tenant->slug) ?></code>
                                    <a href="<?= site_url('t/' . $tenant->slug) ?>" target="_blank" class="ml-2">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>E-mail:</th>
                                <td>
                                    <?php if ($tenant->email): ?>
                                        <a href="mailto:<?= esc($tenant->email) ?>"><?= esc($tenant->email) ?></a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Telefone:</th>
                                <td><?= esc($tenant->phone) ?: '<span class="text-muted">-</span>' ?></td>
                            </tr>
                            <tr>
                                <th>CNPJ:</th>
                                <td><?= $tenant->getFormattedDocument() ?: '<span class="text-muted">-</span>' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="35%">Domínio:</th>
                                <td>
                                    <?php if ($tenant->domain): ?>
                                        <a href="https://<?= esc($tenant->domain) ?>" target="_blank">
                                            <?= esc($tenant->domain) ?>
                                            <i class="fas fa-external-link-alt ml-1"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Endereço:</th>
                                <td><?= esc($tenant->address) ?: '<span class="text-muted">-</span>' ?></td>
                            </tr>
                            <tr>
                                <th>Cidade/UF:</th>
                                <td>
                                    <?php if ($tenant->city || $tenant->state): ?>
                                        <?= esc($tenant->city) ?><?= $tenant->state ? '/' . esc($tenant->state) : '' ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>CEP:</th>
                                <td><?= esc($tenant->zip_code) ?: '<span class="text-muted">-</span>' ?></td>
                            </tr>
                            <tr>
                                <th>Criado em:</th>
                                <td>
                                    <?= $tenant->created_at ? date('d/m/Y H:i', strtotime($tenant->created_at)) : '-' ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recursos Habilitados -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-toggle-on mr-2"></i>Recursos Habilitados
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <div class="p-3 <?= $tenant->whatsapp_enabled ? 'bg-success' : 'bg-secondary' ?> text-white rounded">
                            <i class="fab fa-whatsapp fa-2x mb-2"></i>
                            <h6 class="mb-0">WhatsApp</h6>
                            <small><?= $tenant->whatsapp_enabled ? 'Ativo' : 'Inativo' ?></small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="p-3 <?= $tenant->payments_enabled ? 'bg-primary' : 'bg-secondary' ?> text-white rounded">
                            <i class="fas fa-credit-card fa-2x mb-2"></i>
                            <h6 class="mb-0">Pagamentos</h6>
                            <small><?= $tenant->payments_enabled ? 'Ativo' : 'Inativo' ?></small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="p-3 <?= $tenant->hasFeature('email_notifications') ? 'bg-info' : 'bg-secondary' ?> text-white rounded">
                            <i class="fas fa-envelope fa-2x mb-2"></i>
                            <h6 class="mb-0">E-mail</h6>
                            <small><?= $tenant->hasFeature('email_notifications') ? 'Ativo' : 'Inativo' ?></small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="p-3 <?= $tenant->hasFeature('reports') ? 'bg-warning' : 'bg-secondary' ?> text-white rounded">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <h6 class="mb-0">Relatórios</h6>
                            <small><?= $tenant->hasFeature('reports') ? 'Ativo' : 'Inativo' ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Atividade Recente -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i>Atividade Recente
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($recentActivity)): ?>
                    <div class="timeline">
                        <?php foreach ($recentActivity as $activity): ?>
                            <div class="timeline-item">
                                <span class="badge badge-primary"><?= $activity['date'] ?></span>
                                <?= esc($activity['description']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center mb-0">
                        <i class="fas fa-inbox mr-2"></i>Nenhuma atividade recente
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status e Plano -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-star mr-2"></i>Plano e Status
                </h6>
            </div>
            <div class="card-body text-center">
                <?php if ($tenant->logo): ?>
                    <img src="<?= base_url('uploads/tenants/' . $tenant->logo) ?>" 
                         alt="Logo" class="img-thumbnail mb-3" style="max-width: 150px;">
                <?php else: ?>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 100px; height: 100px; font-size: 36px;">
                        <?= strtoupper(substr($tenant->name, 0, 2)) ?>
                    </div>
                <?php endif; ?>
                
                <h5 class="mb-3"><?= esc($tenant->name) ?></h5>
                
                <div class="mb-3">
                    <?= $tenant->getStatusBadge() ?>
                    <?= $tenant->getPlanBadge() ?>
                </div>

                <hr>

                <table class="table table-sm table-borderless text-left">
                    <tr>
                        <th>Plano expira em:</th>
                        <td>
                            <?php if ($tenant->plan_expires_at): ?>
                                <?php 
                                $expires = new DateTime($tenant->plan_expires_at);
                                $now = new DateTime();
                                $expired = $expires < $now;
                                ?>
                                <span class="<?= $expired ? 'text-danger' : 'text-success' ?>">
                                    <?= date('d/m/Y', strtotime($tenant->plan_expires_at)) ?>
                                    <?php if ($expired): ?>
                                        <i class="fas fa-exclamation-triangle ml-1"></i>
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if ($tenant->status === 'trial'): ?>
                    <tr>
                        <th>Trial até:</th>
                        <td>
                            <?php if ($tenant->trial_ends_at): ?>
                                <?= date('d/m/Y', strtotime($tenant->trial_ends_at)) ?>
                                <?php if ($tenant->isTrialExpired()): ?>
                                    <span class="badge badge-danger">Expirado</span>
                                <?php else: ?>
                                    <span class="badge badge-info"><?= $tenant->getDaysRemaining() ?> dias</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Limites -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-sliders-h mr-2"></i>Limites do Plano
                </h6>
            </div>
            <div class="card-body">
                <!-- Usuários -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Usuários</span>
                        <span><?= $stats['users'] ?? 0 ?>/<?= $tenant->max_users ?></span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <?php $pctUsers = $tenant->max_users > 0 ? (($stats['users'] ?? 0) / $tenant->max_users * 100) : 0; ?>
                        <div class="progress-bar <?= $pctUsers >= 90 ? 'bg-danger' : ($pctUsers >= 70 ? 'bg-warning' : 'bg-success') ?>" 
                             style="width: <?= $pctUsers ?>%"></div>
                    </div>
                </div>

                <!-- Unidades -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Unidades</span>
                        <span><?= $stats['units'] ?? 0 ?>/<?= $tenant->max_units ?></span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <?php $pctUnits = $tenant->max_units > 0 ? (($stats['units'] ?? 0) / $tenant->max_units * 100) : 0; ?>
                        <div class="progress-bar <?= $pctUnits >= 90 ? 'bg-danger' : ($pctUnits >= 70 ? 'bg-warning' : 'bg-success') ?>" 
                             style="width: <?= $pctUnits ?>%"></div>
                    </div>
                </div>

                <!-- Agendamentos -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Agendamentos/Mês</span>
                        <span><?= $stats['appointments_month'] ?? 0 ?>/<?= $tenant->max_appointments_month ?: '∞' ?></span>
                    </div>
                    <?php if ($tenant->max_appointments_month > 0): ?>
                    <div class="progress" style="height: 10px;">
                        <?php $pctAppts = ($stats['appointments_month'] ?? 0) / $tenant->max_appointments_month * 100; ?>
                        <div class="progress-bar <?= $pctAppts >= 90 ? 'bg-danger' : ($pctAppts >= 70 ? 'bg-warning' : 'bg-success') ?>" 
                             style="width: <?= $pctAppts ?>%"></div>
                    </div>
                    <?php else: ?>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt mr-2"></i>Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= route_to('super.tenants.edit', $tenant->id) ?>" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-edit mr-2"></i>Editar Empresa
                    </a>
                    
                    <form action="<?= route_to('super.tenants.status', $tenant->id) ?>" method="POST" class="mb-2">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        <?php if ($tenant->status === 'active'): ?>
                            <button type="submit" class="btn btn-warning btn-block" 
                                    onclick="return confirm('Deseja suspender esta empresa?')">
                                <i class="fas fa-pause mr-2"></i>Suspender
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success btn-block"
                                    onclick="return confirm('Deseja ativar esta empresa?')">
                                <i class="fas fa-play mr-2"></i>Ativar
                            </button>
                        <?php endif; ?>
                    </form>
                    
                    <a href="<?= site_url('t/' . $tenant->slug) ?>" target="_blank" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-external-link-alt mr-2"></i>Visitar Site
                    </a>
                    
                    <button type="button" class="btn btn-danger btn-block" 
                            data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash mr-2"></i>Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
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

<?= $this->endSection() ?>
