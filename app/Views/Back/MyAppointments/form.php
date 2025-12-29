<?php
/**
 * View: Back/MyAppointments/form.php
 * 
 * Formulário para usuário criar novo agendamento
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-calendar-plus mr-2"></i><?= esc($pageHeading ?? 'Novo Agendamento') ?>
    </h1>
    <a href="<?= route_to('admin.my.appointments') ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Voltar
    </a>
</div>

<!-- Flash Messages -->
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i><?= session('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->has('errorsValidation')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>Erro de validação</h6>
        <ul class="mb-0">
            <?php foreach (session('errorsValidation') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Wizard de Agendamento -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-magic mr-2"></i>Agendar Novo Horário
        </h6>
    </div>
    <div class="card-body">
        <!-- Progress Steps -->
        <div class="mb-4">
            <div class="row text-center">
                <div class="col-3">
                    <div class="step-circle active" id="step-circle-1">1</div>
                    <small class="d-block mt-1">Unidade</small>
                </div>
                <div class="col-3">
                    <div class="step-circle" id="step-circle-2">2</div>
                    <small class="d-block mt-1">Serviço</small>
                </div>
                <div class="col-3">
                    <div class="step-circle" id="step-circle-3">3</div>
                    <small class="d-block mt-1">Profissional</small>
                </div>
                <div class="col-3">
                    <div class="step-circle" id="step-circle-4">4</div>
                    <small class="d-block mt-1">Data/Horário</small>
                </div>
            </div>
        </div>

        <form action="<?= route_to('admin.my.appointments.create') ?>" method="POST" id="appointment-form">
            <?= csrf_field() ?>

            <!-- Step 1: Unidade -->
            <div class="step-content" id="step-1">
                <h5 class="text-primary mb-3"><i class="fas fa-building mr-2"></i>Selecione a Unidade</h5>
                <div class="row">
                    <?php foreach ($units as $unit): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card unit-card h-100" data-unit-id="<?= $unit->id ?>">
                            <div class="card-body text-center">
                                <i class="fas fa-building fa-3x text-primary mb-3"></i>
                                <h5 class="card-title"><?= esc($unit->name) ?></h5>
                                <?php if ($unit->address): ?>
                                <p class="card-text small text-muted">
                                    <i class="fas fa-map-marker-alt mr-1"></i><?= esc($unit->address) ?>
                                </p>
                                <?php endif; ?>
                                <?php if ($unit->phone): ?>
                                <p class="card-text small text-muted">
                                    <i class="fas fa-phone mr-1"></i><?= esc($unit->phone) ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="unit_id" id="unit_id" value="">
            </div>

            <!-- Step 2: Serviço -->
            <div class="step-content d-none" id="step-2">
                <h5 class="text-primary mb-3"><i class="fas fa-concierge-bell mr-2"></i>Selecione o Serviço</h5>
                <div class="row" id="services-container">
                    <div class="col-12 text-center py-5">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                        <span class="ml-2">Carregando serviços...</span>
                    </div>
                </div>
                <input type="hidden" name="service_id" id="service_id" value="">
                <input type="hidden" id="service_duration" value="30">
            </div>

            <!-- Step 3: Profissional -->
            <div class="step-content d-none" id="step-3">
                <h5 class="text-primary mb-3"><i class="fas fa-user-tie mr-2"></i>Selecione o Profissional</h5>
                <div class="row" id="professionals-container">
                    <div class="col-12 text-center py-5">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                        <span class="ml-2">Carregando profissionais...</span>
                    </div>
                </div>
                <input type="hidden" name="professional_id" id="professional_id" value="">
            </div>

            <!-- Step 4: Data e Horário -->
            <div class="step-content d-none" id="step-4">
                <h5 class="text-primary mb-3"><i class="fas fa-clock mr-2"></i>Escolha Data e Horário</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date"><strong>Data do Agendamento</strong></label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date" 
                                   name="date" 
                                   min="<?= date('Y-m-d') ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="available_slot"><strong>Horário Disponível</strong></label>
                            <select class="form-control" id="available_slot" required>
                                <option value="">Selecione uma data primeiro...</option>
                            </select>
                            <div id="slot_loading" class="d-none mt-2">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                <span class="ml-2">Carregando horários...</span>
                            </div>
                            <small class="text-muted" id="slot_info"></small>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="start_time" id="start_time" value="">
                <input type="hidden" name="end_time" id="end_time" value="">

                <div class="form-group">
                    <label for="client_phone"><strong>Telefone para Contato</strong></label>
                    <input type="text" 
                           class="form-control" 
                           id="client_phone" 
                           name="client_phone" 
                           placeholder="(11) 99999-9999"
                           maxlength="20">
                </div>

                <div class="form-group">
                    <label for="notes"><strong>Observações</strong></label>
                    <textarea class="form-control" 
                              id="notes" 
                              name="notes" 
                              rows="3"
                              placeholder="Alguma observação adicional?"></textarea>
                </div>
            </div>

            <!-- Summary -->
            <div class="step-content d-none" id="step-summary">
                <h5 class="text-success mb-3"><i class="fas fa-check-circle mr-2"></i>Confirme seu Agendamento</h5>
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Unidade:</strong> <span id="summary-unit">-</span></p>
                                <p><strong>Serviço:</strong> <span id="summary-service">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Profissional:</strong> <span id="summary-professional">-</span></p>
                                <p><strong>Data/Hora:</strong> <span id="summary-datetime">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <hr>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="btn-prev" disabled>
                    <i class="fas fa-arrow-left mr-1"></i> Anterior
                </button>
                <button type="button" class="btn btn-primary" id="btn-next">
                    Próximo <i class="fas fa-arrow-right ml-1"></i>
                </button>
                <button type="submit" class="btn btn-success d-none" id="btn-submit">
                    <i class="fas fa-check mr-1"></i> Confirmar Agendamento
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e0e0e0;
        color: #666;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    .step-circle.active {
        background-color: #4e73df;
        color: white;
    }
    .step-circle.completed {
        background-color: #1cc88a;
        color: white;
    }
    .unit-card, .service-card, .professional-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .unit-card:hover, .service-card:hover, .professional-card:hover {
        border-color: #4e73df;
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    .unit-card.selected, .service-card.selected, .professional-card.selected {
        border-color: #1cc88a;
        background-color: #f0fff4;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 4;
    
    // Dados selecionados
    let selectedUnit = null;
    let selectedService = null;
    let selectedProfessional = null;
    
    // Selecionar Unidade
    $(document).on('click', '.unit-card', function() {
        $('.unit-card').removeClass('selected');
        $(this).addClass('selected');
        selectedUnit = {
            id: $(this).data('unit-id'),
            name: $(this).find('.card-title').text()
        };
        $('#unit_id').val(selectedUnit.id);
    });
    
    // Selecionar Serviço
    $(document).on('click', '.service-card', function() {
        $('.service-card').removeClass('selected');
        $(this).addClass('selected');
        selectedService = {
            id: $(this).data('service-id'),
            name: $(this).find('.card-title').text(),
            duration: $(this).data('duration') || 30
        };
        $('#service_id').val(selectedService.id);
        $('#service_duration').val(selectedService.duration);
    });
    
    // Selecionar Profissional
    $(document).on('click', '.professional-card', function() {
        $('.professional-card').removeClass('selected');
        $(this).addClass('selected');
        selectedProfessional = {
            id: $(this).data('professional-id'),
            name: $(this).find('.card-title').text()
        };
        $('#professional_id').val(selectedProfessional.id);
    });
    
    // Carregar serviços da unidade
    function loadServices(unitId) {
        $('#services-container').html(`
            <div class="col-12 text-center py-5">
                <span class="spinner-border spinner-border-sm" role="status"></span>
                <span class="ml-2">Carregando serviços...</span>
            </div>
        `);
        
        $.ajax({
            url: '<?= base_url('api/v1/services') ?>',
            data: { unit_id: unitId },
            success: function(response) {
                let html = '';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(service) {
                        html += `
                            <div class="col-md-4 mb-3">
                                <div class="card service-card h-100" data-service-id="${service.id}" data-duration="${service.duration || 30}">
                                    <div class="card-body text-center">
                                        <i class="fas fa-concierge-bell fa-3x text-info mb-3"></i>
                                        <h5 class="card-title">${service.name}</h5>
                                        <p class="card-text small text-muted">${service.description || ''}</p>
                                        <p class="card-text">
                                            <strong class="text-success">R$ ${parseFloat(service.price || 0).toFixed(2)}</strong>
                                            <small class="text-muted">• ${service.duration || 30} min</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="col-12 text-center py-5"><p class="text-muted">Nenhum serviço disponível nesta unidade.</p></div>';
                }
                $('#services-container').html(html);
            },
            error: function() {
                $('#services-container').html('<div class="col-12 text-center py-5"><p class="text-danger">Erro ao carregar serviços.</p></div>');
            }
        });
    }
    
    // Carregar profissionais do serviço
    function loadProfessionals(unitId, serviceId) {
        $('#professionals-container').html(`
            <div class="col-12 text-center py-5">
                <span class="spinner-border spinner-border-sm" role="status"></span>
                <span class="ml-2">Carregando profissionais...</span>
            </div>
        `);
        
        $.ajax({
            url: '<?= base_url('api/v1/professionals') ?>',
            data: { unit_id: unitId, service_id: serviceId },
            success: function(response) {
                let html = '';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(prof) {
                        html += `
                            <div class="col-md-4 mb-3">
                                <div class="card professional-card h-100" data-professional-id="${prof.id}">
                                    <div class="card-body text-center">
                                        <img src="${prof.avatar_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(prof.name) + '&background=4e73df&color=fff&size=64'}" 
                                             class="rounded-circle mb-3" width="64" height="64" alt="${prof.name}">
                                        <h5 class="card-title">${prof.name}</h5>
                                        <p class="card-text small text-muted">${prof.specialty || 'Especialista'}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="col-12 text-center py-5"><p class="text-muted">Nenhum profissional disponível.</p></div>';
                }
                $('#professionals-container').html(html);
            },
            error: function() {
                $('#professionals-container').html('<div class="col-12 text-center py-5"><p class="text-danger">Erro ao carregar profissionais.</p></div>');
            }
        });
    }
    
    // Carregar horários disponíveis
    function loadAvailableSlots() {
        const professionalId = $('#professional_id').val();
        const date = $('#date').val();
        const duration = $('#service_duration').val() || 30;
        
        if (!professionalId || !date) {
            $('#available_slot').html('<option value="">Selecione uma data primeiro...</option>');
            return;
        }
        
        $('#slot_loading').removeClass('d-none');
        $('#available_slot').html('<option value="">Carregando...</option>');
        
        $.ajax({
            url: '<?= route_to('super.appointments.slots') ?>',
            data: {
                professional_id: professionalId,
                date: date,
                duration: duration
            },
            success: function(slots) {
                $('#slot_loading').addClass('d-none');
                
                if (slots.length === 0) {
                    $('#available_slot').html('<option value="">Nenhum horário disponível</option>');
                    $('#slot_info').text('Não há horários disponíveis nesta data. Tente outra data.');
                    return;
                }
                
                let options = '<option value="">Selecione um horário...</option>';
                slots.forEach(function(slot) {
                    options += `<option value="${slot.start}|${slot.end}">${slot.label}</option>`;
                });
                $('#available_slot').html(options);
                $('#slot_info').text(slots.length + ' horário(s) disponível(eis)');
            },
            error: function() {
                $('#slot_loading').addClass('d-none');
                $('#available_slot').html('<option value="">Erro ao carregar</option>');
                $('#slot_info').text('Erro ao carregar horários. Tente novamente.');
            }
        });
    }
    
    // Quando mudar a data
    $('#date').on('change', loadAvailableSlots);
    
    // Quando selecionar horário
    $('#available_slot').on('change', function() {
        const value = $(this).val();
        if (value) {
            const times = value.split('|');
            $('#start_time').val(times[0]);
            $('#end_time').val(times[1]);
        } else {
            $('#start_time').val('');
            $('#end_time').val('');
        }
    });
    
    // Navegação entre steps
    function goToStep(step) {
        // Esconder todos os steps
        $('.step-content').addClass('d-none');
        $('#step-' + step).removeClass('d-none');
        
        // Atualizar circles
        for (let i = 1; i <= totalSteps; i++) {
            const circle = $('#step-circle-' + i);
            circle.removeClass('active completed');
            if (i < step) {
                circle.addClass('completed');
            } else if (i === step) {
                circle.addClass('active');
            }
        }
        
        // Atualizar botões
        $('#btn-prev').prop('disabled', step === 1);
        
        if (step === totalSteps) {
            $('#btn-next').addClass('d-none');
            $('#btn-submit').removeClass('d-none');
            updateSummary();
            $('#step-summary').removeClass('d-none');
        } else {
            $('#btn-next').removeClass('d-none');
            $('#btn-submit').addClass('d-none');
        }
        
        currentStep = step;
    }
    
    // Atualizar resumo
    function updateSummary() {
        $('#summary-unit').text(selectedUnit ? selectedUnit.name : '-');
        $('#summary-service').text(selectedService ? selectedService.name : '-');
        $('#summary-professional').text(selectedProfessional ? selectedProfessional.name : '-');
        
        const date = $('#date').val();
        const slot = $('#available_slot option:selected').text();
        if (date && slot) {
            const dateFormatted = new Date(date + 'T00:00:00').toLocaleDateString('pt-BR');
            $('#summary-datetime').text(dateFormatted + ' às ' + slot);
        } else {
            $('#summary-datetime').text('-');
        }
    }
    
    // Botão Próximo
    $('#btn-next').on('click', function() {
        // Validar step atual
        if (currentStep === 1 && !$('#unit_id').val()) {
            alert('Por favor, selecione uma unidade.');
            return;
        }
        if (currentStep === 2 && !$('#service_id').val()) {
            alert('Por favor, selecione um serviço.');
            return;
        }
        if (currentStep === 3 && !$('#professional_id').val()) {
            alert('Por favor, selecione um profissional.');
            return;
        }
        if (currentStep === 4 && (!$('#date').val() || !$('#start_time').val())) {
            alert('Por favor, selecione uma data e horário.');
            return;
        }
        
        // Ações ao avançar
        if (currentStep === 1) {
            loadServices($('#unit_id').val());
        }
        if (currentStep === 2) {
            loadProfessionals($('#unit_id').val(), $('#service_id').val());
        }
        
        if (currentStep < totalSteps) {
            goToStep(currentStep + 1);
        }
    });
    
    // Botão Anterior
    $('#btn-prev').on('click', function() {
        if (currentStep > 1) {
            goToStep(currentStep - 1);
        }
    });
    
    // Validação antes de enviar
    $('#appointment-form').on('submit', function(e) {
        if (!$('#unit_id').val() || !$('#service_id').val() || !$('#professional_id').val() || !$('#start_time').val()) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios.');
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>
