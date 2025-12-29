<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema de Agendamentos - Nova Senha">
    <meta name="author" content="Sistema de Agendamentos">

    <title><?= esc($title ?? 'Nova Senha | Sistema') ?></title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url('back/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url('back/css/sb-admin-2.min.css') ?>" rel="stylesheet">

    <style>
        .bg-reset-image {
            background: url("https://images.unsplash.com/photo-1614064641938-3bbee52942c7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80");
            background-position: center;
            background-size: cover;
        }
        .bg-gradient-primary {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }
        .reset-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        .password-strength.weak { background: #e74a3b; width: 33%; }
        .password-strength.medium { background: #f6c23e; width: 66%; }
        .password-strength.strong { background: #1cc88a; width: 100%; }
    </style>

</head>

<body class="bg-gradient-primary">

    <div class="container reset-container">

        <!-- Outer Row -->
        <div class="row justify-content-center w-100">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-reset-image" style="min-height: 450px;"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">
                                            <i class="fas fa-key text-primary"></i>
                                            Criar Nova Senha
                                        </h1>
                                        <p class="mb-4 text-muted">
                                            Escolha uma senha forte com pelo menos 6 caracteres.
                                        </p>
                                    </div>
                                    
                                    <!-- Mensagens Flash -->
                                    <?php if (session()->has('error')): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            <?= session('error') ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (session()->has('errors')): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <ul class="mb-0 pl-3">
                                                <?php foreach (session('errors') as $error): ?>
                                                    <li><?= esc($error) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Formulário de Nova Senha -->
                                    <form class="user" action="<?= route_to('password.update') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="token" value="<?= esc($token) ?>">
                                        
                                        <div class="form-group">
                                            <input 
                                                type="password" 
                                                class="form-control form-control-user"
                                                id="password" 
                                                name="password"
                                                placeholder="Nova senha..."
                                                minlength="6"
                                                required
                                            >
                                            <div class="password-strength" id="passwordStrength"></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <input 
                                                type="password" 
                                                class="form-control form-control-user"
                                                id="password_confirm" 
                                                name="password_confirm"
                                                placeholder="Confirme a nova senha..."
                                                minlength="6"
                                                required
                                            >
                                            <small id="matchMessage" class="form-text"></small>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            <i class="fas fa-save mr-2"></i>
                                            Salvar Nova Senha
                                        </button>
                                        
                                    </form>
                                    
                                    <hr>
                                    
                                    <div class="text-center">
                                        <a class="small" href="<?= route_to('login') ?>">
                                            <i class="fas fa-arrow-left mr-1"></i>
                                            Voltar ao login
                                        </a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url('back/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('back/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url('back/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url('back/js/sb-admin-2.min.js') ?>"></script>

    <script>
        // Indicador de força da senha
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthBar.className = 'password-strength';
                return;
            }
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            if (strength <= 2) {
                strengthBar.className = 'password-strength weak';
            } else if (strength <= 3) {
                strengthBar.className = 'password-strength medium';
            } else {
                strengthBar.className = 'password-strength strong';
            }
        });
        
        // Verificação de correspondência de senhas
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            const message = document.getElementById('matchMessage');
            
            if (confirm.length === 0) {
                message.textContent = '';
                return;
            }
            
            if (password === confirm) {
                message.textContent = '✓ Senhas coincidem';
                message.className = 'form-text text-success';
            } else {
                message.textContent = '✗ Senhas não coincidem';
                message.className = 'form-text text-danger';
            }
        });
    </script>

</body>

</html>
