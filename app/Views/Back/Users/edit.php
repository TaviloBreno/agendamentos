<?= $this->extend('Back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-edit text-primary mr-2"></i><?= esc($title) ?>
    </h1>
    <a href="<?= route_to('super.users') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar
    </a>
</div>

<!-- Alertas -->
<?= view('Back/layout/partials/alerts') ?>

<?php 
$currentUser = session()->get('user');
$isSelf = $currentUser && $currentUser['id'] == $user->id;
?>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit mr-1"></i>Editar Dados do Usuário
            <?php if ($isSelf): ?>
                <span class="badge badge-primary ml-2">Seu perfil</span>
            <?php endif; ?>
        </h6>
    </div>
    <div class="card-body">
        <form action="<?= route_to('super.users.update', $user->id) ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= old('name', $user->name) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">E-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= old('email', $user->email) ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="password">Nova Senha</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               minlength="6">
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Deixe em branco para manter a senha atual
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="role">Papel <span class="text-danger">*</span></label>
                        <select class="form-control" id="role" name="role" required 
                                <?= $isSelf ? 'disabled' : '' ?>>
                            <?php foreach ($roles as $value => $label): ?>
                                <option value="<?= $value ?>" <?= old('role', $user->role) === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($isSelf): ?>
                            <input type="hidden" name="role" value="<?= $user->role ?>">
                            <small class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle"></i> Você não pode alterar seu próprio papel
                            </small>
                        <?php else: ?>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Define as permissões do usuário
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="active">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="active" name="active" required
                                <?= $isSelf ? 'disabled' : '' ?>>
                            <option value="1" <?= old('active', $user->active) == 1 ? 'selected' : '' ?>>Ativo</option>
                            <option value="0" <?= old('active', $user->active) == 0 ? 'selected' : '' ?>>Inativo</option>
                        </select>
                        <?php if ($isSelf): ?>
                            <input type="hidden" name="active" value="<?= $user->active ?>">
                            <small class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle"></i> Você não pode desativar sua própria conta
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Informação sobre papéis -->
            <div class="alert alert-info">
                <h6 class="font-weight-bold"><i class="fas fa-info-circle mr-1"></i>Sobre os Papéis</h6>
                <ul class="mb-0">
                    <li><strong>Super Admin:</strong> Acesso total ao sistema, pode gerenciar todos os recursos.</li>
                    <li><strong>Administrador:</strong> Acesso à área administrativa, pode gerenciar a maioria dos recursos.</li>
                    <li><strong>Usuário:</strong> Acesso básico, pode visualizar e gerenciar apenas seus próprios dados.</li>
                </ul>
            </div>

            <hr>

            <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Salvar Alterações
                </button>
                <a href="<?= route_to('super.users') ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
