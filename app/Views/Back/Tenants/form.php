<?php
/**
 * View: Back/Tenants/form.php
 * 
 * Formulário de criação/edição de Empresa (Tenant)
 * 
 * @var string $title Título da página
 * @var string $pageHeading Título exibido na página
 * @var object|null $tenant Dados da empresa (null para novo)
 * @var bool $isEdit Se é modo edição
 */

$isEdit = isset($tenant) && $tenant !== null;
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?= esc($pageHeading ?? ($isEdit ? 'Editar Empresa' : 'Nova Empresa')) ?>
    </h1>
    <a href="<?= route_to('super.tenants') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar
    </a>
</div>

<!-- Flash Messages -->
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i><?= session('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Validation Errors -->
<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <strong>Erros de validação:</strong>
        <ul class="mb-0 mt-2">
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
<form action="<?= $isEdit ? route_to('super.tenants.update', $tenant->id) : route_to('super.tenants.create') ?>" 
      method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="row">
        <!-- Informações Básicas -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building mr-2"></i>Informações Básicas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Nome da Empresa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= old('name', $tenant->name ?? '') ?>" 
                                       placeholder="Nome da empresa" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="slug">Slug (URL)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">/t/</span>
                                    </div>
                                    <input type="text" class="form-control" id="slug" name="slug" 
                                           value="<?= old('slug', $tenant->slug ?? '') ?>" 
                                           placeholder="minha-empresa" pattern="[a-z0-9\-]+">
                                </div>
                                <small class="text-muted">Gerado automaticamente se vazio</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email', $tenant->email ?? '') ?>" 
                                       placeholder="contato@empresa.com">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="phone">Telefone</label>
                                <input type="text" class="form-control phone-mask" id="phone" name="phone" 
                                       value="<?= old('phone', $tenant->phone ?? '') ?>" 
                                       placeholder="(00) 00000-0000">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="document">CNPJ</label>
                                <input type="text" class="form-control cnpj-mask" id="document" name="document" 
                                       value="<?= old('document', $tenant->document ?? '') ?>" 
                                       placeholder="00.000.000/0000-00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="domain">Domínio Personalizado</label>
                        <input type="text" class="form-control" id="domain" name="domain" 
                               value="<?= old('domain', $tenant->domain ?? '') ?>" 
                               placeholder="www.minhaempresa.com.br">
                        <small class="text-muted">Opcional. Permite acesso por domínio próprio</small>
                    </div>
                </div>
            </div>

            <!-- Endereço -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map-marker-alt mr-2"></i>Endereço
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="address">Endereço</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?= old('address', $tenant->address ?? '') ?>" 
                                       placeholder="Rua, número, complemento">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="zip_code">CEP</label>
                                <input type="text" class="form-control cep-mask" id="zip_code" name="zip_code" 
                                       value="<?= old('zip_code', $tenant->zip_code ?? '') ?>" 
                                       placeholder="00000-000">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="city">Cidade</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?= old('city', $tenant->city ?? '') ?>" 
                                       placeholder="Cidade">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="state">Estado</label>
                                <select class="form-control" id="state" name="state">
                                    <option value="">Selecione...</option>
                                    <?php
                                    $states = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
                                    foreach ($states as $uf):
                                    ?>
                                        <option value="<?= $uf ?>" <?= old('state', $tenant->state ?? '') === $uf ? 'selected' : '' ?>>
                                            <?= $uf ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Plano e Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog mr-2"></i>Plano e Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="plan">Plano <span class="text-danger">*</span></label>
                        <select class="form-control" id="plan" name="plan" required>
                            <option value="free" <?= old('plan', $tenant->plan ?? 'free') === 'free' ? 'selected' : '' ?>>
                                Gratuito
                            </option>
                            <option value="basic" <?= old('plan', $tenant->plan ?? '') === 'basic' ? 'selected' : '' ?>>
                                Básico
                            </option>
                            <option value="professional" <?= old('plan', $tenant->plan ?? '') === 'professional' ? 'selected' : '' ?>>
                                Profissional
                            </option>
                            <option value="enterprise" <?= old('plan', $tenant->plan ?? '') === 'enterprise' ? 'selected' : '' ?>>
                                Enterprise
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active" <?= old('status', $tenant->status ?? 'active') === 'active' ? 'selected' : '' ?>>
                                Ativo
                            </option>
                            <option value="trial" <?= old('status', $tenant->status ?? '') === 'trial' ? 'selected' : '' ?>>
                                Trial
                            </option>
                            <option value="suspended" <?= old('status', $tenant->status ?? '') === 'suspended' ? 'selected' : '' ?>>
                                Suspenso
                            </option>
                            <option value="cancelled" <?= old('status', $tenant->status ?? '') === 'cancelled' ? 'selected' : '' ?>>
                                Cancelado
                            </option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="plan_expires_at">Expira em</label>
                                <input type="date" class="form-control" id="plan_expires_at" name="plan_expires_at" 
                                       value="<?= old('plan_expires_at', isset($tenant->plan_expires_at) ? date('Y-m-d', strtotime($tenant->plan_expires_at)) : '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="trial_ends_at">Trial até</label>
                                <input type="date" class="form-control" id="trial_ends_at" name="trial_ends_at" 
                                       value="<?= old('trial_ends_at', isset($tenant->trial_ends_at) ? date('Y-m-d', strtotime($tenant->trial_ends_at)) : '') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Limites -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sliders-h mr-2"></i>Limites
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="max_users">Máx. Usuários</label>
                        <input type="number" class="form-control" id="max_users" name="max_users" 
                               value="<?= old('max_users', $tenant->max_users ?? 5) ?>" min="1">
                    </div>
                    <div class="form-group">
                        <label for="max_units">Máx. Unidades</label>
                        <input type="number" class="form-control" id="max_units" name="max_units" 
                               value="<?= old('max_units', $tenant->max_units ?? 1) ?>" min="1">
                    </div>
                    <div class="form-group">
                        <label for="max_appointments_month">Máx. Agendamentos/Mês</label>
                        <input type="number" class="form-control" id="max_appointments_month" name="max_appointments_month" 
                               value="<?= old('max_appointments_month', $tenant->max_appointments_month ?? 100) ?>" min="1">
                        <small class="text-muted">0 = ilimitado</small>
                    </div>
                </div>
            </div>

            <!-- Recursos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-toggle-on mr-2"></i>Recursos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="whatsapp_enabled" 
                               name="whatsapp_enabled" value="1"
                               <?= old('whatsapp_enabled', $tenant->whatsapp_enabled ?? 0) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="whatsapp_enabled">
                            <i class="fab fa-whatsapp text-success mr-1"></i>WhatsApp
                        </label>
                    </div>
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="payments_enabled" 
                               name="payments_enabled" value="1"
                               <?= old('payments_enabled', $tenant->payments_enabled ?? 0) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="payments_enabled">
                            <i class="fas fa-credit-card text-primary mr-1"></i>Pagamentos Online
                        </label>
                    </div>
                </div>
            </div>

            <!-- Logo -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-image mr-2"></i>Logo
                    </h6>
                </div>
                <div class="card-body text-center">
                    <?php if ($isEdit && $tenant->logo): ?>
                        <img src="<?= base_url('uploads/tenants/' . $tenant->logo) ?>" 
                             alt="Logo atual" class="img-thumbnail mb-3" style="max-height: 150px;">
                    <?php endif; ?>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                        <label class="custom-file-label" for="logo">Escolher arquivo...</label>
                    </div>
                    <small class="text-muted">PNG ou JPG. Máx. 2MB</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="<?= route_to('super.tenants') ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i><?= $isEdit ? 'Atualizar' : 'Cadastrar' ?>
                </button>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Input Masks -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(document).ready(function() {
    // Máscaras
    $('.phone-mask').mask('(00) 00000-0000');
    $('.cnpj-mask').mask('00.000.000/0000-00');
    $('.cep-mask').mask('00000-000');
    
    // Auto-gerar slug do nome
    $('#name').on('blur', function() {
        if ($('#slug').val() === '') {
            var slug = $(this).val()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)/g, '');
            $('#slug').val(slug);
        }
    });
    
    // Custom file input label
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Escolher arquivo...');
    });
    
    // CEP lookup
    $('#zip_code').on('blur', function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            $.get('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
                if (!data.erro) {
                    $('#address').val(data.logradouro);
                    $('#city').val(data.localidade);
                    $('#state').val(data.uf);
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
