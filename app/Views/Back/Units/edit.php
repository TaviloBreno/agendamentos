<?php
/**
 * View: Back/Units/edit.php
 * 
 * Formulário de Edição de Unidade
 * 
 * =========================================================================
 * VARIÁVEIS DO CONTROLLER
 * =========================================================================
 * 
 * - $title (string): Título da página
 * - $pageHeading (string): Título exibido na página
 * - $unit (Unit): Entity com dados da unidade a ser editada
 * 
 * =========================================================================
 * CSRF PROTECTION
 * =========================================================================
 * 
 * O token CSRF é OBRIGATÓRIO para formulários POST/PUT/DELETE.
 * Configurado em app/Config/Filters.php ($methods).
 * 
 * Sem <?= csrf_field() ?>, a requisição será bloqueada com erro 403.
 * 
 * =========================================================================
 * METHOD SPOOFING
 * =========================================================================
 * 
 * HTML forms só suportam GET e POST nativamente.
 * Para usar PUT/DELETE, usamos "method spoofing":
 * 
 * <input type="hidden" name="_method" value="PUT">
 * 
 * O CodeIgniter intercepta esse campo e trata como PUT real.
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-edit mr-2"></i><?= esc($pageHeading ?? 'Editar Unidade') ?>
    </h1>
    <a href="<?= route_to('super.units') ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Voltar para Lista
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
            <i class="fas fa-building mr-2"></i>Dados da Unidade
        </h6>
    </div>
    <div class="card-body">
        <!-- 
            FORMULÁRIO DE EDIÇÃO
            ====================
            - action: rota PUT para update
            - method: POST (com _method=PUT para spoofing)
            - csrf_field(): token obrigatório
        -->
        <form action="<?= route_to('super.units.update', $unit->id) ?>" method="POST">
            
            <!-- CSRF Token - OBRIGATÓRIO -->
            <?= csrf_field() ?>
            
            <!-- Method Spoofing - Transforma POST em PUT -->
            <input type="hidden" name="_method" value="PUT">

            <div class="row">
                <!-- Nome -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">
                            Nome da Unidade <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= old('name', esc($unit->name)) ?>"
                               placeholder="Ex: Unidade Centro"
                               maxlength="70"
                               required>
                        <?php if (session('errors.name')): ?>
                            <div class="invalid-feedback"><?= session('errors.name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- E-mail -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">
                            E-mail <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= old('email', esc($unit->email)) ?>"
                               placeholder="contato@unidade.com"
                               maxlength="100"
                               required>
                        <?php if (session('errors.email')): ?>
                            <div class="invalid-feedback"><?= session('errors.email') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Telefone -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="phone">
                            Telefone <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= session('errors.phone') ? 'is-invalid' : '' ?>" 
                               id="phone" 
                               name="phone" 
                               value="<?= old('phone', esc($unit->phone)) ?>"
                               placeholder="(11) 99999-9999"
                               maxlength="14"
                               required>
                        <?php if (session('errors.phone')): ?>
                            <div class="invalid-feedback"><?= session('errors.phone') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Coordenador -->
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="coordinator">
                            Coordenador/Responsável <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= session('errors.coordinator') ? 'is-invalid' : '' ?>" 
                               id="coordinator" 
                               name="coordinator" 
                               value="<?= old('coordinator', esc($unit->coordinator)) ?>"
                               placeholder="Nome do responsável pela unidade"
                               maxlength="70"
                               required>
                        <?php if (session('errors.coordinator')): ?>
                            <div class="invalid-feedback"><?= session('errors.coordinator') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Endereço -->
            <div class="form-group">
                <label for="address">
                    Endereço <span class="text-danger">*</span>
                </label>
                <textarea class="form-control <?= session('errors.address') ? 'is-invalid' : '' ?>" 
                          id="address" 
                          name="address" 
                          rows="2"
                          maxlength="255"
                          placeholder="Rua, número, bairro, cidade - UF"
                          required><?= old('address', esc($unit->address)) ?></textarea>
                <?php if (session('errors.address')): ?>
                    <div class="invalid-feedback"><?= session('errors.address') ?></div>
                <?php endif; ?>
            </div>

            <hr>
            <h6 class="text-primary mb-3">
                <i class="fas fa-clock mr-2"></i>Horário de Funcionamento
            </h6>

            <div class="row">
                <!-- Horário de Início -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="start_time">
                            Horário de Início <span class="text-danger">*</span>
                        </label>
                        <input type="time" 
                               class="form-control <?= session('errors.start_time') ? 'is-invalid' : '' ?>" 
                               id="start_time" 
                               name="start_time" 
                               value="<?= old('start_time', esc($unit->start_time)) ?>"
                               required>
                        <?php if (session('errors.start_time')): ?>
                            <div class="invalid-feedback"><?= session('errors.start_time') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Horário de Término -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="end_time">
                            Horário de Término <span class="text-danger">*</span>
                        </label>
                        <input type="time" 
                               class="form-control <?= session('errors.end_time') ? 'is-invalid' : '' ?>" 
                               id="end_time" 
                               name="end_time" 
                               value="<?= old('end_time', esc($unit->end_time)) ?>"
                               required>
                        <?php if (session('errors.end_time')): ?>
                            <div class="invalid-feedback"><?= session('errors.end_time') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tempo de Atendimento -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="service_time">
                            Tempo de Atendimento <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= session('errors.service_time') ? 'is-invalid' : '' ?>" 
                               id="service_time" 
                               name="service_time" 
                               value="<?= old('service_time', esc($unit->service_time)) ?>"
                               placeholder="30 min"
                               maxlength="20"
                               required>
                        <small class="form-text text-muted">Duração padrão de cada atendimento</small>
                        <?php if (session('errors.service_time')): ?>
                            <div class="invalid-feedback"><?= session('errors.service_time') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Status Ativo -->
            <div class="form-group">
                <label for="active">Status</label>
                <select class="form-control" id="active" name="active" style="max-width: 200px;">
                    <option value="1" <?= old('active', $unit->active) == 1 ? 'selected' : '' ?>>
                        ✅ Ativa
                    </option>
                    <option value="0" <?= old('active', $unit->active) == 0 ? 'selected' : '' ?>>
                        ❌ Inativa
                    </option>
                </select>
                <small class="form-text text-muted">
                    Unidades inativas não aparecem para agendamento
                </small>
            </div>

            <hr>

            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?= route_to('super.units') ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
                <div>
                    <a href="<?= route_to('super.units.show', $unit->id) ?>" class="btn btn-info mr-2">
                        <i class="fas fa-eye mr-1"></i> Ver Detalhes
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Info Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-secondary">
            <i class="fas fa-info-circle mr-2"></i>Informações do Registro
        </h6>
    </div>
    <div class="card-body">
        <div class="row text-muted small">
            <div class="col-md-4">
                <strong>ID:</strong> <?= $unit->id ?>
            </div>
            <div class="col-md-4">
                <strong>Criado em:</strong> 
                <?= $unit->created_at ? $unit->created_at->format('d/m/Y H:i') : '-' ?>
            </div>
            <div class="col-md-4">
                <strong>Atualizado em:</strong> 
                <?= $unit->updated_at ? $unit->updated_at->format('d/m/Y H:i') : '-' ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
