<?= $this->extend('back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <a href="<?= route_to('admin.profile') ?>" class="text-decoration-none text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>
        </a>
        <i class="fas fa-cog mr-2"></i><?= esc($title) ?>
    </h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="<?= route_to('admin.profile.settings.update') ?>" method="POST">
            
            <!-- Notificações -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell mr-2"></i>Notificações
                    </h6>
                </div>
                <div class="card-body">
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="email_notifications" 
                               name="email_notifications" value="1" 
                               <?= ($settings['email_notifications'] ?? true) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="email_notifications">
                            <strong>Notificações por E-mail</strong>
                            <br><small class="text-muted">Receber notificações importantes por e-mail</small>
                        </label>
                    </div>
                    
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="push_notifications" 
                               name="push_notifications" value="1"
                               <?= ($settings['push_notifications'] ?? true) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="push_notifications">
                            <strong>Notificações Push</strong>
                            <br><small class="text-muted">Receber notificações no navegador</small>
                        </label>
                    </div>
                    
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="appointment_reminders" 
                               name="appointment_reminders" value="1"
                               <?= ($settings['appointment_reminders'] ?? true) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="appointment_reminders">
                            <strong>Lembretes de Agendamentos</strong>
                            <br><small class="text-muted">Receber lembretes sobre agendamentos próximos</small>
                        </label>
                    </div>
                    
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="message_notifications" 
                               name="message_notifications" value="1"
                               <?= ($settings['message_notifications'] ?? true) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="message_notifications">
                            <strong>Notificações de Mensagens</strong>
                            <br><small class="text-muted">Ser notificado quando receber novas mensagens</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Aparência -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-palette mr-2"></i>Aparência
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="theme">Tema</label>
                        <select class="form-control" id="theme" name="theme">
                            <option value="light" <?= ($settings['theme'] ?? 'auto') === 'light' ? 'selected' : '' ?>>
                                ☀️ Claro
                            </option>
                            <option value="dark" <?= ($settings['theme'] ?? 'auto') === 'dark' ? 'selected' : '' ?>>
                                🌙 Escuro
                            </option>
                            <option value="auto" <?= ($settings['theme'] ?? 'auto') === 'auto' ? 'selected' : '' ?>>
                                🔄 Automático (seguir sistema)
                            </option>
                        </select>
                        <small class="form-text text-muted">O tema automático segue as configurações do seu sistema operacional</small>
                    </div>
                    
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="sidebar_collapsed" 
                               name="sidebar_collapsed" value="1"
                               <?= ($settings['sidebar_collapsed'] ?? false) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="sidebar_collapsed">
                            <strong>Menu lateral recolhido</strong>
                            <br><small class="text-muted">Iniciar com o menu lateral minimizado</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Idioma e Região -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-globe mr-2"></i>Idioma e Região
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label for="language">Idioma</label>
                        <select class="form-control" id="language" name="language">
                            <option value="pt-BR" <?= ($settings['language'] ?? 'pt-BR') === 'pt-BR' ? 'selected' : '' ?>>
                                🇧🇷 Português (Brasil)
                            </option>
                            <option value="en" <?= ($settings['language'] ?? 'pt-BR') === 'en' ? 'selected' : '' ?>>
                                🇺🇸 English
                            </option>
                            <option value="es" <?= ($settings['language'] ?? 'pt-BR') === 'es' ? 'selected' : '' ?>>
                                🇪🇸 Español
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="text-right">
                <a href="<?= route_to('admin.profile') ?>" class="btn btn-secondary mr-2">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Salvar Configurações
                </button>
            </div>
            
        </form>
    </div>
    
    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-2"></i>Sobre as Configurações
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">
                    Personalize sua experiência no sistema ajustando as configurações de acordo com suas preferências.
                </p>
                
                <h6 class="font-weight-bold">Notificações</h6>
                <p class="small text-muted mb-3">
                    Controle como e quando você deseja receber notificações sobre atividades importantes do sistema.
                </p>
                
                <h6 class="font-weight-bold">Aparência</h6>
                <p class="small text-muted mb-3">
                    Escolha o tema visual que mais lhe agrada e configure a visualização inicial do menu.
                </p>
                
                <h6 class="font-weight-bold">Idioma</h6>
                <p class="small text-muted mb-0">
                    Selecione o idioma em que deseja visualizar o sistema.
                </p>
            </div>
        </div>
        
        <div class="card shadow mb-4 border-left-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-lightbulb fa-2x text-info mr-3"></i>
                    <div>
                        <h6 class="mb-0">Dica</h6>
                        <p class="small text-muted mb-0">
                            As alterações serão aplicadas imediatamente após salvar.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Sincroniza o select de tema com o localStorage e aplica preview
    $('#theme').on('change', function() {
        var theme = $(this).val();
        // Aplica preview do tema imediatamente
        if (typeof applyTheme === 'function') {
            applyTheme(theme);
        }
    });
    
    // Sincroniza o valor inicial do select com localStorage
    var savedTheme = localStorage.getItem('theme') || 'auto';
    $('#theme').val(savedTheme);
});
</script>
<?= $this->endSection() ?>
