<?php
/**
 * View: Back/Professionals/show.php
 * 
 * Exibição de Detalhes de um Profissional
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-tie mr-2"></i><?= esc($pageHeading ?? $professional->name) ?>
    </h1>
    <div>
        <a href="<?= route_to('super.professionals.edit', $professional->id) ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-edit mr-1"></i> Editar
        </a>
        <a href="<?= route_to('super.professionals') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Voltar
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

<div class="row">
    <!-- Informações Principais -->
    <div class="col-lg-8">
        <!-- Card Perfil -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user mr-2"></i>Perfil do Profissional
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <img src="<?= $professional->avatarUrl() ?>" 
                             class="rounded-circle img-thumbnail" 
                             width="120" 
                             height="120"
                             alt="<?= esc($professional->name) ?>">
                        <h5 class="mt-2 mb-0"><?= esc($professional->name) ?></h5>
                        <p class="text-muted"><?= esc($professional->specialtyFormatted()) ?></p>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong class="text-gray-600"><i class="fas fa-envelope mr-2"></i>E-mail:</strong><br>
                                    <a href="mailto:<?= esc($professional->email) ?>"><?= esc($professional->email) ?></a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong class="text-gray-600"><i class="fas fa-phone mr-2"></i>Telefone:</strong><br>
                                    <?= $professional->phoneFormatted() ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong class="text-gray-600"><i class="fas fa-building mr-2"></i>Unidade:</strong><br>
                                    <?php $unit = $professional->getUnit(); ?>
                                    <?php if ($unit): ?>
                                        <a href="<?= route_to('super.units.show', $unit->id) ?>"><?= esc($unit->name) ?></a>
                                    <?php else: ?>
                                        <span class="text-muted">Não vinculado</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong class="text-gray-600"><i class="fas fa-briefcase mr-2"></i>Especialidade:</strong><br>
                                    <?= esc($professional->specialtyFormatted()) ?>
                                </p>
                            </div>
                        </div>

                        <?php if ($professional->bio): ?>
                        <div class="mt-3">
                            <strong class="text-gray-600"><i class="fas fa-align-left mr-2"></i>Biografia:</strong>
                            <p class="mt-1"><?= nl2br(esc($professional->bio)) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Serviços Realizados -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-concierge-bell mr-2"></i>Serviços Realizados
                </h6>
            </div>
            <div class="card-body">
                <?php $services = $professional->loadServices(); ?>
                <?php if (empty($services)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p class="mb-2">Nenhum serviço associado a este profissional.</p>
                        <a href="<?= route_to('super.professionals.edit', $professional->id) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Adicionar Serviços
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($services as $service): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-left-info">
                                    <div class="card-body py-2 px-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 font-weight-bold text-info">
                                                    <?= esc($service->name) ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock mr-1"></i><?= $service->durationFormatted() ?>
                                                    &nbsp;&bull;&nbsp;
                                                    <i class="fas fa-tag mr-1"></i><?= $service->priceFormatted() ?>
                                                </small>
                                            </div>
                                            <span class="badge badge-<?= $service->active ? 'success' : 'secondary' ?>">
                                                <?= $service->active ? 'Ativo' : 'Inativo' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-right mt-2">
                        <small class="text-muted">
                            <i class="fas fa-layer-group mr-1"></i>
                            <?= count($services) ?> serviço<?= count($services) > 1 ? 's' : '' ?>
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-toggle-on mr-2"></i>Status
                </h6>
            </div>
            <div class="card-body text-center">
                <?php if ($professional->active): ?>
                    <span class="badge badge-success p-3 mb-2" style="font-size: 1rem;">
                        <i class="fas fa-check-circle mr-1"></i> ATIVO
                    </span>
                    <p class="text-muted mb-0">Este profissional está disponível para agendamentos.</p>
                <?php else: ?>
                    <span class="badge badge-secondary p-3 mb-2" style="font-size: 1rem;">
                        <i class="fas fa-ban mr-1"></i> INATIVO
                    </span>
                    <p class="text-muted mb-0">Este profissional não aparece para agendamentos.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Datas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-alt mr-2"></i>Registro
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong class="text-gray-600">Criado em:</strong><br>
                    <i class="fas fa-calendar-plus mr-1 text-muted"></i>
                    <?= $professional->created_at ? $professional->created_at->format('d/m/Y H:i') : '-' ?>
                </p>
                <p class="mb-0">
                    <strong class="text-gray-600">Atualizado em:</strong><br>
                    <i class="fas fa-calendar-check mr-1 text-muted"></i>
                    <?= $professional->updated_at ? $professional->updated_at->format('d/m/Y H:i') : '-' ?>
                </p>
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
                <a href="<?= route_to('super.professionals.edit', $professional->id) ?>" class="btn btn-warning btn-block mb-2">
                    <i class="fas fa-edit mr-1"></i> Editar Profissional
                </a>
                <form action="<?= route_to('super.professionals.action', $professional->id) ?>" method="POST" class="mb-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    <button type="submit" class="btn btn-<?= $professional->active ? 'secondary' : 'success' ?> btn-block">
                        <i class="fas <?= $professional->iconToAction() ?> mr-1"></i> <?= $professional->textToAction() ?>
                    </button>
                </form>
                <form action="<?= route_to('super.professionals.delete', $professional->id) ?>" method="POST"
                      onsubmit="return confirm('Tem certeza que deseja excluir este profissional?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-outline-danger btn-block">
                        <i class="fas fa-trash mr-1"></i> Excluir Profissional
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
