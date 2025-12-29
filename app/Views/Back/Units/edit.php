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
 * FORM HELPER
 * =========================================================================
 * 
 * Usamos o Form Helper do CodeIgniter para:
 * 
 * 1. form_open() - Abre o formulário COM CSRF automático
 *    <?= form_open(route_to('rota'), ['class' => 'form'], ['_method' => 'PUT']) ?>
 *    
 *    Gera automaticamente:
 *    <form action="..." method="post">
 *    <input type="hidden" name="csrf" value="token_aqui">
 *    <input type="hidden" name="_method" value="PUT">
 * 
 * 2. form_hidden() - Campos ocultos
 *    <?= form_hidden('campo', 'valor') ?>
 * 
 * 3. form_close() - Fecha o formulário
 *    <?= form_close() ?>
 * 
 * =========================================================================
 * CHECKBOX "ACTIVE" - TÉCNICA DO HIDDEN
 * =========================================================================
 * 
 * Problema: Checkbox desmarcado NÃO envia nenhum valor no POST.
 * Solução: Colocar um hidden com value="0" ANTES do checkbox.
 * 
 * <?= form_hidden('active', '0') ?>
 * <input type="checkbox" name="active" value="1">
 * 
 * - Se desmarcado: envia active=0 (do hidden)
 * - Se marcado: envia active=1 (checkbox sobrescreve hidden)
 * 
 * =========================================================================
 * OLD() PARA REPOPULAÇÃO
 * =========================================================================
 * 
 * value="<?= esc(old('campo', $unit->campo ?? '')) ?>"
 * 
 * Prioridade:
 * 1. old('campo') - Valor do POST anterior (após validação falhar)
 * 2. $unit->campo - Valor do banco de dados
 * 3. '' - String vazia (fallback para criação)
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
        
        <?php
        /**
         * FORM_OPEN COM CSRF AUTOMÁTICO E METHOD SPOOFING
         * ================================================
         * 
         * Parâmetros:
         * 1. action (string|array) - URL ou array de configuração
         * 2. attributes (array) - Atributos HTML do form
         * 3. hidden (array) - Campos hidden adicionais (_method para PUT)
         * 
         * Verifique no "View Source" que gera:
         * - <input type="hidden" name="csrf" value="...">
         * - <input type="hidden" name="_method" value="PUT">
         */
        ?>
        <?= form_open(
            route_to('super.units.update', $unit->id),
            ['class' => 'needs-validation', 'novalidate' => true],
            ['_method' => 'PUT']
        ) ?>

            <!-- ============================================================
                 LINHA 1: Nome e E-mail (6 + 6 colunas)
                 ============================================================ -->
            <div class="row">
                <!-- Nome da Unidade -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">
                            Nome da Unidade <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               value="<?= esc(old('name', $unit->name ?? '')) ?>"
                               placeholder="Ex: Unidade Centro"
                               maxlength="70"
                               required>
                        <small class="form-text text-muted">Nome de identificação da unidade</small>
                    </div>
                </div>

                <!-- E-mail -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">
                            E-mail <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?= esc(old('email', $unit->email ?? '')) ?>"
                               placeholder="contato@unidade.com"
                               maxlength="100"
                               required>
                        <small class="form-text text-muted">E-mail para contato e notificações</small>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 LINHA 2: Telefone e Coordenador (4 + 8 colunas)
                 ============================================================ -->
            <div class="row">
                <!-- Telefone -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="phone">
                            Telefone <span class="text-danger">*</span>
                        </label>
                        <input type="tel" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               value="<?= esc(old('phone', $unit->phone ?? '')) ?>"
                               placeholder="(11) 99999-9999"
                               maxlength="14"
                               required>
                    </div>
                </div>

                <!-- Coordenador -->
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="coordinator">
                            Coordenador/Responsável <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="coordinator" 
                               name="coordinator" 
                               value="<?= esc(old('coordinator', $unit->coordinator ?? '')) ?>"
                               placeholder="Nome do responsável pela unidade"
                               maxlength="70"
                               required>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 LINHA 3: Endereço (12 colunas - largura total)
                 ============================================================ -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="address">
                            Endereço <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="address" 
                               name="address" 
                               value="<?= esc(old('address', $unit->address ?? '')) ?>"
                               placeholder="Rua, número, bairro, cidade - UF"
                               maxlength="255"
                               required>
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="text-primary mb-3">
                <i class="fas fa-clock mr-2"></i>Horário de Funcionamento
            </h6>

            <!-- ============================================================
                 LINHA 4: Horários (4 + 4 + 4 colunas)
                 ============================================================ -->
            <div class="row">
                <!-- Horário de Início -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="start_time">
                            Horário de Início <span class="text-danger">*</span>
                        </label>
                        <input type="time" 
                               class="form-control" 
                               id="start_time" 
                               name="start_time" 
                               value="<?= esc(old('start_time', $unit->start_time ?? '08:00')) ?>"
                               required>
                        <small class="form-text text-muted">Início do expediente</small>
                    </div>
                </div>

                <!-- Horário de Término -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="end_time">
                            Horário de Término <span class="text-danger">*</span>
                        </label>
                        <input type="time" 
                               class="form-control" 
                               id="end_time" 
                               name="end_time" 
                               value="<?= esc(old('end_time', $unit->end_time ?? '18:00')) ?>"
                               required>
                        <small class="form-text text-muted">Fim do expediente</small>
                    </div>
                </div>

                <!-- Tempo de Atendimento -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="service_time">
                            Tempo de Atendimento <span class="text-danger">*</span>
                        </label>
                        <?php
                        /**
                         * DROPDOWN DE SERVICE_TIME
                         * ========================
                         * 
                         * O select é renderizado pela UnitService::renderTimesInterval().
                         * Isso mantém a view limpa e a lógica centralizada na Service.
                         * 
                         * A variável $timesInterval contém o HTML completo do <select>
                         * gerado via form_dropdown(), incluindo:
                         * - class="form-control"
                         * - id="service_time"
                         * - required="required"
                         * - Opções de $serviceTimes
                         * - Valor selecionado (old() ou $unit->service_time)
                         * 
                         * @see UnitService::renderTimesInterval()
                         * @see UnitService::$serviceTimes
                         */
                        ?>
                        <?= $timesInterval ?>
                        <small class="form-text text-muted">
                            Duração de cada atendimento (intervalo entre agendamentos)
                        </small>
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="text-primary mb-3">
                <i class="fas fa-toggle-on mr-2"></i>Status
            </h6>

            <!-- ============================================================
                 LINHA 5: Checkbox "Registro Ativo" (Bootstrap 4 Custom)
                 ============================================================ -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?php
                        /**
                         * TÉCNICA DO HIDDEN PARA CHECKBOX
                         * ================================
                         * 
                         * 1. Hidden com value="0" vem PRIMEIRO
                         * 2. Checkbox com value="1" vem DEPOIS
                         * 
                         * Comportamento:
                         * - Desmarcado: envia active=0 (hidden)
                         * - Marcado: envia active=1 (checkbox sobrescreve)
                         * 
                         * Isso garante que SEMPRE enviamos um valor,
                         * ao contrário do comportamento padrão onde
                         * checkbox desmarcado não envia nada.
                         */
                        ?>
                        <?= form_hidden('active', '0') ?>
                        
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="active" 
                                   name="active" 
                                   value="1"
                                   <?php
                                   /**
                                    * CHECKED DINÂMICO
                                    * ================
                                    * 
                                    * Prioridade:
                                    * 1. old('active') - Valor do POST (após validação)
                                    * 2. $unit->active - Valor do banco (boolean por cast)
                                    * 3. '0' - Padrão desmarcado
                                    * 
                                    * Convertemos para string para comparação segura,
                                    * já que old() retorna string e $unit->active
                                    * pode ser boolean (por causa do cast int-bool).
                                    */
                                   $activeValue = old('active', $unit->active ?? false);
                                   // Trata boolean e string/int
                                   $isChecked = ($activeValue === true || $activeValue === '1' || $activeValue === 1);
                                   echo $isChecked ? 'checked' : '';
                                   ?>>
                            <label class="custom-control-label" for="active">
                                <strong>Registro Ativo</strong>
                            </label>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Unidades inativas não aparecem para agendamento pelos clientes
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <!-- ============================================================
                 BOTÕES DE AÇÃO
                 ============================================================ -->
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

        <?= form_close() ?>
        
    </div>
</div>

<!-- Info Card - Metadados do Registro -->
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
