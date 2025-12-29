<?php
/**
 * View: Back/Appointments/show.php
 * 
 * Exibição de Detalhes de um Agendamento
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-calendar-check mr-2"></i><?= esc($pageHeading ?? 'Detalhes do Agendamento') ?>
    </h1>
    <div>
        <?php if ($appointment->canEdit()): ?>
            <a href="<?= route_to('super.appointments.edit', $appointment->id) ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
        <?php endif; ?>
        <a href="<?= route_to('super.appointments') ?>" class="btn btn-secondary btn-sm">
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
        <!-- Card do Agendamento -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-check mr-2"></i>Agendamento #<?= $appointment->id ?>
                </h6>
                <?= $appointment->statusBadge() ?>
            </div>
            <div class="card-body">
                <!-- Data e Hora -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-light rounded p-3 text-center">
                            <h2 class="text-primary mb-0">
                                <i class="fas fa-calendar-day mr-2"></i>
                                <?= $appointment->dateFormatted('d/m/Y') ?>
                            </h2>
                            <h4 class="text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                <?= $appointment->timeRange() ?>
                                <small class="text-info">(<?= $appointment->durationFormatted() ?>)</small>
                            </h4>
                            <?php if ($appointment->isToday()): ?>
                                <span class="badge badge-primary p-2"><i class="fas fa-star mr-1"></i> HOJE</span>
                            <?php elseif ($appointment->isFuture()): ?>
                                <span class="badge badge-info p-2"><i class="fas fa-hourglass-half mr-1"></i> Futuro</span>
                            <?php else: ?>
                                <span class="badge badge-secondary p-2"><i class="fas fa-history mr-1"></i> Passado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Cliente -->
                <h6 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Dados do Cliente</h6>
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong class="text-gray-600">Nome:</strong><br>
                            <?= esc($appointment->client_name) ?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong class="text-gray-600">E-mail:</strong><br>
                            <a href="mailto:<?= esc($appointment->client_email) ?>">
                                <i class="fas fa-envelope mr-1"></i><?= esc($appointment->client_email) ?>
                            </a>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong class="text-gray-600">Telefone:</strong><br>
                            <?php if ($appointment->client_phone): ?>
                                <a href="tel:<?= esc($appointment->client_phone) ?>">
                                    <i class="fas fa-phone mr-1"></i><?= esc($appointment->client_phone) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Não informado</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <hr>

                <!-- Detalhes -->
                <h6 class="text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Detalhes do Atendimento</h6>
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong class="text-gray-600"><i class="fas fa-building mr-1"></i>Unidade:</strong><br>
                            <?php $unit = $appointment->getUnit(); ?>
                            <?php if ($unit): ?>
                                <a href="<?= route_to('super.units.show', $unit->id) ?>"><?= esc($unit->name) ?></a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong class="text-gray-600"><i class="fas fa-user-tie mr-1"></i>Profissional:</strong><br>
                            <?php $professional = $appointment->getProfessional(); ?>
                            <?php if ($professional): ?>
                                <a href="<?= route_to('super.professionals.show', $professional->id) ?>"><?= esc($professional->name) ?></a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong class="text-gray-600"><i class="fas fa-concierge-bell mr-1"></i>Serviço:</strong><br>
                            <?php $service = $appointment->getService(); ?>
                            <?php if ($service): ?>
                                <a href="<?= route_to('super.services.show', $service->id) ?>">
                                    <?= esc($service->name) ?>
                                </a>
                                <br><small class="text-muted"><?= $service->priceFormatted() ?></small>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <?php if ($appointment->notes): ?>
                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-sticky-note mr-2"></i>Observações</h6>
                <div class="bg-light rounded p-3">
                    <?= nl2br(esc($appointment->notes)) ?>
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
                    <i class="fas fa-tasks mr-2"></i>Status do Agendamento
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <?= $appointment->statusBadge() ?>
                </div>
                
                <!-- Ações de Status -->
                <div class="btn-group-vertical btn-block">
                    <?php if ($appointment->canConfirm()): ?>
                        <a href="<?= route_to('super.appointments.status', $appointment->id) ?>?status=confirmed" 
                           class="btn btn-info btn-sm mb-1">
                            <i class="fas fa-check-circle mr-1"></i> Confirmar
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($appointment->canComplete()): ?>
                        <a href="<?= route_to('super.appointments.status', $appointment->id) ?>?status=completed" 
                           class="btn btn-success btn-sm mb-1">
                            <i class="fas fa-check-double mr-1"></i> Concluir
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($appointment->canCancel()): ?>
                        <a href="<?= route_to('super.appointments.status', $appointment->id) ?>?status=cancelled" 
                           class="btn btn-warning btn-sm mb-1"
                           onclick="return confirm('Deseja cancelar este agendamento?');">
                            <i class="fas fa-times-circle mr-1"></i> Cancelar
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($appointment->status === 'confirmed' || $appointment->status === 'scheduled'): ?>
                        <a href="<?= route_to('super.appointments.status', $appointment->id) ?>?status=no_show" 
                           class="btn btn-secondary btn-sm"
                           onclick="return confirm('Marcar cliente como não compareceu?');">
                            <i class="fas fa-user-slash mr-1"></i> Não Compareceu
                        </a>
                    <?php endif; ?>
                </div>
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
                    <strong class="text-gray-600">ID:</strong> #<?= $appointment->id ?>
                </p>
                <p class="mb-2">
                    <strong class="text-gray-600">Criado em:</strong><br>
                    <i class="fas fa-calendar-plus mr-1 text-muted"></i>
                    <?= $appointment->created_at ? $appointment->created_at->format('d/m/Y H:i') : '-' ?>
                </p>
                <p class="mb-0">
                    <strong class="text-gray-600">Atualizado em:</strong><br>
                    <i class="fas fa-calendar-check mr-1 text-muted"></i>
                    <?= $appointment->updated_at ? $appointment->updated_at->format('d/m/Y H:i') : '-' ?>
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
                <?php if ($appointment->canEdit()): ?>
                    <a href="<?= route_to('super.appointments.edit', $appointment->id) ?>" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit mr-1"></i> Editar Agendamento
                    </a>
                <?php endif; ?>
                
                <a href="<?= route_to('super.appointments.new') ?>" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-plus mr-1"></i> Novo Agendamento
                </a>
                
                <form action="<?= route_to('super.appointments.delete', $appointment->id) ?>" method="POST"
                      onsubmit="return confirm('Tem certeza que deseja excluir este agendamento?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-outline-danger btn-block">
                        <i class="fas fa-trash mr-1"></i> Excluir Agendamento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
