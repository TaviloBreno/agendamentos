<?= $this->extend('Front/layout/main') ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="container">
        <!-- Título da Página -->
        <div class="has-text-centered mb-6">
            <h1 class="title is-2">
                <span class="icon-text">
                    <span class="icon has-text-primary">
                        <i class="fas fa-calendar-plus"></i>
                    </span>
                    <span>Agendar Horário</span>
                </span>
            </h1>
            <p class="subtitle is-5 has-text-grey">
                Siga os passos abaixo para realizar seu agendamento
            </p>
        </div>

        <!-- Wizard Steps Indicator -->
        <div class="wizard-steps mb-6">
            <div class="step-item active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-label">Unidade</div>
            </div>
            <div class="step-line"></div>
            <div class="step-item" data-step="2">
                <div class="step-number">2</div>
                <div class="step-label">Serviço</div>
            </div>
            <div class="step-line"></div>
            <div class="step-item" data-step="3">
                <div class="step-number">3</div>
                <div class="step-label">Profissional</div>
            </div>
            <div class="step-line"></div>
            <div class="step-item" data-step="4">
                <div class="step-number">4</div>
                <div class="step-label">Data</div>
            </div>
            <div class="step-line"></div>
            <div class="step-item" data-step="5">
                <div class="step-number">5</div>
                <div class="step-label">Horário</div>
            </div>
            <div class="step-line"></div>
            <div class="step-item" data-step="6">
                <div class="step-number">6</div>
                <div class="step-label">Confirmação</div>
            </div>
        </div>

        <!-- Wizard Content -->
        <div class="box wizard-content">
            
            <!-- Step 1: Selecionar Unidade -->
            <div class="wizard-step" id="step-1">
                <h3 class="title is-4 mb-4">
                    <span class="icon has-text-info mr-2"><i class="fas fa-building"></i></span>
                    Escolha a Unidade
                </h3>
                
                <div class="columns is-multiline" id="units-list">
                    <?php foreach ($units as $unit): ?>
                    <div class="column is-4">
                        <div class="selection-card" data-unit-id="<?= $unit->id ?>" data-unit-name="<?= esc($unit->name) ?>">
                            <div class="selection-card-icon">
                                <span class="icon is-large has-text-primary">
                                    <i class="fas fa-hospital fa-2x"></i>
                                </span>
                            </div>
                            <h4 class="selection-card-title"><?= esc($unit->name) ?></h4>
                            <p class="selection-card-subtitle has-text-grey">
                                <?php if ($unit->city): ?>
                                    <span class="icon-text">
                                        <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
                                        <span><?= esc($unit->city) ?><?= $unit->state ? ', ' . esc($unit->state) : '' ?></span>
                                    </span>
                                <?php endif; ?>
                            </p>
                            <?php if ($unit->phone): ?>
                            <p class="selection-card-info has-text-grey is-size-7">
                                <span class="icon"><i class="fas fa-phone"></i></span>
                                <?= esc($unit->phone) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($units)): ?>
                <div class="notification is-warning">
                    <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                    Nenhuma unidade disponível no momento.
                </div>
                <?php endif; ?>
            </div>

            <!-- Step 2: Selecionar Serviço -->
            <div class="wizard-step" id="step-2" style="display: none;">
                <h3 class="title is-4 mb-4">
                    <span class="icon has-text-info mr-2"><i class="fas fa-concierge-bell"></i></span>
                    Escolha o Serviço
                </h3>
                
                <div class="columns is-multiline" id="services-list">
                    <!-- Carregado via AJAX -->
                </div>
                
                <div id="services-loading" class="has-text-centered py-6" style="display: none;">
                    <span class="icon is-large has-text-primary">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                    </span>
                    <p class="mt-3">Carregando serviços...</p>
                </div>
                
                <div id="services-empty" class="notification is-warning" style="display: none;">
                    <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                    Nenhum serviço disponível nesta unidade.
                </div>
                
                <div class="mt-5">
                    <button type="button" class="button is-light" onclick="prevStep()">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Voltar</span>
                    </button>
                </div>
            </div>

            <!-- Step 3: Selecionar Profissional -->
            <div class="wizard-step" id="step-3" style="display: none;">
                <h3 class="title is-4 mb-4">
                    <span class="icon has-text-info mr-2"><i class="fas fa-user-md"></i></span>
                    Escolha o Profissional
                </h3>
                
                <div class="columns is-multiline" id="professionals-list">
                    <!-- Carregado via AJAX -->
                </div>
                
                <div id="professionals-loading" class="has-text-centered py-6" style="display: none;">
                    <span class="icon is-large has-text-primary">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                    </span>
                    <p class="mt-3">Carregando profissionais...</p>
                </div>
                
                <div id="professionals-empty" class="notification is-warning" style="display: none;">
                    <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                    Nenhum profissional disponível para este serviço.
                </div>
                
                <div class="mt-5">
                    <button type="button" class="button is-light" onclick="prevStep()">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Voltar</span>
                    </button>
                </div>
            </div>

            <!-- Step 4: Selecionar Data -->
            <div class="wizard-step" id="step-4" style="display: none;">
                <h3 class="title is-4 mb-4">
                    <span class="icon has-text-info mr-2"><i class="fas fa-calendar-alt"></i></span>
                    Escolha a Data
                </h3>
                
                <!-- Abas de Meses -->
                <div class="month-tabs" id="month-tabs">
                    <!-- Carregado via AJAX -->
                </div>
                
                <!-- Calendário -->
                <div class="calendar-container mt-4" id="calendar-container">
                    <div class="calendar-header">
                        <div class="calendar-weekdays">
                            <div class="calendar-weekday">Dom</div>
                            <div class="calendar-weekday">Seg</div>
                            <div class="calendar-weekday">Ter</div>
                            <div class="calendar-weekday">Qua</div>
                            <div class="calendar-weekday">Qui</div>
                            <div class="calendar-weekday">Sex</div>
                            <div class="calendar-weekday">Sáb</div>
                        </div>
                    </div>
                    <div class="calendar-grid" id="calendar-grid">
                        <!-- Carregado via AJAX -->
                    </div>
                </div>
                
                <div id="calendar-loading" class="has-text-centered py-6" style="display: none;">
                    <span class="icon is-large has-text-primary">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                    </span>
                    <p class="mt-3">Carregando calendário...</p>
                </div>
                
                <div class="mt-4">
                    <p class="is-size-7 has-text-grey">
                        <span class="calendar-day-legend today"></span> Hoje &nbsp;
                        <span class="calendar-day-legend available"></span> Disponível &nbsp;
                        <span class="calendar-day-legend unavailable"></span> Indisponível
                    </p>
                </div>
                
                <div class="mt-5">
                    <button type="button" class="button is-light" onclick="prevStep()">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Voltar</span>
                    </button>
                </div>
            </div>

            <!-- Step 5: Selecionar Horário -->
            <div class="wizard-step" id="step-5" style="display: none;">
                <h3 class="title is-4 mb-4">
                    <span class="icon has-text-info mr-2"><i class="fas fa-clock"></i></span>
                    Escolha o Horário
                </h3>
                
                <p class="mb-4 has-text-grey">
                    <span class="icon"><i class="fas fa-calendar"></i></span>
                    Data selecionada: <strong id="selected-date-display">-</strong>
                </p>
                
                <div class="time-slots" id="time-slots">
                    <!-- Carregado via AJAX -->
                </div>
                
                <div id="times-loading" class="has-text-centered py-6" style="display: none;">
                    <span class="icon is-large has-text-primary">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                    </span>
                    <p class="mt-3">Carregando horários...</p>
                </div>
                
                <div id="times-empty" class="notification is-warning" style="display: none;">
                    <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                    Nenhum horário disponível para esta data.
                </div>
                
                <div class="mt-5">
                    <button type="button" class="button is-light" onclick="prevStep()">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Voltar</span>
                    </button>
                </div>
            </div>

            <!-- Step 6: Confirmação -->
            <div class="wizard-step" id="step-6" style="display: none;">
                <h3 class="title is-4 mb-4">
                    <span class="icon has-text-success mr-2"><i class="fas fa-check-circle"></i></span>
                    Confirmar Agendamento
                </h3>
                
                <!-- Resumo do Agendamento -->
                <div class="box has-background-light mb-5">
                    <h4 class="title is-5 mb-4">Resumo do Agendamento</h4>
                    
                    <div class="columns is-multiline">
                        <div class="column is-6">
                            <p class="mb-2">
                                <span class="icon has-text-primary"><i class="fas fa-building"></i></span>
                                <strong>Unidade:</strong>
                            </p>
                            <p class="ml-5 mb-3" id="summary-unit">-</p>
                        </div>
                        
                        <div class="column is-6">
                            <p class="mb-2">
                                <span class="icon has-text-primary"><i class="fas fa-concierge-bell"></i></span>
                                <strong>Serviço:</strong>
                            </p>
                            <p class="ml-5 mb-3" id="summary-service">-</p>
                        </div>
                        
                        <div class="column is-6">
                            <p class="mb-2">
                                <span class="icon has-text-primary"><i class="fas fa-user-md"></i></span>
                                <strong>Profissional:</strong>
                            </p>
                            <p class="ml-5 mb-3" id="summary-professional">-</p>
                        </div>
                        
                        <div class="column is-6">
                            <p class="mb-2">
                                <span class="icon has-text-primary"><i class="fas fa-calendar-alt"></i></span>
                                <strong>Data e Horário:</strong>
                            </p>
                            <p class="ml-5 mb-3" id="summary-datetime">-</p>
                        </div>
                        
                        <div class="column is-6">
                            <p class="mb-2">
                                <span class="icon has-text-primary"><i class="fas fa-money-bill-wave"></i></span>
                                <strong>Valor:</strong>
                            </p>
                            <p class="ml-5 mb-3" id="summary-price">-</p>
                        </div>
                        
                        <div class="column is-6">
                            <p class="mb-2">
                                <span class="icon has-text-primary"><i class="fas fa-clock"></i></span>
                                <strong>Duração:</strong>
                            </p>
                            <p class="ml-5 mb-3" id="summary-duration">-</p>
                        </div>
                    </div>
                </div>
                
                <!-- Formulário de Dados do Cliente -->
                <div class="box">
                    <h4 class="title is-5 mb-4">
                        <span class="icon has-text-info"><i class="fas fa-user"></i></span>
                        Seus Dados
                    </h4>
                    
                    <form id="booking-form">
                        <div class="columns">
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Nome Completo <span class="has-text-danger">*</span></label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="client_name" id="client_name" 
                                               placeholder="Seu nome completo" required
                                               value="<?= session()->get('user')?->name ?? '' ?>">
                                        <span class="icon is-left"><i class="fas fa-user"></i></span>
                                    </div>
                                    <p class="help is-danger" id="error-client_name"></p>
                                </div>
                            </div>
                            
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">E-mail <span class="has-text-danger">*</span></label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="email" name="client_email" id="client_email" 
                                               placeholder="seu@email.com" required
                                               value="<?= session()->get('user')?->email ?? '' ?>">
                                        <span class="icon is-left"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <p class="help is-danger" id="error-client_email"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="columns">
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Telefone</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="tel" name="client_phone" id="client_phone" 
                                               placeholder="(00) 00000-0000">
                                        <span class="icon is-left"><i class="fas fa-phone"></i></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Observações</label>
                                    <div class="control">
                                        <textarea class="textarea" name="notes" id="notes" 
                                                  placeholder="Alguma observação sobre o agendamento?"
                                                  rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campos ocultos -->
                        <input type="hidden" name="unit_id" id="form_unit_id">
                        <input type="hidden" name="service_id" id="form_service_id">
                        <input type="hidden" name="professional_id" id="form_professional_id">
                        <input type="hidden" name="date" id="form_date">
                        <input type="hidden" name="time" id="form_time">
                    </form>
                </div>
                
                <!-- Alert de Erro -->
                <div id="booking-error" class="notification is-danger" style="display: none;">
                    <button class="delete" onclick="this.parentElement.style.display='none'"></button>
                    <span id="booking-error-message"></span>
                </div>
                
                <!-- Botões -->
                <div class="mt-5 is-flex is-justify-content-space-between">
                    <button type="button" class="button is-light" onclick="prevStep()">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Voltar</span>
                    </button>
                    
                    <button type="button" class="button is-success is-medium" id="btn-confirm" onclick="submitBooking()">
                        <span class="icon"><i class="fas fa-check"></i></span>
                        <span>Confirmar Agendamento</span>
                    </button>
                </div>
            </div>

            <!-- Step Sucesso -->
            <div class="wizard-step" id="step-success" style="display: none;">
                <div class="has-text-centered py-6">
                    <span class="icon is-large has-text-success">
                        <i class="fas fa-check-circle fa-4x"></i>
                    </span>
                    
                    <h3 class="title is-3 mt-5 has-text-success">Agendamento Realizado!</h3>
                    
                    <p class="subtitle is-5 has-text-grey">
                        Seu agendamento foi confirmado com sucesso.
                    </p>
                    
                    <div class="box has-background-light mt-5" style="max-width: 500px; margin: 0 auto;">
                        <p class="mb-2">
                            <strong>Número do Agendamento:</strong> 
                            <span id="success-appointment-id" class="tag is-primary is-medium">#</span>
                        </p>
                        <p class="is-size-7 has-text-grey">
                            Um e-mail de confirmação será enviado para você.
                        </p>
                    </div>
                    
                    <div class="buttons is-centered mt-6">
                        <a href="<?= base_url('agendar') ?>" class="button is-primary">
                            <span class="icon"><i class="fas fa-plus"></i></span>
                            <span>Novo Agendamento</span>
                        </a>
                        
                        <a href="<?= base_url('meus-agendamentos') ?>" class="button is-info">
                            <span class="icon"><i class="fas fa-list"></i></span>
                            <span>Meus Agendamentos</span>
                        </a>
                        
                        <a href="<?= base_url() ?>" class="button is-light">
                            <span class="icon"><i class="fas fa-home"></i></span>
                            <span>Voltar ao Início</span>
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
// =========================================================================
// ESTADO DO WIZARD
// =========================================================================

