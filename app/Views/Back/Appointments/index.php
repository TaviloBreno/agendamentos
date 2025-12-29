<?php
/**
 * View: Back/Appointments/index.php
 * 
 * Listagem de Agendamentos com Calendário e Tabela
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('css') ?>
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
<style>
    .fc-event {
        cursor: pointer;
    }
    .stats-card {
        border-left: 4px solid;
    }
    .stats-card.scheduled { border-color: #f6c23e; }
    .stats-card.confirmed { border-color: #36b9cc; }
    .stats-card.completed { border-color: #1cc88a; }
    .stats-card.cancelled { border-color: #e74a3b; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-calendar-alt mr-2"></i><?= esc($pageHeading ?? 'Agenda') ?>
    </h1>
    <a href="<?= route_to('super.appointments.new') ?>" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Novo Agendamento
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

<?php if (session()->has('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i><?= session('warning') ?>
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

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card scheduled shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Hoje - Agendados</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['today']['scheduled'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card confirmed shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Hoje - Confirmados</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['today']['confirmed'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card completed shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Concluídos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['all']['completed'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-double fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card cancelled shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Cancelados</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['all']['cancelled'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs: Calendário / Lista -->
<ul class="nav nav-tabs mb-4" id="viewTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="calendar-tab" data-toggle="tab" href="#calendar" role="tab">
            <i class="fas fa-calendar mr-1"></i> Calendário
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="list-tab" data-toggle="tab" href="#list" role="tab">
            <i class="fas fa-list mr-1"></i> Lista
        </a>
    </li>
</ul>

<div class="tab-content" id="viewTabsContent">
    <!-- Calendário -->
    <div class="tab-pane fade show active" id="calendar" role="tabpanel">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div id="fullCalendar"></div>
            </div>
        </div>
    </div>

    <!-- Lista -->
    <div class="tab-pane fade" id="list" role="tabpanel">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list mr-2"></i>Lista de Agendamentos
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?= $tableHtml ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- DataTables -->
<link href="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<script src="<?= base_url('back/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>

<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/pt-br.js"></script>

<script>
    $(document).ready(function() {
        // DataTable
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
            },
            "order": [[0, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ]
        });

        // FullCalendar
        var calendarEl = document.getElementById('fullCalendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                day: 'Dia',
                list: 'Lista'
            },
            events: '<?= route_to('super.appointments.calendar') ?>',
            eventClick: function(info) {
                window.location.href = '<?= base_url('super/appointments') ?>/' + info.event.id;
            },
            eventDidMount: function(info) {
                $(info.el).tooltip({
                    title: info.event.title + ' - ' + info.event.extendedProps.status,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
        calendar.render();
    });
</script>
<?= $this->endSection() ?>
