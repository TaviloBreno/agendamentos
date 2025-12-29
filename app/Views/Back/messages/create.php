<?= $this->extend('back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <a href="<?= route_to('admin.messages') ?>" class="text-decoration-none text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>
        </a>
        <?= esc($title) ?>
    </h1>
</div>

<div class="row">
    <!-- Nova Conversa Direta -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user mr-2"></i>Conversa Direta
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">Selecione um usuário para iniciar uma conversa privada:</p>
                
                <?php if (empty($users)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users-slash fa-3x mb-3"></i>
                        <p>Nenhum usuário disponível</p>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($users as $user): ?>
                            <a href="<?= route_to('admin.messages.direct', $user->id) ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                                <img src="<?= $user->avatarUrl(40) ?>" class="rounded-circle mr-3" width="40" height="40" alt="Avatar">
                                <div>
                                    <h6 class="mb-0"><?= esc($user->name) ?></h6>
                                    <small class="text-muted"><?= esc($user->email) ?></small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Novo Grupo -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users mr-2"></i>Criar Grupo
                </h6>
            </div>
            <div class="card-body">
                <form action="<?= route_to('admin.messages.group') ?>" method="POST">
                    <div class="form-group">
                        <label for="subject">Nome do Grupo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" 
                               placeholder="Ex: Equipe de Marketing" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Participantes <span class="text-danger">*</span></label>
                        <?php if (empty($users)): ?>
                            <p class="text-muted">Nenhum usuário disponível</p>
                        <?php else: ?>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                <?php foreach ($users as $user): ?>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="user<?= $user->id ?>" name="participants[]" value="<?= $user->id ?>">
                                        <label class="custom-control-label d-flex align-items-center" for="user<?= $user->id ?>">
                                            <img src="<?= $user->avatarUrl(24) ?>" class="rounded-circle mr-2" width="24" height="24" alt="Avatar">
                                            <?= esc($user->name) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block" <?= empty($users) ? 'disabled' : '' ?>>
                        <i class="fas fa-plus mr-1"></i> Criar Grupo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
