<?= $this->extend('Front/layout/main') ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="container">
        <!-- Título da Página -->
        <div class="mb-6">
            <h1 class="title is-2">
                <span class="icon-text">
                    <span class="icon has-text-primary">
                        <i class="fas fa-calendar-check"></i>
                    </span>
                    <span>Meus Agendamentos</span>
                </span>
            </h1>
            <p class="subtitle is-5 has-text-grey">
                Gerencie seus agendamentos
            </p>
        </div>

        <!-- Tabs de Status -->
        <div class="tabs is-boxed">
            <ul>
                <li class="is-active" data-tab="upcoming">
                    <a onclick="showTab('upcoming')">
                        <span class="icon"><i class="fas fa-clock"></i></span>
                        <span>Próximos</span>
                        <?php if (count($upcoming) > 0): ?>
                        <span class="tag is-primary is-rounded ml-2"><?= count($upcoming) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li data-tab="past">
                    <a onclick="showTab('past')">
                        <span class="icon"><i class="fas fa-history"></i></span>
                        <span>Histórico</span>
                        <?php if (count($past) > 0): ?>
                        <span class="tag is-info is-rounded ml-2"><?= count($past) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li data-tab="cancelled">
                    <a onclick="showTab('cancelled')">
                        <span class="icon"><i class="fas fa-times-circle"></i></span>
                        <span>Cancelados</span>
                        <?php if (count($cancelled) > 0): ?>
                        <span class="tag is-danger is-rounded ml-2"><?= count($cancelled) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Tab: Próximos Agendamentos -->
        <div class="tab-content" id="tab-upcoming">
            <?php if (empty($upcoming)): ?>
            <div class="box has-text-centered py-6">
                <span class="icon is-large has-text-grey-light">
                    <i class="fas fa-calendar-times fa-3x"></i>
                </span>
                <p class="title is-5 mt-4 has-text-grey">Nenhum agendamento próximo</p>
                <p class="subtitle is-6 has-text-grey-light">
                    Você ainda não possui agendamentos futuros.
                </p>
                <a href="<?= base_url('agendar') ?>" class="button is-primary mt-3">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span>Fazer Agendamento</span>
                </a>
            </div>
            <?php else: ?>
            <div class="columns is-multiline">
                <?php foreach ($upcoming as $appointment): ?>
                <div class="column is-6">
                    <div class="box appointment-card">
                        <div class="appointment-card-header">
                            <div class="appointment-date">
                                <span class="day"><?= date('d', strtotime($appointment->date)) ?></span>
                                <span class="month"><?= getMonthName((int)date('m', strtotime($appointment->date))) ?></span>
                            </div>
                            <div class="appointment-info">
                                <h4 class="title is-5 mb-1"><?= esc($appointment->service_name ?? 'Serviço') ?></h4>
                                <p class="has-text-grey mb-2">
                                    <span class="icon-text">
                                        <span class="icon"><i class="fas fa-clock"></i></span>
                                        <span><?= substr($appointment->start_time, 0, 5) ?></span>
                                    </span>
                                </p>
                                <p class="has-text-grey is-size-7">
                                    <span class="icon-text">
                                        <span class="icon"><i class="fas fa-user-md"></i></span>
                                        <span><?= esc($appointment->professional_name ?? 'Profissional') ?></span>
                                    </span>
                                </p>
                                <p class="has-text-grey is-size-7">
                                    <span class="icon-text">
                                        <span class="icon"><i class="fas fa-building"></i></span>
                                        <span><?= esc($appointment->unit_name ?? 'Unidade') ?></span>
                                    </span>
                                </p>
                            </div>
                            <div class="appointment-status">
                                <?= $appointment->statusBadge() ?>
                            </div>
                        </div>
                        
                        <?php if ($appointment->canCancel()): ?>
                        <div class="appointment-card-footer">
                            <button type="button" class="button is-danger is-outlined is-small" 
                                    onclick="confirmCancel(<?= $appointment->id ?>)">
                                <span class="icon"><i class="fas fa-times"></i></span>
                                <span>Cancelar</span>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab: Histórico -->
        <div class="tab-content" id="tab-past" style="display: none;">
            <?php if (empty($past)): ?>
            <div class="box has-text-centered py-6">
                <span class="icon is-large has-text-grey-light">
                    <i class="fas fa-history fa-3x"></i>
                </span>
                <p class="title is-5 mt-4 has-text-grey">Nenhum histórico</p>
                <p class="subtitle is-6 has-text-grey-light">
                    Seus agendamentos passados aparecerão aqui.
                </p>
            </div>
            <?php else: ?>
            <div class="columns is-multiline">
                <?php foreach ($past as $appointment): ?>
                <div class="column is-6">
                    <div class="box appointment-card is-past">
                        <div class="appointment-card-header">
                            <div class="appointment-date">
                                <span class="day"><?= date('d', strtotime($appointment->date)) ?></span>
                                <span class="month"><?= getMonthName((int)date('m', strtotime($appointment->date))) ?></span>
                                <span class="year"><?= date('Y', strtotime($appointment->date)) ?></span>
                            </div>
                            <div class="appointment-info">
                                <h4 class="title is-5 mb-1"><?= esc($appointment->service_name ?? 'Serviço') ?></h4>
                                <p class="has-text-grey mb-2">
                                    <span class="icon-text">
                                        <span class="icon"><i class="fas fa-clock"></i></span>
                                        <span><?= substr($appointment->start_time, 0, 5) ?></span>
                                    </span>
                                </p>
                                <p class="has-text-grey is-size-7">
                                    <span class="icon-text">
                                        <span class="icon"><i class="fas fa-user-md"></i></span>
                                        <span><?= esc($appointment->professional_name ?? 'Profissional') ?></span>
                                    </span>
                                </p>
                            </div>
                            <div class="appointment-status">
                                <?= $appointment->statusBadge() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab: Cancelados -->
        <div class="tab-content" id="tab-cancelled" style="display: none;">
            <?php if (empty($cancelled)): ?>
            <div class="box has-text-centered py-6">
                <span class="icon is-large has-text-grey-light">
                    <i class="fas fa-check-circle fa-3x"></i>
                </span>
                <p class="title is-5 mt-4 has-text-grey">Nenhum cancelamento</p>
                <p class="subtitle is-6 has-text-grey-light">
                    Você não possui agendamentos cancelados.
                </p>
            </div>
            <?php else: ?>
            <div class="columns is-multiline">
                <?php foreach ($cancelled as $appointment): ?>
                <div class="column is-6">
                    <div class="box appointment-card is-cancelled">
                        <div class="appointment-card-header">
                            <div class="appointment-date">
                                <span class="day"><?= date('d', strtotime($appointment->date)) ?></span>
                                <span class="month"><?= getMonthName((int)date('m', strtotime($appointment->date))) ?></span>
                                <span class="year"><?= date('Y', strtotime($appointment->date)) ?></span>
                            </div>
                            <div class="appointment-info">
                                <h4 class="title is-5 mb-1 has-text-grey-light"><?= esc($appointment->service_name ?? 'Serviço') ?></h4>
                                <p class="has-text-grey-light mb-2">
                                    <span class="icon-text">
                                        <span class="icon"><i class="fas fa-clock"></i></span>
                                        <span><?= substr($appointment->start_time, 0, 5) ?></span>
                                    </span>
                                </p>
                                <p class="has-text-grey-light is-size-7">
                                    <span class="icon-text">
                                        <span class="icon"><i class="fas fa-user-md"></i></span>
                                        <span><?= esc($appointment->professional_name ?? 'Profissional') ?></span>
                                    </span>
                                </p>
                            </div>
                            <div class="appointment-status">
                                <span class="tag is-danger">
                                    <span class="icon"><i class="fas fa-times"></i></span>
                                    <span>Cancelado</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Botão de Novo Agendamento -->
        <div class="has-text-centered mt-6">
            <a href="<?= base_url('agendar') ?>" class="button is-primary is-medium">
                <span class="icon"><i class="fas fa-plus"></i></span>
                <span>Novo Agendamento</span>
            </a>
        </div>
    </div>
