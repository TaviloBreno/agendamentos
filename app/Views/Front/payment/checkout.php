<?php
/**
 * View: Front/payment/checkout.php
 * 
 * Página de Checkout para pagamento do agendamento
 * 
 * @var object $appointment Dados do agendamento
 * @var string $title Título da página
 */
?>

<?= $this->extend('Front/layout/main') ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-8">
                
                <!-- Header -->
                <div class="has-text-centered mb-6">
                    <span class="icon is-large has-text-primary">
                        <i class="fas fa-credit-card fa-3x"></i>
                    </span>
                    <h1 class="title is-2 mt-4">Pagamento</h1>
                    <p class="subtitle">Escolha a forma de pagamento para confirmar seu agendamento</p>
                </div>
                
                <!-- Resumo do Agendamento -->
                <div class="box mb-5">
                    <h2 class="title is-5 mb-4">
                        <span class="icon"><i class="fas fa-calendar-check"></i></span>
                        Resumo do Agendamento
                    </h2>
                    
                    <div class="columns">
                        <div class="column is-6">
                            <table class="table is-fullwidth is-borderless">
                                <tr>
                                    <th width="40%">Serviço:</th>
                                    <td><?= esc($appointment->service_name) ?></td>
                                </tr>
                                <tr>
                                    <th>Profissional:</th>
                                    <td><?= esc($appointment->professional_name) ?></td>
                                </tr>
                                <tr>
                                    <th>Data:</th>
                                    <td>
                                        <span class="icon"><i class="fas fa-calendar"></i></span>
                                        <?= date('d/m/Y', strtotime($appointment->date)) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Horário:</th>
                                    <td>
                                        <span class="icon"><i class="fas fa-clock"></i></span>
                                        <?= date('H:i', strtotime($appointment->time)) ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="column is-6">
                            <div class="notification is-primary is-light">
                                <p class="is-size-7 has-text-weight-bold mb-2">VALOR TOTAL</p>
                                <p class="is-size-2 has-text-weight-bold has-text-primary">
                                    R$ <?= number_format($appointment->price ?? 0, 2, ',', '.') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Métodos de Pagamento -->
                <div class="box">
                    <h2 class="title is-5 mb-4">
                        <span class="icon"><i class="fas fa-wallet"></i></span>
                        Forma de Pagamento
                    </h2>
                    
                    <!-- Tabs -->
                    <div class="tabs is-boxed mb-4">
                        <ul>
                            <li class="is-active" data-tab="pix">
                                <a>
                                    <span class="icon"><i class="fas fa-qrcode"></i></span>
                                    <span>PIX</span>
                                    <span class="tag is-success is-light ml-2">Instantâneo</span>
                                </a>
                            </li>
                            <li data-tab="card">
                                <a>
                                    <span class="icon"><i class="fas fa-credit-card"></i></span>
                                    <span>Cartão de Crédito</span>
                                </a>
                            </li>
                            <li data-tab="boleto">
                                <a>
                                    <span class="icon"><i class="fas fa-barcode"></i></span>
                                    <span>Boleto</span>
                                </a>
                            </li>
                            <li data-tab="checkout">
                                <a>
                                    <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                                    <span>Checkout MP</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Tab Contents -->
                    <div id="tab-content">
                        <!-- PIX -->
                        <div class="tab-pane is-active" id="pix-content">
                            <div class="has-text-centered">
                                <div class="notification is-info is-light">
                                    <p class="mb-3">
                                        <span class="icon"><i class="fas fa-bolt"></i></span>
                                        Pagamento instantâneo via PIX. A confirmação é automática!
                                    </p>
                                </div>
                                
                                <div id="pix-qrcode-container" style="display: none;">
                                    <div class="box" style="display: inline-block;">
                                        <img id="pix-qrcode" src="" alt="QR Code PIX" style="max-width: 250px;">
                                    </div>
                                    <div class="mt-4">
                                        <p class="is-size-7 has-text-grey mb-2">Ou copie o código:</p>
                                        <div class="field has-addons has-addons-centered">
                                            <div class="control is-expanded">
                                                <input type="text" id="pix-code" class="input" readonly>
                                            </div>
                                            <div class="control">
                                                <button type="button" class="button is-primary" onclick="copyPixCode()">
                                                    <span class="icon"><i class="fas fa-copy"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="is-size-7 has-text-grey mt-4">
                                        <span class="icon"><i class="fas fa-clock"></i></span>
                                        O QR Code expira em <span id="pix-countdown">30:00</span> minutos
                                    </p>
                                </div>
                                
                                <button type="button" id="btn-generate-pix" class="button is-success is-large" onclick="generatePix()">
                                    <span class="icon"><i class="fas fa-qrcode"></i></span>
                                    <span>Gerar QR Code PIX</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Cartão de Crédito -->
                        <div class="tab-pane" id="card-content" style="display: none;">
                            <form id="card-form">
                                <div class="columns">
                                    <div class="column is-8">
                                        <div class="field">
                                            <label class="label">Número do Cartão</label>
                                            <div class="control has-icons-left">
                                                <input type="text" id="card-number" class="input" 
                                                       placeholder="0000 0000 0000 0000" maxlength="19">
                                                <span class="icon is-left"><i class="fas fa-credit-card"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-4">
                                        <div class="field">
                                            <label class="label">Bandeira</label>
                                            <div class="control">
                                                <span id="card-brand" class="icon is-large has-text-grey">
                                                    <i class="fab fa-cc-visa fa-2x"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="field">
                                    <label class="label">Nome no Cartão</label>
                                    <div class="control has-icons-left">
                                        <input type="text" id="card-holder" class="input" 
                                               placeholder="NOME COMO ESTÁ NO CARTÃO">
                                        <span class="icon is-left"><i class="fas fa-user"></i></span>
                                    </div>
                                </div>
                                
                                <div class="columns">
                                    <div class="column is-6">
                                        <div class="field">
                                            <label class="label">Validade</label>
                                            <div class="control has-icons-left">
                                                <input type="text" id="card-expiry" class="input" 
                                                       placeholder="MM/AA" maxlength="5">
                                                <span class="icon is-left"><i class="fas fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-6">
                                        <div class="field">
                                            <label class="label">CVV</label>
                                            <div class="control has-icons-left">
                                                <input type="text" id="card-cvv" class="input" 
                                                       placeholder="000" maxlength="4">
                                                <span class="icon is-left"><i class="fas fa-lock"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="field">
                                    <label class="label">Parcelas</label>
                                    <div class="control">
                                        <div class="select is-fullwidth">
                                            <select id="installments">
                                                <option value="1">1x de R$ <?= number_format($appointment->price ?? 0, 2, ',', '.') ?> (sem juros)</option>
                                                <?php if (($appointment->price ?? 0) >= 100): ?>
                                                <option value="2">2x de R$ <?= number_format(($appointment->price ?? 0) / 2, 2, ',', '.') ?> (sem juros)</option>
                                                <option value="3">3x de R$ <?= number_format(($appointment->price ?? 0) / 3, 2, ',', '.') ?> (sem juros)</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="field">
                                    <label class="label">CPF do Titular</label>
                                    <div class="control has-icons-left">
                                        <input type="text" id="card-cpf" class="input" 
                                               placeholder="000.000.000-00" maxlength="14">
                                        <span class="icon is-left"><i class="fas fa-id-card"></i></span>
                                    </div>
                                </div>
                                
                                <button type="button" class="button is-primary is-fullwidth is-large mt-4" onclick="processCard()">
                                    <span class="icon"><i class="fas fa-lock"></i></span>
                                    <span>Pagar R$ <?= number_format($appointment->price ?? 0, 2, ',', '.') ?></span>
                                </button>
                            </form>
                            
                            <div class="notification is-light mt-4">
                                <p class="is-size-7 has-text-centered">
                                    <span class="icon"><i class="fas fa-shield-alt has-text-success"></i></span>
                                    Pagamento seguro processado por MercadoPago
                                </p>
                            </div>
                        </div>
                        
                        <!-- Boleto -->
                        <div class="tab-pane" id="boleto-content" style="display: none;">
                            <div class="has-text-centered">
                                <div class="notification is-warning is-light">
                                    <p class="mb-2">
                                        <span class="icon"><i class="fas fa-info-circle"></i></span>
                                        O boleto pode levar até 3 dias úteis para compensar.
                                    </p>
                                    <p class="is-size-7">
                                        Seu agendamento será confirmado após a compensação do pagamento.
                                    </p>
                                </div>
                                
                                <div id="boleto-container" style="display: none;">
                                    <div class="box">
                                        <p class="mb-3"><strong>Boleto gerado com sucesso!</strong></p>
                                        <a id="boleto-link" href="#" target="_blank" class="button is-primary is-large">
                                            <span class="icon"><i class="fas fa-external-link-alt"></i></span>
                                            <span>Visualizar Boleto</span>
                                        </a>
                                    </div>
                                    <p class="is-size-7 has-text-grey mt-3">
                                        Código de barras: <code id="boleto-barcode"></code>
                                    </p>
                                </div>
                                
                                <form id="boleto-form">
                                    <div class="columns is-centered">
                                        <div class="column is-8">
                                            <div class="field">
                                                <label class="label">CPF</label>
                                                <div class="control has-icons-left">
                                                    <input type="text" id="boleto-cpf" class="input" 
                                                           placeholder="000.000.000-00" required>
                                                    <span class="icon is-left"><i class="fas fa-id-card"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="button" id="btn-generate-boleto" class="button is-warning is-large" onclick="generateBoleto()">
                                        <span class="icon"><i class="fas fa-barcode"></i></span>
                                        <span>Gerar Boleto</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Checkout MercadoPago -->
                        <div class="tab-pane" id="checkout-content" style="display: none;">
                            <div class="has-text-centered">
                                <div class="notification is-info is-light">
                                    <p>
                                        <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                                        Você será redirecionado para o checkout seguro do MercadoPago
                                    </p>
                                </div>
                                
                                <img src="https://http2.mlstatic.com/frontend-assets/mp-web-navigation/badge.svg" 
                                     alt="MercadoPago" class="mb-4" style="max-width: 200px;">
                                
                                <p class="mb-4 has-text-grey">
                                    Pague com cartão, PIX, boleto ou saldo em conta MercadoPago
                                </p>
                                
                                <button type="button" class="button is-link is-large" onclick="redirectToCheckout()">
                                    <span class="icon"><i class="fas fa-external-link-alt"></i></span>
                                    <span>Ir para Checkout</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Loading Overlay -->
                <div id="loading-overlay" class="modal">
                    <div class="modal-background"></div>
                    <div class="modal-content has-text-centered">
                        <span class="icon is-large has-text-white">
                            <i class="fas fa-spinner fa-spin fa-3x"></i>
                        </span>
                        <p class="has-text-white mt-4" id="loading-message">Processando pagamento...</p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