let currentStep = 1;
const totalSteps = 6;

// Dados selecionados
let selectedData = {
    unit: { id: null, name: '' },
    service: { id: null, name: '', price: '', price_formatted: '', duration: '', duration_formatted: '' },
    professional: { id: null, name: '' },
    date: { value: '', formatted: '' },
    time: { value: '', formatted: '' }
};

// Pre-selected unit from URL
const preSelectedUnit = <?= json_encode($preSelectedUnit ?? null) ?>;

// =========================================================================
// INICIALIZAÇÃO
// =========================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Se tiver unidade pré-selecionada, selecionar automaticamente
    if (preSelectedUnit) {
        const unitCard = document.querySelector(`[data-unit-id="${preSelectedUnit}"]`);
        if (unitCard) {
            selectUnit(preSelectedUnit, unitCard.dataset.unitName);
        }
    }
    
    // Setup listeners dos cards de unidade
    document.querySelectorAll('#units-list .selection-card').forEach(card => {
        card.addEventListener('click', function() {
            const unitId = this.dataset.unitId;
            const unitName = this.dataset.unitName;
            selectUnit(unitId, unitName);
        });
    });
});

// =========================================================================
// NAVEGAÇÃO DO WIZARD
// =========================================================================

function goToStep(step) {
    // Esconder step atual
    document.querySelectorAll('.wizard-step').forEach(s => s.style.display = 'none');
    
    // Mostrar novo step
    document.getElementById(`step-${step}`).style.display = 'block';
    
    // Atualizar indicadores
    document.querySelectorAll('.step-item').forEach(item => {
        const itemStep = parseInt(item.dataset.step);
        item.classList.remove('active', 'completed');
        
        if (itemStep < step) {
            item.classList.add('completed');
        } else if (itemStep === step) {
            item.classList.add('active');
        }
    });
    
    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep() {
    if (currentStep > 1) {
        goToStep(currentStep - 1);
    }
}

// =========================================================================
// STEP 1: SELEÇÃO DE UNIDADE
// =========================================================================

function selectUnit(unitId, unitName) {
    selectedData.unit = { id: unitId, name: unitName };
    
    // Destacar card selecionado
    document.querySelectorAll('#units-list .selection-card').forEach(c => c.classList.remove('selected'));
    document.querySelector(`[data-unit-id="${unitId}"]`).classList.add('selected');
    
    // Carregar serviços
    loadServices(unitId);
    
    // Avançar para próximo step
    goToStep(2);
}

// =========================================================================
// STEP 2: SELEÇÃO DE SERVIÇO
// =========================================================================

function loadServices(unitId) {
    const container = document.getElementById('services-list');
    const loading = document.getElementById('services-loading');
    const empty = document.getElementById('services-empty');
    
    container.innerHTML = '';
    loading.style.display = 'block';
    empty.style.display = 'none';
    
    fetch(`<?= base_url('api/services') ?>?unit_id=${unitId}`)
        .then(response => response.json())
        .then(services => {
            loading.style.display = 'none';
            
            if (services.length === 0) {
                empty.style.display = 'block';
                return;
            }
            
            services.forEach(service => {
                container.innerHTML += `
                    <div class="column is-4">
                        <div class="selection-card" onclick="selectService(${service.id}, '${escapeHtml(service.name)}', '${service.price}', '${service.price_formatted}', ${service.duration}, '${service.duration_formatted}')">
                            <div class="selection-card-icon">
                                <span class="icon is-large has-text-info">
                                    <i class="fas fa-clipboard-list fa-2x"></i>
                                </span>
                            </div>
                            <h4 class="selection-card-title">${escapeHtml(service.name)}</h4>
                            <p class="selection-card-subtitle has-text-grey">
                                ${service.description ? escapeHtml(service.description) : ''}
                            </p>
                            <div class="selection-card-footer">
                                <span class="tag is-success is-light">${service.price_formatted}</span>
                                <span class="tag is-info is-light">
                                    <span class="icon is-small"><i class="fas fa-clock"></i></span>
                                    ${service.duration_formatted}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            });
        })
        .catch(error => {
            loading.style.display = 'none';
            empty.style.display = 'block';
            console.error('Erro ao carregar serviços:', error);
        });
}

function selectService(serviceId, serviceName, price, priceFormatted, duration, durationFormatted) {
    selectedData.service = { 
        id: serviceId, 
        name: serviceName, 
        price: price,
        price_formatted: priceFormatted,
        duration: duration,
        duration_formatted: durationFormatted
    };
    
    // Destacar card selecionado
    document.querySelectorAll('#services-list .selection-card').forEach(c => c.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    
    // Carregar profissionais
    loadProfessionals();
    
    // Avançar
    goToStep(3);
}

// =========================================================================
// STEP 3: SELEÇÃO DE PROFISSIONAL
// =========================================================================

function loadProfessionals() {
    const container = document.getElementById('professionals-list');
    const loading = document.getElementById('professionals-loading');
    const empty = document.getElementById('professionals-empty');
    
    container.innerHTML = '';
    loading.style.display = 'block';
    empty.style.display = 'none';
    
    const params = new URLSearchParams({
        unit_id: selectedData.unit.id,
        service_id: selectedData.service.id
    });
    
    fetch(`<?= base_url('api/professionals') ?>?${params}`)
        .then(response => response.json())
        .then(professionals => {
            loading.style.display = 'none';
            
            if (professionals.length === 0) {
                empty.style.display = 'block';
                return;
            }
            
            professionals.forEach(professional => {
                container.innerHTML += `
                    <div class="column is-4">
                        <div class="selection-card" onclick="selectProfessional(${professional.id}, '${escapeHtml(professional.name)}')">
                            <div class="selection-card-avatar">
                                <figure class="image is-96x96">
                                    <img class="is-rounded" src="${professional.avatar}" alt="${escapeHtml(professional.name)}">
                                </figure>
                            </div>
                            <h4 class="selection-card-title">${escapeHtml(professional.name)}</h4>
                            <p class="selection-card-subtitle has-text-grey">
                                ${professional.specialty ? escapeHtml(professional.specialty) : ''}
                            </p>
                        </div>
                    </div>
                `;
            });
        })
        .catch(error => {
            loading.style.display = 'none';
            empty.style.display = 'block';
            console.error('Erro ao carregar profissionais:', error);
        });
}

function selectProfessional(professionalId, professionalName) {
    selectedData.professional = { id: professionalId, name: professionalName };
    
    // Destacar card selecionado
    document.querySelectorAll('#professionals-list .selection-card').forEach(c => c.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    
    // Carregar meses e calendário
    loadMonths();
    
    // Avançar
    goToStep(4);
}

// =========================================================================
// STEP 4: SELEÇÃO DE DATA
// =========================================================================

function loadMonths() {
    fetch('<?= base_url('api/months') ?>')
        .then(response => response.json())
        .then(months => {
            const tabsContainer = document.getElementById('month-tabs');
            tabsContainer.innerHTML = '';
            
            months.forEach((month, index) => {
                const isActive = index === 0 ? 'active' : '';
                tabsContainer.innerHTML += `
                    <button type="button" class="month-tab ${isActive}" 
                            onclick="selectMonth(${month.year}, ${month.month}, this)">
                        ${month.label}
                    </button>
                `;
            });
            
            // Carregar primeiro mês
            if (months.length > 0) {
                loadCalendar(months[0].year, months[0].month);
            }
        });
}

function selectMonth(year, month, button) {
    // Atualizar tab ativa
    document.querySelectorAll('.month-tab').forEach(t => t.classList.remove('active'));
    button.classList.add('active');
    
    // Carregar calendário
    loadCalendar(year, month);
}

function loadCalendar(year, month) {
    const container = document.getElementById('calendar-grid');
    const loading = document.getElementById('calendar-loading');
    
    container.innerHTML = '';
    loading.style.display = 'block';
    
    const params = new URLSearchParams({
        year: year,
        month: month,
        professional_id: selectedData.professional.id
    });
    
    fetch(`<?= base_url('api/calendar') ?>?${params}`)
        .then(response => response.json())
        .then(calendar => {
            loading.style.display = 'none';
            
            calendar.days.forEach(day => {
                if (day.is_empty) {
                    container.innerHTML += `<div class="calendar-day empty"></div>`;
                } else {
                    let classes = ['calendar-day'];
                    let clickable = false;
                    
                    if (day.is_today) classes.push('today');
                    if (day.is_past) classes.push('past');
                    if (day.is_weekend) classes.push('weekend');
                    
                    if (day.is_available) {
                        classes.push('available');
                        clickable = true;
                    } else {
                        classes.push('unavailable');
                    }
                    
                    const onclick = clickable ? `onclick="selectDate('${day.date}', ${day.day}, ${month}, ${year})"` : '';
                    
                    container.innerHTML += `
                        <div class="${classes.join(' ')}" ${onclick}>
                            ${day.day}
                        </div>
                    `;
                }
            });
        })
        .catch(error => {
            loading.style.display = 'none';
            console.error('Erro ao carregar calendário:', error);
        });
}

function selectDate(date, day, month, year) {
    const monthNames = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
                        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    
    selectedData.date = { 
        value: date, 
        formatted: `${day} de ${monthNames[month]} de ${year}` 
    };
    
    // Destacar dia selecionado
    document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    
    // Carregar horários
    loadTimeSlots();
    
    // Avançar
    goToStep(5);
}

// =========================================================================
// STEP 5: SELEÇÃO DE HORÁRIO
// =========================================================================

function loadTimeSlots() {
    const container = document.getElementById('time-slots');
    const loading = document.getElementById('times-loading');
    const empty = document.getElementById('times-empty');
    
    container.innerHTML = '';
    loading.style.display = 'block';
    empty.style.display = 'none';
    
    document.getElementById('selected-date-display').textContent = selectedData.date.formatted;
    
    const params = new URLSearchParams({
        date: selectedData.date.value,
        professional_id: selectedData.professional.id,
        service_id: selectedData.service.id,
        unit_id: selectedData.unit.id
    });
    
    fetch(`<?= base_url('api/hours') ?>?${params}`)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.error) {
                empty.textContent = data.error;
                empty.style.display = 'block';
                return;
            }
            
            if (data.message) {
                empty.innerHTML = `<span class="icon"><i class="fas fa-info-circle"></i></span> ${data.message}`;
                empty.classList.remove('is-danger');
                empty.classList.add('is-warning');
                empty.style.display = 'block';
                return;
            }
            
            const hours = data.hours || [];
            
            if (hours.length === 0) {
                empty.style.display = 'block';
                return;
            }
            
            hours.forEach(hour => {
                const classes = ['time-slot'];
                if (!hour.available) classes.push('unavailable');
                
                const onclick = hour.available ? `onclick="selectTime('${hour.time}')"` : '';
                
                container.innerHTML += `
                    <div class="${classes.join(' ')}" ${onclick}>
                        <span class="icon"><i class="fas fa-clock"></i></span>
                        ${hour.formatted}
                    </div>
                `;
            });
        })
        .catch(error => {
            loading.style.display = 'none';
            empty.style.display = 'block';
            console.error('Erro ao carregar horários:', error);
        });
}

function selectTime(time) {
    selectedData.time = { value: time, formatted: time };
    
    // Destacar horário selecionado
    document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    
    // Atualizar resumo
    updateSummary();
    
    // Avançar
    goToStep(6);
}

// =========================================================================
// STEP 6: CONFIRMAÇÃO E ENVIO
// =========================================================================

function updateSummary() {
    document.getElementById('summary-unit').textContent = selectedData.unit.name;
    document.getElementById('summary-service').textContent = selectedData.service.name;
    document.getElementById('summary-professional').textContent = selectedData.professional.name;
    document.getElementById('summary-datetime').textContent = `${selectedData.date.formatted} às ${selectedData.time.formatted}`;
    document.getElementById('summary-price').textContent = selectedData.service.price_formatted;
    document.getElementById('summary-duration').textContent = selectedData.service.duration_formatted;
    
    // Preencher campos ocultos
    document.getElementById('form_unit_id').value = selectedData.unit.id;
    document.getElementById('form_service_id').value = selectedData.service.id;
    document.getElementById('form_professional_id').value = selectedData.professional.id;
    document.getElementById('form_date').value = selectedData.date.value;
    document.getElementById('form_time').value = selectedData.time.value;
}

function submitBooking() {
    const form = document.getElementById('booking-form');
    const btn = document.getElementById('btn-confirm');
    const errorContainer = document.getElementById('booking-error');
    const errorMessage = document.getElementById('booking-error-message');
    
    // Limpar erros anteriores
    errorContainer.style.display = 'none';
    document.querySelectorAll('.help.is-danger').forEach(el => el.textContent = '');
    document.querySelectorAll('.input, .textarea').forEach(el => el.classList.remove('is-danger'));
    
    // Coletar dados do formulário
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => data[key] = value);
    
    // Desabilitar botão
    btn.disabled = true;
    btn.innerHTML = '<span class="icon"><i class="fas fa-spinner fa-spin"></i></span><span>Processando...</span>';
    
    fetch('<?= base_url('api/schedule') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        btn.disabled = false;
        btn.innerHTML = '<span class="icon"><i class="fas fa-check"></i></span><span>Confirmar Agendamento</span>';
        
        if (result.success) {
            // Mostrar tela de sucesso
            document.getElementById('success-appointment-id').textContent = `#${result.appointment_id}`;
            goToStep('success');
            
            // Atualizar steps para todos completos
            document.querySelectorAll('.step-item').forEach(item => {
                item.classList.add('completed');
                item.classList.remove('active');
            });
        } else {
            // Mostrar erros
            if (result.errors) {
                for (const [field, error] of Object.entries(result.errors)) {
                    const input = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-danger');
                    }
                    const errorEl = document.getElementById(`error-${field}`);
                    if (errorEl) {
                        errorEl.textContent = error;
                    }
                }
            }
            
            errorMessage.textContent = result.message || 'Erro ao criar agendamento. Verifique os dados e tente novamente.';
            errorContainer.style.display = 'block';
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<span class="icon"><i class="fas fa-check"></i></span><span>Confirmar Agendamento</span>';
        
        errorMessage.textContent = 'Erro de conexão. Verifique sua internet e tente novamente.';
        errorContainer.style.display = 'block';
        console.error('Erro:', error);
    });
}

// =========================================================================
// UTILITÁRIOS
// =========================================================================

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
<?= $this->endSection() ?>
