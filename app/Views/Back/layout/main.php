<!DOCTYPE html>
<html lang="pt-BR" data-theme="auto">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= isset($description) ? esc($description) : 'Sistema de Agendamentos' ?>">
    <meta name="author" content="<?= isset($author) ? esc($author) : '' ?>">
    <meta name="color-scheme" content="light dark">

    <!-- Título dinâmico com fallback seguro -->
    <title><?= isset($title) ? esc($title) : 'Admin | Sistema' ?></title>

    <!-- Prevenir flash de tema incorreto -->
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || 'auto';
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (theme === 'dark' || (theme === 'auto' && prefersDark)) {
                document.documentElement.classList.add('dark-theme');
            }
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url('back/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url('back/css/sb-admin-2.min.css') ?>" rel="stylesheet">
    
    <!-- Dark Theme -->
    <link href="<?= base_url('back/css/dark-theme.css') ?>" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    
    <!-- Select2 Custom Styles for Bootstrap 4 -->
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            min-height: 38px;
            padding: 0.375rem 0.75rem;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #4e73df;
            border: none;
            color: #fff;
            padding: 2px 8px;
            margin: 2px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            margin-right: 5px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #fff;
            background-color: transparent;
        }
    </style>

    <!-- Seção para CSS específico por view -->
    <?= $this->renderSection('css') ?>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= route_to('super.home') ?>">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Agendamentos</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item <?= url_is('super') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('super.home') ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Cadastros
            </div>

            <!-- Nav Item - Unidades -->
            <li class="nav-item <?= url_is('super/units*') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('super.units') ?>">
                    <i class="fas fa-fw fa-building"></i>
                    <span>Unidades</span>
                </a>
            </li>

            <!-- Nav Item - Serviços -->
            <li class="nav-item <?= url_is('super/services*') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('super.services') ?>">
                    <i class="fas fa-fw fa-concierge-bell"></i>
                    <span>Serviços</span>
                </a>
            </li>

            <!-- Nav Item - Profissionais -->
            <li class="nav-item <?= url_is('super/professionals*') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('super.professionals') ?>">
                    <i class="fas fa-fw fa-user-tie"></i>
                    <span>Profissionais</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Agendamentos
            </div>

            <!-- Nav Item - Agenda -->
            <li class="nav-item <?= url_is('super/appointments*') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('super.appointments') ?>">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Agenda</span>
                </a>
            </li>

            <!-- Nav Item - Clientes -->
            <li class="nav-item <?= url_is('super/clients*') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('super.clients') ?>">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Comunicação
            </div>

            <!-- Nav Item - Mensagens -->
            <li class="nav-item <?= url_is('admin/messages*') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('admin.messages') ?>">
                    <i class="fas fa-fw fa-envelope"></i>
                    <span>Mensagens</span>
                </a>
            </li>

            <!-- Nav Item - Notificações -->
            <li class="nav-item <?= url_is('admin/notifications*') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('admin.notifications') ?>">
                    <i class="fas fa-fw fa-bell"></i>
                    <span>Notificações</span>
                </a>
            </li>

            <!-- Nav Item - WhatsApp -->
            <li class="nav-item <?= url_is('super/whatsapp*') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('super.whatsapp') ?>">
                    <i class="fab fa-fw fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            </li>

            <!-- Nav Item - Relatórios -->
            <li class="nav-item <?= url_is('super/reports*') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('super.reports') ?>">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Relatórios</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Sistema
            </div>

            <!-- Nav Item - Usuários -->
            <li class="nav-item <?= url_is('super/users*') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route_to('super.users') ?>">
                    <i class="fas fa-fw fa-user-cog"></i>
                    <span>Usuários</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Pesquisar..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Pesquisar..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1" id="notificationsDropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter notification-count" style="display: none;">0</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                    Notificações
                                    <a href="#" class="text-white small mark-all-read" style="display: none;">Marcar todas como lidas</a>
                                </h6>
                                <div class="notifications-container">
                                    <div class="dropdown-item text-center small text-gray-500">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Carregando...
                                    </div>
                                </div>
                                <a class="dropdown-item text-center small text-gray-500" href="<?= route_to('admin.notifications') ?>">Ver Todas as Notificações</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1" id="messagesDropdownContainer">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter messages-count" style="display: none;">0</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Mensagens
                                </h6>
                                <div class="messages-container">
                                    <div class="dropdown-item text-center small text-gray-500">
                                        Nenhuma mensagem nova
                                    </div>
                                </div>
                                <a class="dropdown-item text-center small text-gray-500" href="<?= route_to('admin.messages') ?>">Ver Todas as Mensagens</a>
                            </div>
                        </li>

                        <!-- Nav Item - Theme Toggle -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="themeDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Alterar Tema">
                                <i class="fas fa-sun fa-fw theme-icon-light"></i>
                                <i class="fas fa-moon fa-fw theme-icon-dark" style="display: none;"></i>
                            </a>
                            <!-- Dropdown - Theme -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in dropdown-theme" aria-labelledby="themeDropdown">
                                <h6 class="dropdown-header">Tema</h6>
                                <a class="dropdown-item theme-option d-flex align-items-center" href="#" data-theme="light">
                                    <i class="fas fa-sun fa-sm fa-fw mr-2 text-warning"></i>
                                    <span>Claro</span>
                                    <i class="fas fa-check ml-auto text-success theme-check"></i>
                                </a>
                                <a class="dropdown-item theme-option d-flex align-items-center" href="#" data-theme="dark">
                                    <i class="fas fa-moon fa-sm fa-fw mr-2 text-primary"></i>
                                    <span>Escuro</span>
                                    <i class="fas fa-check ml-auto text-success theme-check"></i>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item theme-option d-flex align-items-center" href="#" data-theme="auto">
                                    <i class="fas fa-adjust fa-sm fa-fw mr-2 text-secondary"></i>
                                    <span>Automático</span>
                                    <i class="fas fa-check ml-auto text-success theme-check"></i>
                                </a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= esc(session('userName') ?? 'Usuário') ?></span>
                                <?php 
                                $currentUser = session('user') ?? null;
                                $avatarUrl = $currentUser ? $currentUser->avatarUrl(40) : base_url('back/img/undraw_profile.svg');
                                ?>
                                <img class="img-profile rounded-circle" src="<?= $avatarUrl ?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <span class="dropdown-item text-gray-500">
                                    <i class="fas fa-id-badge fa-sm fa-fw mr-2 text-gray-400"></i>
                                    <?= esc(session('userRole') ?? 'user') ?>
                                </span>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= route_to('admin.profile') ?>">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <a class="dropdown-item" href="<?= route_to('admin.profile.settings') ?>">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Configurações
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Sair
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Flash Messages (success, danger, warning, info, errorsValidation) -->
                    <?= $this->include('Back/Layout/_messages') ?>

                    <!-- Seção para conteúdo principal da página -->
                    <?= $this->renderSection('content') ?>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; <?= isset($footerText) ? esc($footerText) : 'Seu Sistema ' . date('Y') ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Deseja realmente sair?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Selecione "Sair" abaixo se você deseja encerrar sua sessão atual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="<?= base_url('logout') ?>">Sair</a>
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

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>
    
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <!-- Initialize Select2 and Masks -->
    <script>
        $(document).ready(function() {
            // Initialize Select2 for service multi-select
            $('.select2-services').select2({
                theme: 'bootstrap-5',
                language: 'pt-BR',
                placeholder: $(this).data('placeholder') || 'Selecione...',
                allowClear: true,
                width: '100%'
            });
            
            // Phone mask - (00) 00000-0000
            var phoneMaskBehavior = function(val) {
                return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
            };
            var phoneOptions = {
                onKeyPress: function(val, e, field, options) {
                    field.mask(phoneMaskBehavior.apply({}, arguments), options);
                }
            };
            $('input[name="phone"], .phone-mask').mask(phoneMaskBehavior, phoneOptions);
            
            // CPF mask - 000.000.000-00
            $('input[name="cpf"], .cpf-mask').mask('000.000.000-00', {reverse: true});
            
            // CNPJ mask - 00.000.000/0000-00
            $('input[name="cnpj"], .cnpj-mask').mask('00.000.000/0000-00', {reverse: true});
            
            // CEP mask - 00000-000
            $('input[name="zip_code"], .cep-mask').mask('00000-000');
            
            // Date mask - 00/00/0000
            $('.date-mask').mask('00/00/0000');
            
            // Time mask - 00:00
            $('.time-mask').mask('00:00');
            
            // Money mask - R$ 0.000,00
            $('.money-mask').mask('#.##0,00', {reverse: true});

            // =========================================================================
            // NOTIFICAÇÕES DROPDOWN
            // =========================================================================
            function loadNotifications() {
                $.get('<?= route_to('admin.notifications.dropdown') ?>', function(response) {
                    if (response.success) {
                        updateNotificationCounter(response.unreadCount);
                        renderNotifications(response.notifications);
                    }
                });
            }

            function updateNotificationCounter(count) {
                var $counter = $('.notification-count');
                var $markAllBtn = $('.mark-all-read');
                
                if (count > 0) {
                    $counter.text(count > 9 ? '9+' : count).show();
                    $markAllBtn.show();
                } else {
                    $counter.hide();
                    $markAllBtn.hide();
                }
            }

            function renderNotifications(notifications) {
                var $container = $('.notifications-container');
                $container.empty();

                if (notifications.length === 0) {
                    $container.html('<div class="dropdown-item text-center small text-gray-500">Nenhuma notificação</div>');
                    return;
                }

                notifications.forEach(function(n) {
                    var html = '<a class="dropdown-item d-flex align-items-center notification-item" href="#" data-id="' + n.id + '" data-link="' + (n.link || '') + '">' +
                        '<div class="mr-3">' + n.iconHtml + '</div>' +
                        '<div>' +
                        '<div class="small text-gray-500">' + n.timeAgo + '</div>' +
                        '<span class="font-weight-bold">' + n.title + '</span>' +
                        (n.message ? '<div class="small text-gray-600">' + n.message + '</div>' : '') +
                        '</div>' +
                        '</a>';
                    $container.append(html);
                });
            }

            // Carregar notificações ao clicar no dropdown
            $('#alertsDropdown').on('click', function() {
                loadNotifications();
            });

            // Marcar como lida ao clicar
            $(document).on('click', '.notification-item', function(e) {
                e.preventDefault();
                var $item = $(this);
                var id = $item.data('id');
                var link = $item.data('link');

                $.post('<?= base_url('admin/notifications/') ?>' + id + '/read', function(response) {
                    if (response.success) {
                        updateNotificationCounter(response.unreadCount);
                        $item.removeClass('font-weight-bold');
                        
                        if (link) {
                            window.location.href = link;
                        }
                    }
                });
            });

            // Marcar todas como lidas
            $('.mark-all-read').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                $.post('<?= route_to('admin.notifications.read.all') ?>', function(response) {
                    if (response.success) {
                        updateNotificationCounter(0);
                        loadNotifications();
                    }
                });
            });

            // Carregar contador inicial
            loadNotifications();

            // Atualizar a cada 60 segundos
            setInterval(loadNotifications, 60000);

            // =========================================================
            // TEMA ESCURO / CLARO / AUTOMÁTICO
            // =========================================================
            
            // Função para verificar se está no modo escuro
            function isDarkMode() {
                return document.documentElement.classList.contains('dark-theme');
            }
            
            // Função para atualizar ícone do botão de tema
            function updateThemeIcon() {
                var dark = isDarkMode();
                $('.theme-icon-light').toggle(!dark);
                $('.theme-icon-dark').toggle(dark);
            }
            
            // Função para aplicar o tema
            function applyTheme(theme) {
                var html = document.documentElement;
                
                // Adiciona classe de transição suave
                html.classList.add('theme-transition');
                
                if (theme === 'auto') {
                    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (prefersDark) {
                        html.classList.add('dark-theme');
                    } else {
                        html.classList.remove('dark-theme');
                    }
                } else if (theme === 'dark') {
                    html.classList.add('dark-theme');
                } else {
                    html.classList.remove('dark-theme');
                }
                
                html.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                
                // Remove classe de transição após animação
                setTimeout(function() {
                    html.classList.remove('theme-transition');
                }, 300);
                
                // Atualiza ícones
                updateThemeDropdown(theme);
                updateThemeIcon();
            }
            
            // Função para atualizar o dropdown do tema
            function updateThemeDropdown(theme) {
                $('.theme-option .theme-check').hide();
                $('.theme-option[data-theme="' + theme + '"] .theme-check').show();
            }
            
            // Handler para mudança de tema
            $('.theme-option').on('click', function(e) {
                e.preventDefault();
                var theme = $(this).data('theme');
                applyTheme(theme);
                
                // Salva no servidor se o usuário estiver logado
                <?php if (session()->get('user_id')): ?>
                $.post('<?= base_url('admin/profile/settings') ?>', {
                    theme: theme,
                    _ajax: true,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                });
                <?php endif; ?>
            });
            
            // Escuta mudanças na preferência do sistema
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                var currentTheme = localStorage.getItem('theme') || 'auto';
                if (currentTheme === 'auto') {
                    applyTheme('auto');
                }
            });
            
            // Inicializa o dropdown e ícone
            var savedTheme = localStorage.getItem('theme') || 'auto';
            updateThemeDropdown(savedTheme);
            updateThemeIcon();
        });
    </script>

    <!-- Seção para scripts JavaScript específicos por view -->
    <?= $this->renderSection('js') ?>

</body>

</html>