const appointmentId = <?= $appointment->id ?>;
const appointmentPrice = <?= $appointment->price ?? 0 ?>;

// Tab navigation
document.querySelectorAll('.tabs li').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remove active from all tabs
        document.querySelectorAll('.tabs li').forEach(t => t.classList.remove('is-active'));
        // Add active to clicked tab
        this.classList.add('is-active');
        
        // Hide all content
        document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
        // Show selected content
        const tabId = this.dataset.tab;
        document.getElementById(tabId + '-content').style.display = 'block';
    });
});

// Show loading
function showLoading(message = 'Processando...') {
    document.getElementById('loading-message').textContent = message;
    document.getElementById('loading-overlay').classList.add('is-active');
}

// Hide loading
function hideLoading() {
    document.getElementById('loading-overlay').classList.remove('is-active');
}

// Copy PIX code
function copyPixCode() {
    const code = document.getElementById('pix-code');
    code.select();
    document.execCommand('copy');
    
    // Show notification
    const notification = document.createElement('div');
    notification.className = 'notification is-success is-light';
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.innerHTML = '<button class="delete"></button><span class="icon"><i class="fas fa-check"></i></span> Código copiado!';
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), 3000);
}

// Generate PIX
async function generatePix() {
    showLoading('Gerando QR Code PIX...');
    
    try {
        const response = await fetch('<?= route_to('payment.pix') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('btn-generate-pix').style.display = 'none';
            document.getElementById('pix-qrcode-container').style.display = 'block';
            document.getElementById('pix-qrcode').src = 'data:image/png;base64,' + data.qr_code_base64;
            document.getElementById('pix-code').value = data.qr_code;
            
            // Start countdown
            startCountdown(30 * 60);
            
            // Start polling for payment status
            pollPaymentStatus(data.payment_id);
        } else {
            alert('Erro ao gerar PIX: ' + data.message);
        }
    } catch (error) {
        alert('Erro ao processar: ' + error.message);
    } finally {
        hideLoading();
    }
}

