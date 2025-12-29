<?php
/**
 * View: Back/WhatsApp/queue.php
 * 
 * Lista de Notificações na Fila
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-list-alt mr-2"></i><?= esc($pageHeading ?? 'Fila de Notificações') ?>
    </h1>
    <div>
        <a href="<?= route_to('super.whatsapp') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pendentes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['pending'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Processando</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['processing'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-spinner fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Enviadas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['sent'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Falhas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['failed'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter mr-2"></i>Filtros
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= route_to('super.whatsapp.queue') ?>" class="row align-items-end">
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>>Pendentes</option>
                        <option value="processing" <?= $selectedStatus === 'processing' ? 'selected' : '' ?>>Processando</option>
                        <option value="sent" <?= $selectedStatus === 'sent' ? 'selected' : '' ?>>Enviadas</option>
                        <option value="failed" <?= $selectedStatus === 'failed' ? 'selected' : '' ?>>Falhas</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search mr-1"></i>Filtrar
                </button>
                <a href="<?= route_to('super.whatsapp.queue') ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i>Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Notificações -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-envelope mr-2"></i>Notificações
        </h6>
        <?php if (($stats['pending'] ?? 0) > 0): ?>
            <button type="button" class="btn btn-warning btn-sm" onclick="processQueue()">
                <i class="fas fa-play mr-1"></i>Processar Pendentes
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-gray-300 mb-3"></i>
                <p class="text-muted">Nenhuma notificação encontrada</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="thead-light">
                        <tr>
                            <th width="80">ID</th>
                            <th width="100">Tipo</th>
                            <th width="150">Destinatário</th>
                            <th>Mensagem</th>
                            <th width="100">Status</th>
                            <th width="80">Tent.</th>
                            <th width="150">Criado em</th>
                            <th width="150">Enviado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notifications as $notification): ?>
                            <tr>
                                <td><?= $notification->id ?></td>
                                <td>
                                    <?php if ($notification->type === 'whatsapp'): ?>
                                        <span class="badge badge-success"><i class="fab fa-whatsapp mr-1"></i>WhatsApp</span>
                                    <?php elseif ($notification->type === 'email'): ?>
                                        <span class="badge badge-info"><i class="fas fa-envelope mr-1"></i>Email</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= esc($notification->type) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($notification->recipient) ?></td>
                                <td>
                                    <span title="<?= esc($notification->message) ?>">
                                        <?= esc(substr($notification->message, 0, 50)) ?><?= strlen($notification->message) > 50 ? '...' : '' ?>
                                    </span>
                                    <?php if ($notification->error_message): ?>
                                        <br><small class="text-danger"><?= esc($notification->error_message) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusBadge = [
                                        'pending'    => 'warning',
                                        'processing' => 'info',
                                        'sent'       => 'success',
                                        'failed'     => 'danger',
                                    ];
                                    $statusLabel = [
                                        'pending'    => 'Pendente',
                                        'processing' => 'Processando',
                                        'sent'       => 'Enviada',
                                        'failed'     => 'Falhou',
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $statusBadge[$notification->status] ?? 'secondary' ?>">
                                        <?= $statusLabel[$notification->status] ?? $notification->status ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $notification->attempts >= $notification->max_attempts ? 'danger' : 'secondary' ?>">
                                        <?= $notification->attempts ?>/<?= $notification->max_attempts ?>
                                    </span>
                                </td>
                                <td><small><?= date('d/m/Y H:i', strtotime($notification->created_at)) ?></small></td>
                                <td>
                                    <?php if ($notification->sent_at): ?>
                                        <small><?= date('d/m/Y H:i', strtotime($notification->sent_at)) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- DataTables -->
<link href="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<script src="<?= base_url('back/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
            },
            "order": [[0, "desc"]]
        });
    });
    
    function processQueue() {
        if (!confirm('Deseja processar a fila de notificações agora?')) return;
        
        $.post('<?= route_to('super.whatsapp.process') ?>', {
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(response) {
            alert(response.message);
            location.reload();
        });
    }
</script>
<?= $this->endSection() ?>
