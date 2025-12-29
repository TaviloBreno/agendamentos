<?= $this->extend('back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user mr-2"></i><?= esc($title) ?>
    </h1>
</div>

<div class="row">
    <!-- Perfil Card -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <!-- Avatar -->
                <div class="position-relative d-inline-block mb-3">
                    <img src="<?= $user->avatarUrl(150) ?>" class="rounded-circle" width="150" height="150" alt="Avatar" id="avatarPreview">
                    <button type="button" class="btn btn-primary btn-circle position-absolute" 
                            style="bottom: 5px; right: 5px;" onclick="document.getElementById('avatarInput').click()">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                
                <!-- Upload Avatar Form -->
                <form action="<?= route_to('admin.profile.avatar') ?>" method="POST" enctype="multipart/form-data" id="avatarForm">
                    <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*">
                </form>
                
                <?php if ($user->hasCustomAvatar()): ?>
                    <form action="<?= route_to('admin.profile.avatar.remove') ?>" method="POST" class="mb-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover avatar?')">
                            <i class="fas fa-trash mr-1"></i> Remover Avatar
                        </button>
                    </form>
                <?php endif; ?>
                
                <h4 class="mb-1"><?= esc($user->name) ?></h4>
                <p class="text-muted mb-2"><?= esc($user->email) ?></p>
                <?= $user->roleBadge() ?>
                
                <hr>
                
                <div class="text-left">
                    <?php if ($user->phone): ?>
                        <p class="mb-2">
                            <i class="fas fa-phone mr-2 text-primary"></i>
                            <?= esc($user->phone) ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($user->bio): ?>
                        <p class="mb-2">
                            <i class="fas fa-quote-left mr-2 text-primary"></i>
                            <?= esc($user->bio) ?>
                        </p>
                    <?php endif; ?>
                    
                    <p class="mb-2">
                        <i class="fas fa-calendar mr-2 text-primary"></i>
                        Membro desde <?= date('d/m/Y', strtotime($user->created_at)) ?>
                    </p>
                    
                    <?php if ($user->last_login_at): ?>
                        <p class="mb-0">
                            <i class="fas fa-clock mr-2 text-primary"></i>
                            Último acesso: <?= date('d/m/Y H:i', strtotime($user->last_login_at)) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <a href="<?= route_to('admin.profile.settings') ?>" class="btn btn-outline-primary btn-block mb-2">
                    <i class="fas fa-cog mr-2"></i> Configurações
                </a>
                <a href="<?= route_to('admin.notifications') ?>" class="btn btn-outline-info btn-block mb-2">
                    <i class="fas fa-bell mr-2"></i> Notificações
                </a>
                <a href="<?= route_to('admin.messages') ?>" class="btn btn-outline-success btn-block">
                    <i class="fas fa-envelope mr-2"></i> Mensagens
                </a>
            </div>
        </div>
    </div>
    
    <!-- Editar Perfil -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Editar Informações</h6>
            </div>
            <div class="card-body">
                <form action="<?= route_to('admin.profile.update') ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nome <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= esc($user->name) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" id="email" 
                                       value="<?= esc($user->email) ?>" disabled>
                                <small class="form-text text-muted">O e-mail não pode ser alterado</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="text" class="form-control phone-mask" id="phone" name="phone" 
                               value="<?= esc($user->phone ?? '') ?>" placeholder="(00) 00000-0000">
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3" 
                                  placeholder="Conte um pouco sobre você..."><?= esc($user->bio ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Salvar Alterações
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Alterar Senha -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Alterar Senha</h6>
            </div>
            <div class="card-body">
                <form action="<?= route_to('admin.profile.password') ?>" method="POST">
                    <div class="form-group">
                        <label for="current_password">Senha Atual <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_password">Nova Senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                <small class="form-text text-muted">Mínimo 6 caracteres</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password">Confirmar Nova Senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key mr-1"></i> Alterar Senha
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .btn-circle {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    // Auto submit avatar form on file select
    document.getElementById('avatarInput').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            // Preview
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
            
            // Submit
            document.getElementById('avatarForm').submit();
        }
    });
    
    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        var newPassword = document.getElementById('new_password').value;
        if (this.value !== newPassword) {
            this.setCustomValidity('As senhas não conferem');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
<?= $this->endSection() ?>
