<?php
/**
 * View: Front/payment/success.php
 * 
 * Página de sucesso após pagamento
 * 
 * @var object $appointment Dados do agendamento
 * @var object $payment Dados do pagamento
 */
?>

<?= $this->extend('Front/layout/main') ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-8 has-text-centered">
                
                <!-- Success Animation -->
                <div class="mb-6">
                    <span class="icon is-large has-text-success" style="font-size: 6rem;">
                        <i class="fas fa-check-circle"></i>
                    </span>
                </div>
                
                <h1 class="title is-2 has-text-success">Pagamento Confirmado!</h1>
                <p class="subtitle is-4">Seu agendamento foi confirmado com sucesso</p>
                
                <!-- Confirmation Box -->
                <div class="box mt-6">
                    <div class="columns is-vcentered">
                        <div class="column is-4 has-text-left">
                            <span class="icon-text">
                                <span class="icon is-large has-text-primary">
                                    <i class="fas fa-calendar-check fa-2x"></i>
                                </span>
                            </span>
                        </div>
                        <div class="column has-text-left">
                            <h2 class="title is-5 mb-2"><?= esc($appointment->service_name ?? 'Serviço') ?></h2>
                            <p class="subtitle is-6 mb-3">
                                com <?= esc($appointment->professional_name ?? 'Profissional') ?>
                            </p>
                            
                            <div class="tags are-medium">
                                <span class="tag is-primary is-light">
                                    <span class="icon"><i class="fas fa-calendar"></i></span>
                                    <span><?= date('d/m/Y', strtotime($appointment->date ?? 'now')) ?></span>
                                </span>
                                <span class="tag is-info is-light">
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
                                <p class="heading">Código do Agendamento</p>
                                <p class="title is-5">
                                    <code>#<?= str_pad($appointment->id ?? 0, 6, '0', STR_PAD_LEFT) ?></code>
                                </p>
                            </div>
                        </div>
                        <div class="level-item has-text-centered">
                            <div>
                                <p class="heading">Valor Pago</p>
                                <p class="title is-5 has-text-success">
                                    R$ <?= number_format($payment->amount ?? $appointment->price ?? 0, 2, ',', '.') ?>
                                </p>
                            </div>
                        </div>
                        <div class="level-item has-text-centered">
                            <div>
                                <p class="heading">Forma de Pagamento</p>
                                <p class="title is-5">
                                    <?php
                                    $method = $payment->payment_method ?? 'pix';
                                    $icons = [
                                        'pix' => 'fa-qrcode',
                                        'credit_card' => 'fa-credit-card',
                                        'debit_card' => 'fa-credit-card',
                                        'boleto' => 'fa-barcode',
                                        'account_money' => 'fa-wallet'
                                    ];
                                    $labels = [
                                        'pix' => 'PIX',
                                        'credit_card' => 'Cartão de Crédito',
                                        'debit_card' => 'Cartão de Débito',
                                        'boleto' => 'Boleto',
                                        'account_money' => 'Saldo MP'
                                    ];
                                    ?>
                                    <span class="icon-text">
                                        <span class="icon"><i class="fas <?= $icons[$method] ?? 'fa-money-bill' ?>"></i></span>
                                        <span><?= $labels[$method] ?? 'Outro' ?></span>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Next Steps -->
                <div class="notification is-info is-light mt-5">
                    <div class="content has-text-left">
                        <h4><span class="icon"><i class="fas fa-info-circle"></i></span> Próximos Passos</h4>
                        <ul>
                            <li>Você receberá uma confirmação por <strong>WhatsApp</strong> e <strong>E-mail</strong></li>
                            <li>Compareça no local com <strong>15 minutos</strong> de antecedência</li>
                            <li>Em caso de dúvidas, entre em contato pelo WhatsApp</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Location Info -->
                <?php if (!empty($appointment->unit_address)): ?>
                <div class="box mt-5">
                    <h4 class="title is-5">
                        <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
                        Local do Atendimento
                    </h4>
                    <p><?= esc($appointment->unit_name ?? 'Unidade') ?></p>
                    <p class="has-text-grey"><?= esc($appointment->unit_address) ?></p>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($appointment->unit_address) ?>" 
                       target="_blank" class="button is-link is-light mt-3">
                        <span class="icon"><i class="fas fa-directions"></i></span>
                        <span>Ver no Mapa</span>
                    </a>
                </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="buttons is-centered mt-6">
                    <a href="<?= site_url('meus-agendamentos') ?>" class="button is-primary is-large">
                        <span class="icon"><i class="fas fa-list"></i></span>
                        <span>Ver Meus Agendamentos</span>
                    </a>
                    <a href="<?= site_url('agendar') ?>" class="button is-light is-large">
                        <span class="icon"><i class="fas fa-plus"></i></span>
                        <span>Novo Agendamento</span>
                    </a>
                </div>
                
                <!-- Add to Calendar -->
                <div class="mt-5">
                    <p class="is-size-7 has-text-grey mb-2">Adicionar à agenda:</p>
                    <div class="buttons is-centered">
                        <a href="#" class="button is-small is-light" 
                           onclick="addToGoogleCalendar(); return false;">
                            <span class="icon"><i class="fab fa-google"></i></span>
                            <span>Google</span>
                        </a>
                        <a href="#" class="button is-small is-light"
                           onclick="downloadICS(); return false;">
                            <span class="icon"><i class="fas fa-calendar-plus"></i></span>
                            <span>Outlook/Apple</span>
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function addToGoogleCalendar() {
    const event = {
        title: '<?= esc($appointment->service_name ?? 'Agendamento') ?>',
        date: '<?= $appointment->date ?? date('Y-m-d') ?>',
        time: '<?= $appointment->time ?? '09:00:00' ?>',
        location: '<?= esc($appointment->unit_address ?? '') ?>'
    };
    
    const startDate = new Date(event.date + 'T' + event.time);
    const endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // +1 hour
    
    const formatDate = (d) => d.toISOString().replace(/-|:|\.\d\d\d/g, '');
    
    const url = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(event.title)}&dates=${formatDate(startDate)}/${formatDate(endDate)}&location=${encodeURIComponent(event.location)}`;
    
    window.open(url, '_blank');
}

function downloadICS() {
    const event = {
        title: '<?= esc($appointment->service_name ?? 'Agendamento') ?>',
        date: '<?= $appointment->date ?? date('Y-m-d') ?>',
        time: '<?= $appointment->time ?? '09:00:00' ?>',
        location: '<?= esc($appointment->unit_address ?? '') ?>'
    };
    
    const startDate = new Date(event.date + 'T' + event.time);
    const endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
    
    const formatDate = (d) => d.toISOString().replace(/-|:|\.\d\d\d/g, '').slice(0, -1);
    
    const ics = `BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
DTSTART:${formatDate(startDate)}
DTEND:${formatDate(endDate)}
SUMMARY:${event.title}
LOCATION:${event.location}
END:VEVENT
END:VCALENDAR`;
    
    const blob = new Blob([ics], { type: 'text/calendar' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'agendamento.ics';
    a.click();
}
</script>
<?= $this->endSection() ?>
