<?php
/**
 * View: Back/WhatsApp/index.php
 * 
 * Configuração de integração WhatsApp
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('css') ?>
<style>
    .provider-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .provider-card:hover {
        border-color: #4e73df;
        transform: translateY(-2px);
    }
    .provider-card.selected {
        border-color: #4e73df;
        background-color: #f8f9fc;
    }
    .status-badge {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }
    .qr-code-container {
        text-align: center;
        padding: 20px;
        background: #f8f9fc;
        border-radius: 10px;
    }
    .qr-code-container img {
        max-width: 200px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fab fa-whatsapp text-success mr-2"></i><?= esc($pageHeading ?? 'Configurar WhatsApp') ?>
    </h1>
    <a href="<?= route_to('super.whatsapp.queue') ?>" class="btn btn-info btn-sm shadow-sm">
        <i class="fas fa-list-alt fa-sm mr-1"></i> Ver Fila
        <?php if (($queueStats['pending'] ?? 0) > 0): ?>
            <span class="badge badge-light"><?= $queueStats['pending'] ?></span>
        <?php endif; ?>
    </a>
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

<?php if (session()->has('danger')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i><?= session('danger') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Configuração -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-cog mr-2"></i>Configurações
                </h6>
            </div>
            <div class="card-body">
                <form action="<?= route_to('super.whatsapp.save') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <!-- Habilitar/Desabilitar -->
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="whatsapp_enabled" name="whatsapp_enabled" value="1" <?= $user->whatsapp_enabled ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="whatsapp_enabled">
                                <strong>Habilitar Notificações WhatsApp</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted">Ative para enviar notificações automáticas aos clientes</small>
                    </div>
                    
                    <hr>
                    
                    <!-- Provedor -->
                    <div class="form-group">
                        <label><i class="fas fa-server mr-1"></i>Provedor de API</label>
                        <div class="row">
                            <?php foreach ($providers as $key => $name): ?>
                                <div class="col-md-3 mb-2">
                                    <div class="card provider-card <?= $user->whatsapp_provider === $key ? 'selected' : '' ?>" onclick="selectProvider('<?= $key ?>')">
                                        <div class="card-body text-center py-3">
                                            <i class="fab fa-whatsapp fa-2x text-success mb-2"></i>
                                            <p class="mb-0 small font-weight-bold"><?= $name ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="whatsapp_provider" id="whatsapp_provider" value="<?= $user->whatsapp_provider ?? 'ultramsg' ?>">
                    </div>
                    
                    <hr>
                    
                    <!-- Credenciais -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="whatsapp_instance_id">
                                    <i class="fas fa-key mr-1"></i>Instance ID <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="whatsapp_instance_id" name="whatsapp_instance_id" value="<?= esc($user->whatsapp_instance_id ?? '') ?>" placeholder="Ex: instance12345">
                                <small class="form-text text-muted">ID da sua instância no provedor</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="whatsapp_token">
                                    <i class="fas fa-lock mr-1"></i>Token <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="whatsapp_token" name="whatsapp_token" value="<?= esc($user->whatsapp_token ?? '') ?>" placeholder="Seu token de autenticação">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="whatsapp_phone">
                            <i class="fas fa-phone mr-1"></i>Número WhatsApp Conectado
                        </label>
                        <input type="text" class="form-control phone-mask" id="whatsapp_phone" name="whatsapp_phone" value="<?= esc($user->whatsapp_phone ?? '') ?>" placeholder="(11) 99999-9999">
                        <small class="form-text text-muted">Número que será usado para enviar as mensagens</small>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Salvar Configurações
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Status e Teste -->
    <div class="col-lg-4">
        <!-- Status da Conexão -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-signal mr-2"></i>Status da Conexão
                </h6>
            </div>
            <div class="card-body text-center">
                <div id="statusContainer">
                    <?php if ($user->whatsapp_status === 'connected'): ?>
                        <span class="badge badge-success status-badge">
                            <i class="fas fa-check-circle mr-1"></i>Conectado
                        </span>
                    <?php elseif ($user->whatsapp_instance_id): ?>
                        <span class="badge badge-warning status-badge">
                            <i class="fas fa-exclamation-circle mr-1"></i>Verificando...
                        </span>
                    <?php else: ?>
                        <span class="badge badge-secondary status-badge">
                            <i class="fas fa-minus-circle mr-1"></i>Não Configurado
                        </span>
                    <?php endif; ?>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="checkStatus()">
                        <i class="fas fa-sync-alt mr-1"></i>Verificar Status
                    </button>
                </div>
                
                <!-- QR Code -->
                <div id="qrCodeContainer" class="qr-code-container mt-3 d-none">
                    <p class="text-muted mb-2"><small>Escaneie o QR Code para conectar:</small></p>
                    <img id="qrCodeImage" src="" alt="QR Code">
                </div>
            </div>
        </div>
        
        <!-- Teste de Mensagem -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-paper-plane mr-2"></i>Enviar Teste
                </h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="test_phone">Número para Teste</label>
                    <input type="text" class="form-control phone-mask" id="test_phone" placeholder="(11) 99999-9999">
                </div>
                <div class="form-group">
                    <label for="test_message">Mensagem</label>
                    <textarea class="form-control" id="test_message" rows="3" placeholder="Mensagem de teste...">🔔 Teste do Sistema de Agendamentos - WhatsApp configurado com sucesso!</textarea>
                </div>
                <button type="button" class="btn btn-success btn-block" onclick="sendTest()" id="btnTest">
                    <i class="fab fa-whatsapp mr-1"></i>Enviar Teste
                </button>
                <div id="testResult" class="mt-3"></div>
            </div>
        </div>
        
        <!-- Estatísticas da Fila -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie mr-2"></i>Fila de Notificações
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <span class="h4 text-warning"><?= $queueStats['pending'] ?? 0 ?></span>
                        <p class="small text-muted mb-0">Pendentes</p>
                    </div>
                    <div class="col-6 mb-3">
                        <span class="h4 text-info"><?= $queueStats['processing'] ?? 0 ?></span>
                        <p class="small text-muted mb-0">Processando</p>
                    </div>
                    <div class="col-6">
                        <span class="h4 text-success"><?= $queueStats['sent'] ?? 0 ?></span>
                        <p class="small text-muted mb-0">Enviadas</p>
                    </div>
                    <div class="col-6">
                        <span class="h4 text-danger"><?= $queueStats['failed'] ?? 0 ?></span>
                        <p class="small text-muted mb-0">Falhas</p>
                    </div>
                </div>
                <?php if (($queueStats['pending'] ?? 0) > 0): ?>
                    <hr>
                    <button type="button" class="btn btn-warning btn-sm btn-block" onclick="processQueue()">
                        <i class="fas fa-play mr-1"></i>Processar Fila Agora
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    // Selecionar provedor
    function selectProvider(provider) {
        $('#whatsapp_provider').val(provider);
        $('.provider-card').removeClass('selected');
        $('.provider-card').each(function() {
            if ($(this).find('.small').text().toLowerCase().includes(provider.replace('-', ''))) {
                $(this).addClass('selected');
            }
        });
        // Re-select based on value
        $('.provider-card').removeClass('selected');
        $(`[onclick="selectProvider('${provider}')"]`).addClass('selected');
    }
    
    // Toggle visibilidade do token
    function togglePassword() {
        const input = $('#whatsapp_token');
        const icon = $('#toggleIcon');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }
    
    // Verificar status da conexão
    function checkStatus() {
        $('#statusContainer').html('<span class="badge badge-info status-badge"><i class="fas fa-spinner fa-spin mr-1"></i>Verificando...</span>');
        
        $.get('<?= route_to('super.whatsapp.status') ?>', function(response) {
            let badge = '';
            if (response.status === 'connected') {
                badge = '<span class="badge badge-success status-badge"><i class="fas fa-check-circle mr-1"></i>Conectado</span>';
                $('#qrCodeContainer').addClass('d-none');
            } else if (response.status === 'not_configured') {
                badge = '<span class="badge badge-secondary status-badge"><i class="fas fa-minus-circle mr-1"></i>Não Configurado</span>';
            } else {
                badge = '<span class="badge badge-danger status-badge"><i class="fas fa-times-circle mr-1"></i>Desconectado</span>';
                // Tentar obter QR Code
                getQrCode();
            }
            $('#statusContainer').html(badge);
        }).fail(function() {
            $('#statusContainer').html('<span class="badge badge-danger status-badge"><i class="fas fa-exclamation-triangle mr-1"></i>Erro</span>');
        });
    }
    
    // Obter QR Code
    function getQrCode() {
        $.get('<?= route_to('super.whatsapp.qrcode') ?>', function(response) {
            if (response.qr) {
                $('#qrCodeImage').attr('src', response.qr);
                $('#qrCodeContainer').removeClass('d-none');
            }
        });
    }
    
    // Enviar mensagem de teste
    function sendTest() {
        const phone = $('#test_phone').val();
        const message = $('#test_message').val();
        
        if (!phone) {
            alert('Informe o número de telefone');
            return;
        }
        
        $('#btnTest').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Enviando...');
        $('#testResult').html('');
        
        $.post('<?= route_to('super.whatsapp.test') ?>', {
            <?= csrf_token() ?>: '<?= csrf_hash() ?>',
            phone: phone,
            message: message
        }, function(response) {
            if (response.success) {
                $('#testResult').html('<div class="alert alert-success"><i class="fas fa-check mr-1"></i>Mensagem enviada com sucesso!</div>');
            } else {
                $('#testResult').html('<div class="alert alert-danger"><i class="fas fa-times mr-1"></i>' + (response.error || 'Erro ao enviar') + '</div>');
            }
        }).fail(function() {
            $('#testResult').html('<div class="alert alert-danger"><i class="fas fa-times mr-1"></i>Erro de conexão</div>');
        }).always(function() {
            $('#btnTest').prop('disabled', false).html('<i class="fab fa-whatsapp mr-1"></i>Enviar Teste');
        });
    }
    
    // Processar fila
    function processQueue() {
        if (!confirm('Deseja processar a fila de notificações agora?')) return;
        
        $.post('<?= route_to('super.whatsapp.process') ?>', {
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(response) {
            alert(response.message);
            location.reload();
        });
    }
    
    // Verificar status ao carregar
    $(document).ready(function() {
        <?php if ($user->whatsapp_instance_id): ?>
        checkStatus();
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>
