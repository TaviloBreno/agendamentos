<?php
/**
 * View: Front/payment/failure.php
 * 
 * Página de falha no pagamento
 * 
 * @var object $appointment Dados do agendamento
 * @var string|null $error_message Mensagem de erro
 */
?>

<?= $this->extend('Front/layout/main') ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-8 has-text-centered">
                
                <!-- Failure Icon -->
                <div class="mb-6">
                    <span class="icon is-large has-text-danger" style="font-size: 6rem;">
                        <i class="fas fa-times-circle"></i>
                    </span>
                </div>
                
                <h1 class="title is-2 has-text-danger">Pagamento não aprovado</h1>
                <p class="subtitle is-4">Não foi possível processar seu pagamento</p>
                
                <!-- Error Details -->
                <div class="notification is-danger is-light mt-5">
                    <div class="content">
                        <p>
                            <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <?= esc($error_message ?? 'O pagamento foi recusado pela operadora do cartão ou houve um problema no processamento.') ?>
                        </p>
                    </div>
                </div>
                
                <!-- Common Reasons -->
                <div class="box mt-5">
                    <h4 class="title is-5">
                        <span class="icon"><i class="fas fa-question-circle"></i></span>
                        Possíveis motivos:
                    </h4>
                    <div class="content has-text-left">
                        <ul>
                            <li>Cartão com limite insuficiente</li>
                            <li>Dados do cartão incorretos</li>
                            <li>Cartão bloqueado ou vencido</li>
                            <li>Transação não autorizada pelo banco</li>
                            <li>Problemas temporários na operadora</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Appointment Summary -->
                <div class="box mt-5">
                    <h4 class="title is-5">Seu agendamento ainda está reservado:</h4>
                    
                    <div class="level">
                        <div class="level-item has-text-centered">
                            <div>
                                <p class="heading">Serviço</p>
                                <p class="title is-6"><?= esc($appointment->service_name ?? 'Serviço') ?></p>
                            </div>
                        </div>
                        <div class="level-item has-text-centered">
                            <div>
                                <p class="heading">Data</p>
                                <p class="title is-6">
                                    <?= date('d/m/Y', strtotime($appointment->date ?? 'now')) ?>
                                </p>
                            </div>
                        </div>
                        <div class="level-item has-text-centered">
                            <div>
                                <p class="heading">Horário</p>
                                <p class="title is-6">
                                    <?= date('H:i', strtotime($appointment->time ?? 'now')) ?>
                                </p>
                            </div>
                        </div>
                        <div class="level-item has-text-centered">
                            <div>
                                <p class="heading">Valor</p>
                                <p class="title is-6 has-text-primary">
                                    R$ <?= number_format($appointment->price ?? 0, 2, ',', '.') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="notification is-warning is-light">
                        <span class="icon"><i class="fas fa-clock"></i></span>
                        Você tem <strong>30 minutos</strong> para tentar novamente antes de perder a reserva.
                    </div>
                </div>
                
                <!-- What to do -->
                <div class="notification is-info is-light mt-5">
                    <div class="content has-text-left">
                        <h4><span class="icon"><i class="fas fa-lightbulb"></i></span> O que você pode fazer:</h4>
                        <ol>
                            <li>Verificar os dados do cartão e tentar novamente</li>
                            <li>Usar outro cartão de crédito</li>
                            <li>Tentar pagamento via PIX (instantâneo)</li>
                            <li>Entrar em contato com seu banco</li>
                        </ol>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="buttons is-centered mt-6">
                    <a href="<?= site_url('payment/checkout/' . ($appointment->id ?? 0)) ?>" class="button is-primary is-large">
                        <span class="icon"><i class="fas fa-redo"></i></span>
                        <span>Tentar Novamente</span>
                    </a>
                    <a href="<?= site_url('agendar') ?>" class="button is-light is-large">
                        <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                        <span>Novo Agendamento</span>
                    </a>
                </div>
                
                <!-- Support -->
                <div class="mt-6">
                    <p class="is-size-7 has-text-grey">
                        Precisa de ajuda? Entre em contato pelo WhatsApp:
                        <a href="https://wa.me/5500000000000" target="_blank" class="has-text-success">
                            <span class="icon"><i class="fab fa-whatsapp"></i></span>
                            (00) 00000-0000
                        </a>
                    </p>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
