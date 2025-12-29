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

<?php
/**
 * MENSAGENS DE FEEDBACK
 * =====================
 * 
 * As mensagens de erro de validação e flash messages (success, danger, info)
 * agora são exibidas automaticamente pelo partial _messages.php incluído
 * no layout principal (main.php).
 * 
 * Não é mais necessário incluir bloco de erros aqui.
 * 
 * Os erros específicos por campo são exibidos via showErrorInput()
 * ao lado de cada input.
 */
?>

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
                               class="form-control <?= hasErrorInput('name') ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= esc(old('name', $unit->name ?? '')) ?>"
                               placeholder="Ex: Unidade Centro"
                               maxlength="70"
                               required>
                        <?= showErrorInput('name') ?>
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
                               class="form-control <?= hasErrorInput('email') ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= esc(old('email', $unit->email ?? '')) ?>"
                               placeholder="contato@unidade.com"
                               maxlength="100"
                               required>
                        <?= showErrorInput('email') ?>
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
                               class="form-control <?= hasErrorInput('phone') ? 'is-invalid' : '' ?>" 
                               id="phone" 
                               name="phone" 
                               value="<?= esc(old('phone', $unit->phone ?? '')) ?>"
                               placeholder="(11) 99999-9999"
                               maxlength="14"
                               required>
                        <?= showErrorInput('phone') ?>
                    </div>
                </div>

                <!-- Coordenador -->
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="coordinator">
                            Coordenador/Responsável
                        </label>
                        <input type="text" 
                               class="form-control <?= hasErrorInput('coordinator') ? 'is-invalid' : '' ?>" 
                               id="coordinator" 
                               name="coordinator" 
                               value="<?= esc(old('coordinator', $unit->coordinator ?? '')) ?>"
                               placeholder="Nome do responsável pela unidade"
                               maxlength="70">
                        <?= showErrorInput('coordinator') ?>
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
                               class="form-control <?= hasErrorInput('address') ? 'is-invalid' : '' ?>" 
                               id="address" 
                               name="address" 
                               value="<?= esc(old('address', $unit->address ?? '')) ?>"
                               placeholder="Rua, número, bairro, cidade - UF"
                               maxlength="128"
                               required>
                        <?= showErrorInput('address') ?>
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
                               class="form-control <?= hasErrorInput('start_time') ? 'is-invalid' : '' ?>" 
                               id="start_time" 
                               name="start_time" 
                               value="<?= esc(old('start_time', $unit->start_time ?? '08:00')) ?>"
                               required>
                        <?= showErrorInput('start_time') ?>
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
                               class="form-control <?= hasErrorInput('end_time') ? 'is-invalid' : '' ?>" 
                               id="end_time" 
                               name="end_time" 
                               value="<?= esc(old('end_time', $unit->end_time ?? '18:00')) ?>"
                               required>
                        <?= showErrorInput('end_time') ?>
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
                        <?= showErrorInput('service_time') ?>
                        <small class="form-text text-muted">
                            Duração de cada atendimento (intervalo entre agendamentos)
                        </small>
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="text-primary mb-3">
                <i class="fas fa-concierge-bell mr-2"></i>Serviços Oferecidos
            </h6>

            <!-- ============================================================
                 LINHA 5: Serviços (Multi-select com Select2)
                 ============================================================ -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="services">
                            Serviços Disponíveis nesta Unidade
                        </label>
                        <?php
                        /**
                         * MULTI-SELECT DE SERVIÇOS
                         * ========================
                         * 
                         * Permite selecionar múltiplos serviços para a unidade.
                         * Os serviços selecionados são armazenados como JSON no banco.
                         * 
                         * Prioridade de valores:
                         * 1. old('services') - Valores do POST (após erro de validação)
                         * 2. $unit->services - Valores do banco (decodificados)
                         * 3. [] - Array vazio como padrão
                         * 
                         * O name="services[]" garante que o PHP receba um array.
                         */
                        $selectedServices = old('services');
                        if ($selectedServices === null && isset($unit->services)) {
                            // Decodifica o JSON do banco
                            $decoded = is_string($unit->services) ? json_decode($unit->services, true) : $unit->services;
                            $selectedServices = is_array($decoded) ? $decoded : [];
                        }
                        $selectedServices = $selectedServices ?? [];
                        ?>
                        <select class="form-control select2-services <?= hasErrorInput('services') ? 'is-invalid' : '' ?>" 
                                id="services" 
                                name="services[]" 
                                multiple="multiple"
                                data-placeholder="Selecione os serviços oferecidos nesta unidade">
                            <?php foreach ($availableServices as $id => $label): ?>
                                <option value="<?= $id ?>" <?= in_array($id, $selectedServices) ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= showErrorInput('services') ?>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Selecione os serviços que esta unidade pode oferecer aos clientes
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