</section>

<!-- Modal de Confirmação de Cancelamento -->
<div class="modal" id="cancel-modal">
    <div class="modal-background" onclick="closeModal()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Cancelar Agendamento</p>
            <button class="delete" aria-label="close" onclick="closeModal()"></button>
        </header>
        <section class="modal-card-body">
            <div class="has-text-centered">
                <span class="icon is-large has-text-warning">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </span>
                <p class="title is-5 mt-4">Tem certeza que deseja cancelar?</p>
                <p class="has-text-grey">Esta ação não pode ser desfeita.</p>
            </div>
        </section>
        <footer class="modal-card-foot is-justify-content-flex-end">
            <button class="button" onclick="closeModal()">Não, manter</button>
            <button class="button is-danger" id="btn-confirm-cancel" onclick="cancelAppointment()">
                <span class="icon"><i class="fas fa-times"></i></span>
                <span>Sim, cancelar</span>
            </button>
        </footer>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Appointment Cards */
.appointment-card {
    transition: all 0.3s ease;
}

.appointment-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.appointment-card.is-past {
    opacity: 0.7;
}

.appointment-card.is-cancelled {
    opacity: 0.5;
    background: #fafafa;
}

.appointment-card-header {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.appointment-date {
    background: linear-gradient(135deg, var(--primary-color, #3273dc), #5a9cf5);
    color: white;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    text-align: center;
    min-width: 70px;
}

.appointment-date .day {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    line-height: 1;
}

.appointment-date .month {
    display: block;
    font-size: 0.75rem;
    text-transform: uppercase;
}

.appointment-date .year {
    display: block;
    font-size: 0.7rem;
    opacity: 0.8;
}

.appointment-card.is-past .appointment-date,
.appointment-card.is-cancelled .appointment-date {
    background: #b5b5b5;
}

.appointment-info {
    flex: 1;
}

.appointment-status {
    text-align: right;
}

.appointment-card-footer {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
    text-align: right;
}

/* Tabs Style */
.tabs.is-boxed li.is-active a {
    background-color: white;
    border-color: #dbdbdb;
    border-bottom-color: transparent !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let appointmentToCancel = null;

function showTab(tabName) {
    // Esconder todas as tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });
    
    // Remover classe ativa de todas as tabs
    document.querySelectorAll('.tabs li').forEach(li => {
        li.classList.remove('is-active');
    });
    
    // Mostrar tab selecionada
    document.getElementById('tab-' + tabName).style.display = 'block';
    
    // Ativar tab no menu
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('is-active');
}

function confirmCancel(appointmentId) {
    appointmentToCancel = appointmentId;
    document.getElementById('cancel-modal').classList.add('is-active');
}

function closeModal() {
    document.getElementById('cancel-modal').classList.remove('is-active');
    appointmentToCancel = null;
}

function cancelAppointment() {
    if (!appointmentToCancel) return;
    
    const btn = document.getElementById('btn-confirm-cancel');
    btn.disabled = true;
    btn.innerHTML = '<span class="icon"><i class="fas fa-spinner fa-spin"></i></span><span>Cancelando...</span>';
    
    fetch(`<?= base_url('api/schedule/cancel') ?>/${appointmentToCancel}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Recarregar página para atualizar lista
            window.location.reload();
        } else {
            alert(result.message || 'Erro ao cancelar agendamento');
            btn.disabled = false;
            btn.innerHTML = '<span class="icon"><i class="fas fa-times"></i></span><span>Sim, cancelar</span>';
        }
    })
    .catch(error => {
        alert('Erro de conexão');
        btn.disabled = false;
        btn.innerHTML = '<span class="icon"><i class="fas fa-times"></i></span><span>Sim, cancelar</span>';
    });
}

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
<?= $this->endSection() ?>

<?php
// Helper function para nome do mês
function getMonthName(int $month): string {
    $months = [
        1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr', 5 => 'Mai', 6 => 'Jun',
        7 => 'Jul', 8 => 'Ago', 9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez',
    ];
    return $months[$month] ?? '';
}
?>
