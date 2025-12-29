<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema de Agendamentos - Recuperar Senha">
    <meta name="author" content="Sistema de Agendamentos">

    <title><?= esc($title ?? 'Recuperar Senha | Sistema') ?></title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url('back/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url('back/css/sb-admin-2.min.css') ?>" rel="stylesheet">

    <style>
        .bg-forgot-image {
            background: url("https://images.unsplash.com/photo-1526378787940-576a539ba69d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80");
            background-position: center;
            background-size: cover;
        }
        .bg-gradient-primary {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }
        .forgot-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

</head>

<body class="bg-gradient-primary">

    <div class="container forgot-container">

        <!-- Outer Row -->
        <div class="row justify-content-center w-100">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-forgot-image" style="min-height: 400px;"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">
                                            <i class="fas fa-unlock-alt text-primary"></i>
                                            Esqueceu sua senha?
                                        </h1>
                                        <p class="mb-4 text-muted">
                                            Informe seu e-mail e enviaremos um link para redefinir sua senha.
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

                                    <?php if (session()->has('success')): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            <?= session('success') ?>
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

                                    <!-- Formulário de Recuperação -->
                                    <form class="user" action="<?= route_to('password.send') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        
                                        <div class="form-group">
                                            <input 
                                                type="email" 
                                                class="form-control form-control-user"
                                                id="email" 
                                                name="email"
                                                aria-describedby="emailHelp"
                                                placeholder="Digite seu e-mail cadastrado..."
                                                value="<?= old('email') ?>"
                                                autofocus
                                                required
                                            >
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            <i class="fas fa-paper-plane mr-2"></i>
                                            Enviar Link de Recuperação
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

</body>

</html>
