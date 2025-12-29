<?= $this->extend('back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-bell mr-2"></i><?= esc($title) ?>
    </h1>
    <div>
        <?php if ($unreadCount > 0): ?>
            <form action="<?= route_to('admin.notifications.read.all') ?>" method="POST" class="d-inline">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-check-double mr-1"></i> Marcar todas como lidas
                </button>
            </form>
        <?php endif; ?>
        <?php if ($filter === 'read'): ?>
            <form action="<?= route_to('admin.notifications.clear') ?>" method="POST" class="d-inline">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir todas as notificações lidas?')">
                    <i class="fas fa-trash mr-1"></i> Limpar lidas
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link <?= !$filter ? 'active' : '' ?>" href="<?= route_to('admin.notifications') ?>">
                    Todas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'unread' ? 'active' : '' ?>" href="<?= route_to('admin.notifications') ?>?filter=unread">
                    <i class="fas fa-circle text-primary mr-1" style="font-size: 8px;"></i>
                    Não lidas
                    <?php if ($unreadCount > 0): ?>
                        <span class="badge badge-primary"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'read' ? 'active' : '' ?>" href="<?= route_to('admin.notifications') ?>?filter=read">
                    Lidas
                </a>
            </li>
        </ul>
    </div>
    
    <div class="card-body p-0">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-4x text-gray-300 mb-3"></i>
                <p class="text-muted mb-0">Nenhuma notificação encontrada</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notification): ?>
                    <div class="list-group-item list-group-item-action d-flex align-items-start <?= !$notification->isRead() ? 'bg-light-blue' : '' ?>">
                        <div class="mr-3 mt-1">
                            <?= $notification->iconHtml() ?>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 <?= !$notification->isRead() ? 'font-weight-bold' : '' ?>">
                                        <?= esc($notification->title) ?>
                                    </h6>
                                    <?php if ($notification->message): ?>
                                        <p class="mb-1 text-muted small"><?= esc($notification->message) ?></p>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?= $notification->timeAgo() ?>
                                    </small>
                                </div>
                                <div class="d-flex">
                                    <?php if (!$notification->isRead()): ?>
                                        <form action="<?= route_to('admin.notifications.read', $notification->id) ?>" method="POST" class="mr-1">
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Marcar como lida">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($notification->link): ?>
                                        <a href="<?= esc($notification->link) ?>" class="btn btn-sm btn-outline-info mr-1" title="Ver">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                    <form action="<?= route_to('admin.notifications.delete', $notification->id) ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Excluir esta notificação?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="card-footer">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .bg-light-blue {
        background-color: #e8f4fd !important;
    }
    .icon-circle {
        height: 2.5rem;
        width: 2.5rem;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<?= $this->endSection() ?>
