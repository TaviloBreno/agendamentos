<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-calendar-check mr-2"></i>
        <?= esc($pageHeading ?? 'Meus Agendamentos') ?>
    </h1>
    <a href="<?= route_to('admin.my.appointments.new') ?>" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm mr-1"></i> Novo Agendamento
    </a>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtrar Agendamentos</h6>
    </div>
    <div class="card-body">
        <form method="get" class="row">
            <div class="col-md-3 mb-2">
                <label class="small text-muted">Status</label>
                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="scheduled">Agendado</option>
                    <option value="confirmed">Confirmado</option>
                    <option value="completed">Concluído</option>
                    <option value="cancelled">Cancelado</option>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="small text-muted">Período</label>
                <select name="period" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="upcoming">Próximos</option>
                    <option value="past">Passados</option>
                    <option value="today">Hoje</option>
                    <option value="week">Esta semana</option>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Agendamentos -->
<div class="card shadow mb-4">
    <div class="card-body">
        <?php if (empty($appointments)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Nenhum agendamento encontrado</h5>
                <p class="text-muted">Você ainda não possui agendamentos registrados.</p>
                <a href="<?= route_to('admin.my.appointments.new') ?>" class="btn btn-primary mt-2">
                    <i class="fas fa-plus mr-1"></i> Fazer um Agendamento
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="appointmentsTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Serviço</th>
                            <th>Profissional</th>
                            <th>Unidade</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <?php
                            $service = $services[$appointment->service_id] ?? null;
                            $professional = $professionals[$appointment->professional_id] ?? null;
                            $unit = $units[$appointment->unit_id] ?? null;
                            ?>
                            <tr>
                                <td>
                                    <strong><?= date('d/m/Y', strtotime($appointment->date)) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= date('l', strtotime($appointment->date)) ?></small>
                                </td>
                                <td>
                                    <i class="far fa-clock text-muted mr-1"></i>
                                    <?= date('H:i', strtotime($appointment->start_time)) ?>
                                    <span class="text-muted">-</span>
                                    <?= date('H:i', strtotime($appointment->end_time)) ?>
                                </td>
                                <td><?= esc($service->name ?? 'N/A') ?></td>
                                <td><?= esc($professional->name ?? 'N/A') ?></td>
                                <td><?= esc($unit->name ?? 'N/A') ?></td>
                                <td>
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
                                    <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= route_to('admin.my.appointments.show', $appointment->id) ?>" 
                                       class="btn btn-sm btn-info" title="Ver detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
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
                                              method="post" class="d-inline" 
                                              onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Cancelar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    $('#appointmentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
        },
        order: [[0, 'desc']],
        pageLength: 10
    });
});
</script>
<?= $this->endSection() ?>
