<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-calendar-check mr-2"></i>
        <?= esc($pageHeading ?? 'Detalhes do Agendamento') ?>
    </h1>
    <a href="<?= route_to('admin.my.appointments') ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Voltar
    </a>
</div>

<div class="row">
    <!-- Informações Principais -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-2"></i>
                    Informações do Agendamento
                </h6>
                <?php
                $statusClasses = [
                    'scheduled'  => 'badge-info',
                    'confirmed'  => 'badge-primary',
                    'completed'  => 'badge-success',
                    'cancelled'  => 'badge-danger',
                    'no_show'    => 'badge-warning',
                ];
                $statusLabels = [
                    'scheduled'  => 'Agendado',
                    'confirmed'  => 'Confirmado',
                    'completed'  => 'Concluído',
                    'cancelled'  => 'Cancelado',
                    'no_show'    => 'Não compareceu',
                ];
                $badgeClass = $statusClasses[$appointment->status] ?? 'badge-secondary';
                $statusLabel = $statusLabels[$appointment->status] ?? $appointment->status;
                ?>
                <span class="badge <?= $badgeClass ?> px-3 py-2"><?= $statusLabel ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h6 class="text-muted small text-uppercase mb-1">Data</h6>
                        <p class="h5 mb-0">
                            <i class="far fa-calendar-alt text-primary mr-2"></i>
                            <?= date('d/m/Y', strtotime($appointment->date)) ?>
                            <small class="text-muted">(<?= strftime('%A', strtotime($appointment->date)) ?>)</small>
                        </p>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h6 class="text-muted small text-uppercase mb-1">Horário</h6>
                        <p class="h5 mb-0">
                            <i class="far fa-clock text-primary mr-2"></i>
                            <?= date('H:i', strtotime($appointment->start_time)) ?> 
                            <span class="text-muted">às</span> 
                            <?= date('H:i', strtotime($appointment->end_time)) ?>
                        </p>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h6 class="text-muted small text-uppercase mb-1">Serviço</h6>
                        <p class="h5 mb-0">
                            <i class="fas fa-concierge-bell text-success mr-2"></i>
                            <?= esc($service->name ?? 'N/A') ?>
                        </p>
                        <?php if ($service && $service->duration): ?>
                            <small class="text-muted">
                                <i class="fas fa-hourglass-half mr-1"></i>
                                Duração: <?= $service->duration ?> min
                            </small>
                        <?php endif; ?>
                        <?php if ($service && $service->price): ?>
                            <br>
                            <small class="text-success font-weight-bold">
                                <i class="fas fa-tag mr-1"></i>
                                R$ <?= number_format($service->price, 2, ',', '.') ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h6 class="text-muted small text-uppercase mb-1">Profissional</h6>
                        <p class="h5 mb-0">
                            <i class="fas fa-user-tie text-info mr-2"></i>
                            <?= esc($professional->name ?? 'N/A') ?>
                        </p>
                        <?php if ($professional && $professional->specialty): ?>
                            <small class="text-muted"><?= esc($professional->specialty) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <h6 class="text-muted small text-uppercase mb-1">Unidade</h6>
                        <p class="h5 mb-0">
                            <i class="fas fa-building text-warning mr-2"></i>
                            <?= esc($unit->name ?? 'N/A') ?>
                        </p>
                        <?php if ($unit && $unit->address): ?>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?= esc($unit->address) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (!empty($appointment->notes)): ?>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted small text-uppercase mb-1">Observações</h6>
                            <p class="mb-0"><?= nl2br(esc($appointment->notes)) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Ações -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-cogs mr-2"></i>
                    Ações
                </h6>
            </div>
            <div class="card-body">
                <?php 
                $canCancel = !in_array($appointment->status, ['cancelled', 'completed', 'no_show']);
                $appointmentDate = new \DateTime($appointment->date . ' ' . $appointment->start_time);
                $now = new \DateTime();
                if ($appointmentDate < $now) {
                    $canCancel = false;
                }
                ?>
                
                <?php if ($canCancel): ?>
                    <form action="<?= route_to('admin.my.appointments.cancel', $appointment->id) ?>" 
                          method="post" 
                          onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-times mr-1"></i> Cancelar Agendamento
                        </button>
                    </form>
                    <hr>
                <?php endif; ?>
                
                <a href="<?= route_to('schedule') ?>" class="btn btn-primary btn-block">
                    <i class="fas fa-plus mr-1"></i> Novo Agendamento
                </a>
                
                <a href="<?= route_to('admin.my.appointments') ?>" class="btn btn-secondary btn-block">
                    <i class="fas fa-list mr-1"></i> Voltar à Lista
                </a>
            </div>
        </div>
        
        <!-- Informações do Registro -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-history mr-2"></i>
                    Informações do Registro
                </h6>
            </div>
            <div class="card-body small">
                <p class="mb-2">
                    <strong>Criado em:</strong><br>
                    <?= $appointment->created_at ? $appointment->created_at->format('d/m/Y H:i') : 'N/A' ?>
                </p>
                <p class="mb-0">
                    <strong>Última atualização:</strong><br>
                    <?= $appointment->updated_at ? $appointment->updated_at->format('d/m/Y H:i') : 'N/A' ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
