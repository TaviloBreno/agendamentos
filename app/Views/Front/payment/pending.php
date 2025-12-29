<?php
/**
 * View: Front/payment/pending.php
 * 
 * Página de pagamento pendente (aguardando confirmação)
 * 
 * @var object $appointment Dados do agendamento
 * @var object|null $payment Dados do pagamento
 */
?>

<?= $this->extend('Front/layout/main') ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-8 has-text-centered">
                
                <!-- Pending Icon -->
                <div class="mb-6">
                    <span class="icon is-large has-text-warning" style="font-size: 6rem;">
                        <i class="fas fa-clock"></i>
                    </span>
                </div>
                
                <h1 class="title is-2 has-text-warning">Pagamento Pendente</h1>
                <p class="subtitle is-4">Aguardando confirmação do pagamento</p>
                
                <!-- Status Info -->
                <div class="notification is-warning is-light mt-5">
                    <div class="content">
                        <p>
                            <span class="icon"><i class="fas fa-hourglass-half"></i></span>
                            <?php if (($payment->payment_method ?? '') === 'boleto'): ?>
                                Seu <strong>boleto</strong> foi gerado. A compensação pode levar até <strong>3 dias úteis</strong>.
                            <?php elseif (($payment->payment_method ?? '') === 'pix'): ?>
                                Aguardando confirmação do <strong>PIX</strong>. A confirmação é automática e instantânea.
                            <?php else: ?>
                                Seu pagamento está sendo processado. Você receberá uma confirmação em breve.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                
                <!-- Appointment Summary -->
                <div class="box mt-5">
                    <div class="columns is-vcentered">
                        <div class="column is-3">
                            <span class="icon is-large has-text-primary">
                                <i class="fas fa-calendar-check fa-3x"></i>
                            </span>
                        </div>
                        <div class="column has-text-left">
                            <h3 class="title is-5 mb-2"><?= esc($appointment->service_name ?? 'Serviço') ?></h3>
                            <p class="mb-3">com <?= esc($appointment->professional_name ?? 'Profissional') ?></p>
                            
                            <div class="tags">
                                <span class="tag is-primary is-light is-medium">
                                    <span class="icon"><i class="fas fa-calendar"></i></span>
                                    <span><?= date('d/m/Y', strtotime($appointment->date ?? 'now')) ?></span>
                                </span>
                                <span class="tag is-info is-light is-medium">
                                    <span class="icon"><i class="fas fa-clock"></i></span>
                                    <span><?= date('H:i', strtotime($appointment->time ?? 'now')) ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="level">
                        <div class="level-item has-text-centered">
                            <div>
                                <p class="heading">Código</p>
                                <p class="title is-6">
                                    <code>#<?= str_pad($appointment->id ?? 0, 6, '0', STR_PAD_LEFT) ?></code>
                                </p>
                            </div>
                        </div>
                        <div class="level-item has-text-centered">
                            <div>
                                <p class="heading">Valor</p>
                                <p class="title is-6 has-text-primary">
                                    R$ <?= number_format($payment->amount ?? $appointment->price ?? 0, 2, ',', '.') ?>
                                </p>
                            </div>
                        </div>
                        <div class="level-item has-text-centered">
                            <div>
                                <p class="heading">Status</p>
                                <p class="title is-6">
                                    <span class="tag is-warning is-medium">
                                        <span class="icon"><i class="fas fa-clock"></i></span>
                                        <span>Pendente</span>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PIX Info -->
                <?php if (($payment->payment_method ?? '') === 'pix' && !empty($payment->pix_qr_code)): ?>
                <div class="box mt-5">
                    <h4 class="title is-5">
                        <span class="icon"><i class="fas fa-qrcode"></i></span>
                        QR Code PIX
                    </h4>
                    <div class="has-text-centered">
                        <img src="data:image/png;base64,<?= $payment->pix_qr_code_base64 ?? '' ?>" 
                             alt="QR Code PIX" style="max-width: 200px;" class="mb-3">
                        
                        <div class="field has-addons has-addons-centered">
                            <div class="control is-expanded">
                                <input type="text" id="pix-code" class="input" 
                                       value="<?= esc($payment->pix_qr_code) ?>" readonly>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-primary" onclick="copyPixCode()">
                                    <span class="icon"><i class="fas fa-copy"></i></span>
                                </button>
                            </div>
                        </div>
                        
                        <p class="is-size-7 has-text-grey mt-3">
                            <span class="icon"><i class="fas fa-clock"></i></span>
                            Expira em <span id="pix-countdown">30:00</span>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Boleto Info -->
                <?php if (($payment->payment_method ?? '') === 'boleto' && !empty($payment->boleto_url)): ?>
                <div class="box mt-5">
                    <h4 class="title is-5">
                        <span class="icon"><i class="fas fa-barcode"></i></span>
                        Boleto Bancário
                    </h4>
                    <p class="mb-4">Clique no botão abaixo para visualizar ou imprimir seu boleto:</p>
                    
                    <a href="<?= esc($payment->boleto_url) ?>" target="_blank" class="button is-primary is-large">
                        <span class="icon"><i class="fas fa-external-link-alt"></i></span>
                        <span>Visualizar Boleto</span>
                    </a>
                    
                    <?php if (!empty($payment->boleto_barcode)): ?>
                    <div class="mt-4">
                        <p class="is-size-7 has-text-grey mb-2">Código de barras:</p>
                        <code class="is-size-7"><?= esc($payment->boleto_barcode) ?></code>
                    </div>
                    <?php endif; ?>
                    
                    <div class="notification is-info is-light mt-4">
                        <p class="is-size-7">
                            <span class="icon"><i class="fas fa-info-circle"></i></span>
                            Vencimento: <?= date('d/m/Y', strtotime($payment->boleto_due_date ?? '+3 days')) ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- What Happens Next -->
                <div class="notification is-info is-light mt-5">
                    <div class="content has-text-left">
                        <h4><span class="icon"><i class="fas fa-forward"></i></span> O que acontece agora?</h4>
                        <ul>
                            <li>Assim que o pagamento for confirmado, você receberá um <strong>WhatsApp</strong> e <strong>E-mail</strong></li>
                            <li>Seu agendamento será automaticamente confirmado</li>
                            <li>Você pode acompanhar o status em "Meus Agendamentos"</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Auto-refresh notice -->
                <div class="notification is-light mt-4">
                    <p class="is-size-7">
                        <span class="icon"><i class="fas fa-sync-alt fa-spin"></i></span>
                        Esta página será atualizada automaticamente quando o pagamento for confirmado
                    </p>
                </div>
                
                <!-- Action Buttons -->
                <div class="buttons is-centered mt-6">
                    <a href="<?= site_url('meus-agendamentos') ?>" class="button is-primary is-large">
                        <span class="icon"><i class="fas fa-list"></i></span>
                        <span>Meus Agendamentos</span>
                    </a>
                    <a href="<?= site_url() ?>" class="button is-light is-large">
                        <span class="icon"><i class="fas fa-home"></i></span>
                        <span>Voltar ao Início</span>
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Copy PIX code
function copyPixCode() {
    const code = document.getElementById('pix-code');
    if (code) {
        code.select();
        document.execCommand('copy');
        
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
}

// Auto-refresh to check payment status
const appointmentId = <?= $appointment->id ?? 0 ?>;

setInterval(async () => {
    try {
        const response = await fetch('<?= site_url('payment/status') ?>/' + appointmentId);
        const data = await response.json();
        
        if (data.status === 'approved') {
            window.location.href = '<?= site_url('payment/success') ?>/' + appointmentId;
        } else if (data.status === 'rejected' || data.status === 'cancelled') {
            window.location.href = '<?= site_url('payment/failure') ?>/' + appointmentId;
        }
    } catch (e) {
        console.error('Erro ao verificar status', e);
    }
}, 10000); // Check every 10 seconds

// PIX countdown
<?php if (($payment->payment_method ?? '') === 'pix'): ?>
let seconds = 30 * 60; // 30 minutes
const countdownEl = document.getElementById('pix-countdown');

if (countdownEl) {
    setInterval(() => {
        seconds--;
        
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        countdownEl.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        
        if (seconds <= 0) {
            countdownEl.textContent = 'Expirado';
            countdownEl.classList.add('has-text-danger');
        }
    }, 1000);
}
<?php endif; ?>
</script>
<?= $this->endSection() ?>
