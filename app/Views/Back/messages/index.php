<?= $this->extend('back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-envelope mr-2"></i><?= esc($title) ?>
    </h1>
    <a href="<?= route_to('admin.messages.create') ?>" class="btn btn-primary">
        <i class="fas fa-plus mr-1"></i> Nova Conversa
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Conversas</h6>
            </div>
            
            <div class="card-body p-0">
                <?php if (empty($conversations)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-4x text-gray-300 mb-3"></i>
                        <p class="text-muted mb-3">Nenhuma conversa ainda</p>
                        <a href="<?= route_to('admin.messages.create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Iniciar Conversa
                        </a>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($conversations as $data): ?>
                            <?php 
                            $conversation = $data['conversation'];
                            $lastMessage = $data['lastMessage'];
                            $unreadCount = $data['unreadCount'];
                            $users = $data['users'];
                            ?>
                            <a href="<?= route_to('admin.messages.show', $conversation->id) ?>" 
                               class="list-group-item list-group-item-action <?= $unreadCount > 0 ? 'bg-light-blue' : '' ?>">
                                <div class="d-flex align-items-center">
                                    <!-- Avatar -->
                                    <div class="mr-3">
                                        <?php if ($conversation->type === 'group'): ?>
                                            <div class="avatar-group-stack">
                                                <i class="fas fa-users fa-2x text-primary"></i>
                                            </div>
                                        <?php else: ?>
                                            <?php 
                                            $otherUser = null;
                                            foreach ($users as $user) {
                                                if ($user->id !== session('user_id')) {
                                                    $otherUser = $user;
                                                    break;
                                                }
                                            }
                                            ?>
                                            <?php if ($otherUser): ?>
                                                <img src="<?= $otherUser->avatarUrl(50) ?>" class="rounded-circle" width="50" height="50" alt="Avatar">
                                            <?php else: ?>
                                                <i class="fas fa-user fa-2x text-gray-400"></i>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Conteúdo -->
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-truncate <?= $unreadCount > 0 ? 'font-weight-bold' : '' ?>">
                                                <?= esc($data['title']) ?>
                                            </h6>
                                            <?php if ($lastMessage): ?>
                                                <small class="text-muted"><?= $lastMessage->timeAgo() ?></small>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($lastMessage): ?>
                                            <p class="mb-0 text-muted small text-truncate <?= $unreadCount > 0 ? 'font-weight-bold' : '' ?>">
                                                <?php if ($lastMessage->sender_id === session('user_id')): ?>
                                                    <span class="text-primary">Você:</span>
                                                <?php endif; ?>
                                                <?= $lastMessage->preview(60) ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="mb-0 text-muted small font-italic">Nenhuma mensagem ainda</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Badge não lidas -->
                                    <?php if ($unreadCount > 0): ?>
                                        <div class="ml-2">
                                            <span class="badge badge-primary badge-pill"><?= $unreadCount ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .bg-light-blue {
        background-color: #e8f4fd !important;
    }
    .min-width-0 {
        min-width: 0;
    }
    .avatar-group-stack {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e3e6f0;
        border-radius: 50%;
    }
</style>
<?= $this->endSection() ?>