// Generate Boleto
async function generateBoleto() {
    const cpf = document.getElementById('boleto-cpf').value;
    
    if (!cpf || cpf.length < 11) {
        alert('Informe um CPF válido');
        return;
    }
    
    showLoading('Gerando boleto...');
    
    try {
        const response = await fetch('<?= route_to('payment.pix') ?>', { // Will create boleto endpoint
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                cpf: cpf.replace(/\D/g, ''),
                payment_method: 'boleto',
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('boleto-form').style.display = 'none';
            document.getElementById('boleto-container').style.display = 'block';
            document.getElementById('boleto-link').href = data.boleto_url;
            document.getElementById('boleto-barcode').textContent = data.barcode;
        } else {
            alert('Erro ao gerar boleto: ' + data.message);
        }
    } catch (error) {
        alert('Erro ao processar: ' + error.message);
    } finally {
        hideLoading();
    }
}

// Process Card Payment
async function processCard() {
    // Validate form
    const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
    const cardHolder = document.getElementById('card-holder').value;
    const cardExpiry = document.getElementById('card-expiry').value;
    const cardCvv = document.getElementById('card-cvv').value;
    const cardCpf = document.getElementById('card-cpf').value;
    const installments = document.getElementById('installments').value;
    
    if (!cardNumber || !cardHolder || !cardExpiry || !cardCvv || !cardCpf) {
        alert('Preencha todos os campos do cartão');
        return;
    }
    
    showLoading('Processando pagamento...');
    
    // Here you would integrate with MercadoPago SDK to tokenize the card
    // For now, redirect to MercadoPago checkout
    alert('Integração com cartão em desenvolvimento. Use o Checkout MercadoPago.');
    hideLoading();
}

// Redirect to MercadoPago Checkout
async function redirectToCheckout() {
    showLoading('Redirecionando...');
    
    try {
        const response = await fetch('<?= route_to('payment.preference') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            })
        });
        
        const data = await response.json();
        
        if (data.success && data.init_point) {
            window.location.href = data.init_point;
        } else {
            alert('Erro ao criar checkout: ' + data.message);
            hideLoading();
        }
    } catch (error) {
        alert('Erro ao processar: ' + error.message);
        hideLoading();
    }
}

