<?php
/**
 * View: Back/Professionals/form.php
 * 
 * Formulário de Criação de Profissional
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-plus mr-2"></i><?= esc($pageHeading ?? 'Novo Profissional') ?>
    </h1>
    <a href="<?= route_to('super.professionals') ?>" class="btn btn-secondary btn-sm">
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
            <i class="fas fa-user-plus mr-2"></i>Dados do Profissional
        </h6>
    </div>
    <div class="card-body">
        <form action="<?= route_to('super.professionals.create') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row">
                <!-- Nome -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               value="<?= old('name') ?>"
                               placeholder="Nome do profissional"
                               maxlength="100"
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
                               value="<?= old('email') ?>"
                               placeholder="email@exemplo.com"
                               maxlength="100"
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
                               value="<?= old('phone') ?>"
                               placeholder="(11) 99999-9999"
                               maxlength="20">
                    </div>
                </div>

                <!-- Especialidade -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="specialty">Especialidade</label>
                        <input type="text" 
                               class="form-control" 
                               id="specialty" 
                               name="specialty" 
                               value="<?= old('specialty') ?>"
                               placeholder="Ex: Clínico Geral, Dentista"
                               maxlength="100">
                    </div>
                </div>

                <!-- Unidade -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="unit_id">Unidade</label>
                        <select class="form-control" id="unit_id" name="unit_id">
                            <option value="">Selecione uma unidade...</option>
                            <?php foreach ($units as $id => $name): ?>
                                <option value="<?= $id ?>" <?= old('unit_id') == $id ? 'selected' : '' ?>>
                                    <?= esc($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Biografia -->
            <div class="form-group">
                <label for="bio">Biografia / Descrição</label>
                <textarea class="form-control" 
                          id="bio" 
                          name="bio" 
                          rows="3"
                          placeholder="Breve descrição sobre o profissional..."><?= old('bio') ?></textarea>
            </div>

            <hr>
            <h6 class="text-primary mb-3"><i class="fas fa-concierge-bell mr-2"></i>Serviços Realizados</h6>

            <!-- Multi-select de Serviços -->
            <div class="form-group">
                <label for="services">Serviços que o Profissional Realiza</label>
                <select class="form-control select2-services" 
                        id="services" 
                        name="services[]" 
                        multiple="multiple"
                        data-placeholder="Selecione os serviços...">
                    <?php foreach ($availableServices as $id => $label): ?>
                        <option value="<?= $id ?>" <?= in_array($id, old('services') ?? []) ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Selecione os serviços que este profissional está apto a realizar
                </small>
            </div>

            <hr>

            <!-- Status Ativo -->
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" 
                           class="custom-control-input" 
                           id="active" 
                           name="active" 
                           value="1"
                           <?= old('active', '1') == '1' ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="active">
                        Profissional Ativo
                    </label>
                    <small class="form-text text-muted">
                        Profissionais inativos não aparecem para agendamento
                    </small>
                </div>
            </div>

            <hr>

            <!-- Botões -->
            <div class="d-flex justify-content-between">
                <a href="<?= route_to('super.professionals') ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Cadastrar
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
