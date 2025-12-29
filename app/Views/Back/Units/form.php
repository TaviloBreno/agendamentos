<?php
/**
 * View: Back/Units/form.php
 * 
 * Formulário de Criação/Edição de Unidades
 * 
 * =========================================================================
 * VARIÁVEIS DO CONTROLLER
 * =========================================================================
 * 
 * - $title (string): Título da página
 * - $pageHeading (string): Título exibido na página
 * - $unit (Unit|null): Entity para edição (null = criação)
 * 
 * =========================================================================
 * FORMULÁRIO REUTILIZÁVEL
 * =========================================================================
 * 
 * Este formulário serve para CRIAR e EDITAR:
 * - Se $unit existe → modo edição (PUT)
 * - Se $unit não existe → modo criação (POST)
 * 
 * O helper form_open() gera o CSRF token automaticamente.
 */

// Detecta modo: edição ou criação
$isEdit = isset($unit) && $unit !== null;
$formAction = $isEdit 
    ? route_to('super.units.update', $unit->id) 
    : route_to('super.units.create');
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?= esc($pageHeading ?? ($isEdit ? 'Editar Unidade' : 'Nova Unidade')) ?>
    </h1>
    <a href="<?= route_to('super.units') ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Voltar
    </a>
</div>

<!-- Mensagens de Erro de Validação -->
<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>Erro de validação</h6>
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
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
            <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?> mr-2"></i>
            <?= $isEdit ? 'Editar Dados da Unidade' : 'Cadastrar Nova Unidade' ?>
        </h6>
    </div>
    <div class="card-body">
        <form action="<?= $formAction ?>" method="POST">
            <?= csrf_field() ?>
            
            <?php if ($isEdit): ?>
                <!-- Spoofing para PUT -->
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="row">
                <!-- Nome -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome da Unidade <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               value="<?= old('name', $unit->name ?? '') ?>"
                               placeholder="Ex: Unidade Centro"
                               required>
                    </div>
                </div>

                <!-- E-mail -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">E-mail <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?= old('email', $unit->email ?? '') ?>"
                               placeholder="contato@unidade.com"
                               required>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Telefone -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="text" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               value="<?= old('phone', $unit->phone ?? '') ?>"
                               placeholder="(11) 99999-9999">
                    </div>
                </div>

                <!-- Coordenador -->
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="coordinator">Coordenador/Responsável</label>
                        <input type="text" 
                               class="form-control" 
                               id="coordinator" 
                               name="coordinator" 
                               value="<?= old('coordinator', $unit->coordinator ?? '') ?>"
                               placeholder="Nome do responsável pela unidade">
                    </div>
                </div>
            </div>

            <!-- Endereço -->
            <div class="form-group">
                <label for="address">Endereço</label>
                <textarea class="form-control" 
                          id="address" 
                          name="address" 
                          rows="2"
                          placeholder="Rua, número, bairro, cidade - UF"><?= old('address', $unit->address ?? '') ?></textarea>
            </div>

            <hr>
            <h6 class="text-primary mb-3"><i class="fas fa-clock mr-2"></i>Horário de Funcionamento</h6>

            <div class="row">
                <!-- Horário de Início -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="start_time">Horário de Início <span class="text-danger">*</span></label>
                        <input type="time" 
                               class="form-control" 
                               id="start_time" 
                               name="start_time" 
                               value="<?= old('start_time', $unit->start_time ?? '08:00') ?>"
                               required>
                    </div>
                </div>

                <!-- Horário de Término -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="end_time">Horário de Término <span class="text-danger">*</span></label>
                        <input type="time" 
                               class="form-control" 
                               id="end_time" 
                               name="end_time" 
                               value="<?= old('end_time', $unit->end_time ?? '18:00') ?>"
                               required>
                    </div>
                </div>

                <!-- Tempo de Atendimento -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="service_time">Tempo de Atendimento <span class="text-danger">*</span></label>
                        <?= $timesInterval ?? '<input type="number" class="form-control" id="service_time" name="service_time" value="' . old('service_time', $unit->service_time ?? '30') . '" min="5" max="240" placeholder="30">' ?>
                        <small class="form-text text-muted">Duração padrão de cada atendimento</small>
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="text-primary mb-3"><i class="fas fa-concierge-bell mr-2"></i>Serviços Oferecidos</h6>

            <!-- Multi-select de Serviços -->
            <div class="form-group">
                <label for="services">Serviços Disponíveis nesta Unidade</label>
                <?php
                $selectedServices = old('services');
                if ($selectedServices === null && isset($unit->services)) {
                    $decoded = is_string($unit->services) ? json_decode($unit->services, true) : $unit->services;
                    $selectedServices = is_array($decoded) ? $decoded : [];
                }
                $selectedServices = $selectedServices ?? [];
                ?>
                <select class="form-control select2-services" 
                        id="services" 
                        name="services[]" 
                        multiple="multiple"
                        data-placeholder="Selecione os serviços oferecidos nesta unidade">
                    <?php if (isset($availableServices)): ?>
                        <?php foreach ($availableServices as $id => $label): ?>
                            <option value="<?= $id ?>" <?= in_array($id, $selectedServices) ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Selecione os serviços que esta unidade pode oferecer aos clientes
                </small>
            </div>

            <hr>

            <!-- Status Ativo -->
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" 
                           class="custom-control-input" 
                           id="active" 
                           name="active" 
                           value="1"
                           <?= old('active', $unit->active ?? 1) ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="active">
                        Unidade Ativa
                    </label>
                    <small class="form-text text-muted">
                        Unidades inativas não aparecem para agendamento
                    </small>
                </div>
            </div>

            <hr>

            <!-- Botões -->
            <div class="d-flex justify-content-between">
                <a href="<?= route_to('super.units') ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> 
                    <?= $isEdit ? 'Atualizar' : 'Cadastrar' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
