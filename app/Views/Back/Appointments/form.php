<?php
/**
 * View: Back/Appointments/form.php
 * 
 * Formulário de Criação de Agendamento
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-calendar-plus mr-2"></i><?= esc($pageHeading ?? 'Novo Agendamento') ?>
    </h1>
    <a href="<?= route_to('super.appointments') ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Voltar
    </a>
</div>

<!-- Flash Messages -->
<?php if (session()->has('danger')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i><?= session('danger') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->has('errorsValidation')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>Erro de validação</h6>
        <ul class="mb-0">
            <?php foreach (session('errorsValidation') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-calendar-plus mr-2"></i>Dados do Agendamento
        </h6>
    </div>
    <div class="card-body">
        <form action="<?= route_to('super.appointments.create') ?>" method="POST">
            <?= csrf_field() ?>

            <h6 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Dados do Cliente</h6>
            
            <div class="row">
                <!-- Nome do Cliente -->
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="client_name">Nome do Cliente <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="client_name" 
                               name="client_name" 
                               value="<?= old('client_name') ?>"
                               placeholder="Nome completo do cliente"
                               maxlength="100"
                               required>
                    </div>
                </div>

                <!-- E-mail do Cliente -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="client_email">E-mail <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control" 
                               id="client_email" 
                               name="client_email" 
                               value="<?= old('client_email') ?>"
                               placeholder="email@exemplo.com"
                               maxlength="100"
                               required>
                    </div>
                </div>

                <!-- Telefone do Cliente -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="client_phone">Telefone</label>
                        <input type="text" 
                               class="form-control" 
                               id="client_phone" 
                               name="client_phone" 
                               value="<?= old('client_phone') ?>"
                               placeholder="(11) 99999-9999"
                               maxlength="20">
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="text-primary mb-3"><i class="fas fa-cog mr-2"></i>Detalhes do Agendamento</h6>

            <div class="row">
                <!-- Unidade -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="unit_id">Unidade <span class="text-danger">*</span></label>
                        <select class="form-control" id="unit_id" name="unit_id" required>
                            <option value="">Selecione a unidade...</option>
                            <?php foreach ($units as $id => $name): ?>
                                <option value="<?= $id ?>" <?= old('unit_id') == $id ? 'selected' : '' ?>>
                                    <?= esc($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Profissional -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="professional_id">Profissional <span class="text-danger">*</span></label>
                        <select class="form-control" id="professional_id" name="professional_id" required>
                            <option value="">Selecione o profissional...</option>
                            <?php foreach ($professionals as $id => $name): ?>
                                <option value="<?= $id ?>" <?= old('professional_id') == $id ? 'selected' : '' ?>>
                                    <?= esc($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Serviço -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="service_id">Serviço <span class="text-danger">*</span></label>
                        <select class="form-control" id="service_id" name="service_id" required>
                            <option value="">Selecione o serviço...</option>
                            <?php foreach ($services as $id => $name): ?>
                                <option value="<?= $id ?>" <?= old('service_id') == $id ? 'selected' : '' ?>>
                                    <?= esc($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="text-primary mb-3"><i class="fas fa-clock mr-2"></i>Data e Horário</h6>

            <div class="row">
                <!-- Data -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date">Data <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control" 
                               id="date" 
                               name="date" 
                               value="<?= old('date', date('Y-m-d')) ?>"
                               min="<?= date('Y-m-d') ?>"
                               required>
                    </div>
                </div>

                <!-- Horário Início -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_time">Horário Início <span class="text-danger">*</span></label>
                        <input type="time" 
                               class="form-control" 
                               id="start_time" 
                               name="start_time" 
                               value="<?= old('start_time', '09:00') ?>"
                               required>
                    </div>
                </div>

                <!-- Horário Término -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_time">Horário Término <span class="text-danger">*</span></label>
                        <input type="time" 
                               class="form-control" 
                               id="end_time" 
                               name="end_time" 
                               value="<?= old('end_time', '09:30') ?>"
                               required>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <?= $statusOptions ?>
                    </div>
                </div>
            </div>

            <!-- Observações -->
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea class="form-control" 
                          id="notes" 
                          name="notes" 
                          rows="3"
                          placeholder="Observações sobre o agendamento..."><?= old('notes') ?></textarea>
            </div>

            <hr>

            <!-- Botões -->
            <div class="d-flex justify-content-between">
                <a href="<?= route_to('super.appointments') ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Agendar
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