// Poll payment status
function pollPaymentStatus(paymentId) {
    const interval = setInterval(async () => {
        try {
            const response = await fetch('<?= site_url('payment/status') ?>/' + appointmentId);
            const data = await response.json();
            
            if (data.status === 'approved') {
                clearInterval(interval);
                window.location.href = '<?= site_url('payment/success') ?>/' + appointmentId;
            } else if (data.status === 'rejected') {
                clearInterval(interval);
                window.location.href = '<?= site_url('payment/failure') ?>/' + appointmentId;
            }
        } catch (e) {
            console.error('Erro ao verificar status', e);
        }
    }, 5000); // Check every 5 seconds
}

// Countdown timer
function startCountdown(seconds) {
    const countdownEl = document.getElementById('pix-countdown');
    
    const interval = setInterval(() => {
        seconds--;
        
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        countdownEl.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        
        if (seconds <= 0) {
            clearInterval(interval);
            countdownEl.textContent = 'Expirado';
            countdownEl.classList.add('has-text-danger');
        }
    }, 1000);
}

// Input masks
document.getElementById('card-number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    e.target.value = value.substring(0, 19);
});

document.getElementById('card-expiry').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2);
    }
    e.target.value = value.substring(0, 5);
});

document.getElementById('card-cvv').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
});

['card-cpf', 'boleto-cpf'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value.substring(0, 14);
        });
    }
});
</script>
<?= $this->endSection() ?>
