<?php
/**
 * View: Back/Units/show.php
 * 
 * Exibição de Detalhes de uma Unidade
 * 
 * =========================================================================
 * VARIÁVEIS DO CONTROLLER
 * =========================================================================
 * 
 * - $title (string): Título da página
 * - $pageHeading (string): Título exibido na página
 * - $unit (Unit): Entity da unidade
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-building mr-2"></i><?= esc($pageHeading ?? $unit->name) ?>
    </h1>
    <div>
        <a href="<?= route_to('super.units.edit', $unit->id) ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-edit mr-1"></i> Editar
        </a>
        <a href="<?= route_to('super.units') ?>" class="btn btn-secondary btn-sm">
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
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-2"></i>Informações da Unidade
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong class="text-gray-600">Nome:</strong><br>
                            <?= esc($unit->name) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong class="text-gray-600">E-mail:</strong><br>
                            <a href="mailto:<?= esc($unit->email) ?>"><?= esc($unit->email) ?></a>
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong class="text-gray-600">Telefone:</strong><br>
                            <?= esc($unit->phone) ?: '<span class="text-muted">Não informado</span>' ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong class="text-gray-600">Coordenador:</strong><br>
                            <?= esc($unit->coordinator) ?: '<span class="text-muted">Não informado</span>' ?>
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <p class="mb-2">
                            <strong class="text-gray-600">Endereço:</strong><br>
                            <?= esc($unit->address) ?: '<span class="text-muted">Não informado</span>' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Horário de Funcionamento -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clock mr-2"></i>Horário de Funcionamento
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border rounded p-3 mb-3">
                            <i class="fas fa-sign-in-alt fa-2x text-success mb-2"></i>
                            <h6 class="text-gray-600 mb-1">Início</h6>
                            <span class="h4"><?= esc($unit->start_time) ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 mb-3">
                            <i class="fas fa-sign-out-alt fa-2x text-danger mb-2"></i>
                            <h6 class="text-gray-600 mb-1">Término</h6>
                            <span class="h4"><?= esc($unit->end_time) ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 mb-3">
                            <i class="fas fa-hourglass-half fa-2x text-info mb-2"></i>
                            <h6 class="text-gray-600 mb-1">Tempo de Atendimento</h6>
                            <span class="h4"><?= esc($unit->service_time) ?> min</span>
                        </div>
                    </div>
                </div>
                
                <?php if (method_exists($unit, 'getWorkingHours')): ?>
                <p class="text-center text-muted mb-0">
                    <i class="fas fa-business-time mr-1"></i>
                    Expediente: <?= $unit->getWorkingHours() ?>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Serviços Associados -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-concierge-bell mr-2"></i>Serviços Oferecidos
                </h6>
            </div>
            <div class="card-body">
                <?php
                // Decodifica os serviços associados (JSON para array)
                $serviceIds = is_string($unit->services) ? json_decode($unit->services, true) : $unit->services;
                $serviceIds = is_array($serviceIds) ? $serviceIds : [];
                
                if (empty($serviceIds)):
                ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p class="mb-2">Nenhum serviço associado a esta unidade.</p>
                        <a href="<?= route_to('super.units.edit', $unit->id) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Adicionar Serviços
                        </a>
                    </div>
                <?php else:
                    // Busca os serviços pelo IDs
                    $serviceModel = model(\App\Models\ServiceModel::class);
                    $services = $serviceModel->findByIds($serviceIds);
                ?>
                    <div class="row">
                        <?php foreach ($services as $service): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-left-primary">
                                    <div class="card-body py-2 px-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 font-weight-bold text-primary">
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
                                        <?php if ($service->description): ?>
                                            <p class="text-muted small mb-0 mt-2"><?= $service->shortDescription(80) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-right mt-2">
                        <small class="text-muted">
                            <i class="fas fa-layer-group mr-1"></i>
                            <?= count($services) ?> serviço<?= count($services) > 1 ? 's' : '' ?> associado<?= count($services) > 1 ? 's' : '' ?>
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar com Status e Datas -->
    <div class="col-lg-4">
        <!-- Status -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-toggle-on mr-2"></i>Status
                </h6>
            </div>
            <div class="card-body text-center">
                <?php if ($unit->active): ?>
                    <span class="badge badge-success p-3 mb-2" style="font-size: 1rem;">
                        <i class="fas fa-check-circle mr-1"></i> ATIVA
                    </span>
                    <p class="text-muted mb-0">Esta unidade está disponível para agendamentos.</p>
                <?php else: ?>
                    <span class="badge badge-secondary p-3 mb-2" style="font-size: 1rem;">
                        <i class="fas fa-ban mr-1"></i> INATIVA
                    </span>
                    <p class="text-muted mb-0">Esta unidade não aparece para agendamentos.</p>
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
                    <?= $unit->created_at ? $unit->created_at->format('d/m/Y H:i') : '-' ?>
                </p>
                <p class="mb-0">
                    <strong class="text-gray-600">Atualizado em:</strong><br>
                    <i class="fas fa-calendar-check mr-1 text-muted"></i>
                    <?= $unit->updated_at ? $unit->updated_at->format('d/m/Y H:i') : '-' ?>
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
                <a href="<?= route_to('super.units.edit', $unit->id) ?>" class="btn btn-warning btn-block mb-2">
                    <i class="fas fa-edit mr-1"></i> Editar Unidade
                </a>
                <button type="button" class="btn btn-outline-danger btn-block" 
                        onclick="if(confirm('Tem certeza que deseja excluir esta unidade?')) { alert('Em desenvolvimento'); }">
                    <i class="fas fa-trash mr-1"></i> Excluir Unidade
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
