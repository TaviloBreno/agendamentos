<?php
/**
 * View: Back/Home/index.php
 * 
 * ACESSO ÀS VARIÁVEIS DO CONTROLLER:
 * ==================================
 * Cada índice do array $data enviado pelo controller se torna
 * uma variável disponível diretamente nesta view:
 * 
 * No Controller:                    Na View:
 * $data['title']            →       $title
 * $data['pageHeading']      →       $pageHeading
 * $data['userName']         →       $userName
 * $data['totalAgendamentos']→       $totalAgendamentos
 * 
 * BOA PRÁTICA - TRATAMENTO DE VARIÁVEIS OPCIONAIS:
 * ================================================
 * Sempre use isset() ou operador ternário para variáveis que
 * podem não existir, evitando "Undefined variable" exceptions:
 * 
 * <?= isset($variavel) ? $variavel : 'valor_padrao' ?>
 * <?= $variavel ?? 'valor_padrao' ?>  (PHP 7+, null coalescing)
 * <?= isset($variavel) ? esc($variavel) : 'valor_padrao' ?>  (com escape XSS)
 * 
 * DEBUG TOOLBAR - ABA "VARS":
 * ===========================
 * Para depurar os dados recebidos:
 * 1. Certifique-se que CI_ENVIRONMENT = 'development' no .env
 * 2. Acesse a página e clique no ícone do CI4 (canto inferior direito)
 * 3. Navegue até a aba "Vars"
 * 4. Expanda "View Data" para ver todas as variáveis disponíveis
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('css') ?>
<!-- CSS específico desta página -->
<style>
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .welcome-card {
        transition: transform 0.3s ease;
    }
    .welcome-card:hover {
        transform: translateY(-5px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<!-- 
    Demonstração de acesso à variável com tratamento seguro:
    Se $pageHeading não existir, usa 'Dashboard' como fallback
-->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?= isset($pageHeading) ? esc($pageHeading) : 'Dashboard' ?>
    </h1>
    <?php if (canAccess('reports')): ?>
    <a href="<?= route_to('super.reports') ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-chart-bar fa-sm text-white-50"></i> Gerar Relatório
    </a>
    <?php endif; ?>
</div>

<!-- Cards de Estatísticas -->
<div class="row">

    <!-- Card - Total de Agendamentos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total de Agendamentos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <!-- Acesso direto à variável com fallback -->
                            <?= isset($totalAgendamentos) ? number_format($totalAgendamentos) : '0' ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card - Agendamentos Hoje -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Agendamentos Hoje
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= isset($agendamentosHoje) ? $agendamentosHoje : '0' ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card - Clientes Ativos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Clientes Ativos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= isset($clientesAtivos) ? $clientesAtivos : '0' ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card - Versão do Sistema -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Versão do Sistema
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <!-- Usando null coalescing operator (PHP 7+) -->
                            <?= $systemVersion ?? '1.0.0' ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cog fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Conteúdo Principal -->
<div class="row">
    
    <!-- Card de Boas-vindas -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow welcome-card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-home mr-2"></i>Bem-vindo ao Sistema
                </h6>
            </div>
            <div class="card-body">
                <!-- Demonstração de acesso à variável $userName -->
                <p class="mb-2">
                    <strong>Olá, <?= isset($userName) ? esc($userName) : 'Usuário' ?>!</strong>
                </p>
                <p>Este é o painel administrativo do sistema de agendamentos.</p>
                <p class="mb-0">Utilize o menu lateral para navegar entre as funcionalidades.</p>
            </div>
        </div>
    </div>

    <!-- Card - Últimos Agendamentos (demonstração de array) -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow welcome-card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-calendar-alt mr-2"></i>Últimos Agendamentos
                </h6>
            </div>
            <div class="card-body">
                <!-- Demonstração de iteração sobre array vindo do controller -->
                <?php if (isset($ultimosAgendamentos) && is_array($ultimosAgendamentos) && count($ultimosAgendamentos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimosAgendamentos as $agendamento): ?>
                                    <tr>
                                        <td><?= esc($agendamento['cliente']) ?></td>
                                        <td><?= esc($agendamento['data']) ?></td>
                                        <td>
                                            <span class="badge badge-primary">
                                                <?= esc($agendamento['hora']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        Nenhum agendamento encontrado.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- JavaScript específico desta página -->
<script>
    $(document).ready(function() {
        // Animação suave nos cards ao carregar
        $('.stat-card').each(function(index) {
            $(this).delay(100 * index).animate({ opacity: 1 }, 300);
        });
    });
</script>
<?= $this->endSection() ?>