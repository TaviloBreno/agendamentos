<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($description) ? esc($description) : 'Sistema de Agendamentos Online' ?>">
    <title><?= isset($title) ? esc($title) . ' | ' : '' ?>Agendamentos</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#3273dc">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Agendamentos">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    
    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('front/img/icons/icon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('front/img/icons/icon-16x16.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('front/img/icons/icon-192x192.png') ?>">
    
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #3273dc;
            --success-color: #48c774;
            --danger-color: #f14668;
            --warning-color: #ffdd57;
            --info-color: #3298dc;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand .navbar-item {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .main-content {
            flex: 1;
            padding: 2rem 0;
        }
        
        .footer {
            background-color: #f5f5f5;
            padding: 2rem 1.5rem;
        }
        
        /* Wizard Steps */
        .wizard-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .wizard-step {
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: relative;
        }
        
        .wizard-step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 2px;
            background-color: #dbdbdb;
        }
        
        .wizard-step.is-active::after,
        .wizard-step.is-completed::after {
            background-color: var(--primary-color);
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #dbdbdb;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .wizard-step.is-active .step-number {
            background-color: var(--primary-color);
            transform: scale(1.1);
        }
        
        .wizard-step.is-completed .step-number {
            background-color: var(--success-color);
        }
        
        .step-title {
            font-weight: 600;
            color: #7a7a7a;
        }
        
        .wizard-step.is-active .step-title,
        .wizard-step.is-completed .step-title {
            color: #363636;
        }
        
        /* Cards */
        .selection-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .selection-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .selection-card.is-selected {
            border-color: var(--primary-color);
            background-color: #f0f7ff;
        }
        
        .selection-card .card-content {
            text-align: center;
        }
        
        .selection-card .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), #667eea);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .selection-card .icon-wrapper i {
            font-size: 2rem;
            color: #fff;
        }
        
        /* Calendar */
        .calendar-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
        }
        
        .calendar-day-name {
            text-align: center;
            font-weight: 700;
            color: #7a7a7a;
            padding: 0.5rem;
            font-size: 0.85rem;
        }
        
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }
        
        .calendar-day:hover:not(.is-disabled):not(.is-empty) {
            background-color: #f0f7ff;
        }
        
        .calendar-day.is-today {
            border: 2px solid var(--primary-color);
        }
        
        .calendar-day.is-selected {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        .calendar-day.is-disabled {
            color: #dbdbdb;
            cursor: not-allowed;
        }
        
        .calendar-day.is-weekend {
            color: #f14668;
        }
        
        .calendar-day.is-empty {
            cursor: default;
        }
        
        /* Time Slots */
        .time-slots {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }
        
        @media (max-width: 768px) {
            .time-slots {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        .time-slot {
            padding: 0.75rem 1rem;
            border: 2px solid #dbdbdb;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }
        
        .time-slot:hover:not(.is-disabled) {
            border-color: var(--primary-color);
            background-color: #f0f7ff;
        }
        
        .time-slot.is-selected {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }
        
        .time-slot.is-disabled {
            background-color: #f5f5f5;
            color: #dbdbdb;
            cursor: not-allowed;
        }
        
        /* Month selector */
        .month-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .month-tab {
            padding: 0.5rem 1rem;
            border: 2px solid #dbdbdb;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }
        
        .month-tab:hover {
            border-color: var(--primary-color);
        }
        
        .month-tab.is-active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }
        
        /* Summary */
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .summary-label {
            color: #7a7a7a;
        }
        
        .summary-value {
            font-weight: 600;
        }
        
        /* Hero section */
        .hero.is-primary {
            background: linear-gradient(135deg, #3273dc 0%, #667eea 100%);
        }
        
        /* Appointment Card */
        .appointment-card {
            transition: all 0.3s ease;
        }
        
        .appointment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .appointment-status {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        /* Loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f0f0f0;
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Utilities */
        .has-text-primary { color: var(--primary-color) !important; }
        .mb-0 { margin-bottom: 0 !important; }
    </style>
    
    <?= $this->renderSection('css') ?>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar is-white" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item has-text-primary" href="<?= base_url('/') ?>">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Agendamentos
                </a>
                
                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>
            
            <div id="navbarMenu" class="navbar-menu">
                <div class="navbar-start">
                    <a class="navbar-item" href="<?= base_url('/') ?>">
                        <i class="fas fa-home mr-1"></i> Início
                    </a>
                    <a class="navbar-item" href="<?= base_url('agendar') ?>">
                        <i class="fas fa-calendar-plus mr-1"></i> Agendar
                    </a>
                    <?php if (session()->get('user')): ?>
                    <a class="navbar-item" href="<?= base_url('meus-agendamentos') ?>">
                        <i class="fas fa-list mr-1"></i> Meus Agendamentos
                    </a>
                    <?php endif; ?>
                </div>
                
                <div class="navbar-end">
                    <?php if (session()->get('user')): ?>
                        <?php $user = session()->get('user'); ?>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <i class="fas fa-user-circle mr-1"></i>
                                <?= esc($user['name']) ?>
                            </a>
                            <div class="navbar-dropdown is-right">
                                <a class="navbar-item" href="<?= base_url('meus-agendamentos') ?>">
                                    <i class="fas fa-calendar-alt mr-2"></i> Meus Agendamentos
                                </a>
                                <?php if (in_array($user['role'] ?? '', ['super', 'admin'])): ?>
                                <a class="navbar-item" href="<?= route_to('super.home') ?>">
                                    <i class="fas fa-cogs mr-2"></i> Painel Admin
                                </a>
                                <hr class="navbar-divider">
                                <?php endif; ?>
                                <a class="navbar-item" href="<?= route_to('auth.logout') ?>">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Sair
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="navbar-item">
                            <div class="buttons">
                                <a class="button is-primary" href="<?= base_url('login') ?>">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Entrar
                                </a>
                                <a class="button is-light" href="<?= base_url('cadastro') ?>">
                                    <i class="fas fa-user-plus mr-1"></i> Cadastrar
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="columns">
                <div class="column is-4">
                    <h4 class="title is-5 has-text-primary">
                        <i class="fas fa-calendar-check mr-1"></i> Agendamentos
                    </h4>
                    <p>Sistema de agendamentos online. Agende seus horários de forma rápida e fácil.</p>
                </div>
                <div class="column is-4">
                    <h4 class="title is-6">Links Úteis</h4>
                    <ul>
                        <li><a href="<?= base_url('/') ?>">Início</a></li>
                        <li><a href="<?= base_url('agendar') ?>">Agendar Horário</a></li>
                        <li><a href="<?= base_url('login') ?>">Login</a></li>
                    </ul>
                </div>
                <div class="column is-4">
                    <h4 class="title is-6">Contato</h4>
                    <p>
                        <i class="fas fa-envelope mr-1"></i> contato@agendamentos.com.br<br>
                        <i class="fas fa-phone mr-1"></i> (11) 99999-9999
                    </p>
                </div>
            </div>
            <hr>
            <div class="has-text-centered">
                <p>&copy; <?= date('Y') ?> Sistema de Agendamentos. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="loading-spinner"></div>
    </div>
    
    <!-- Scripts -->
    <script>
        // Navbar burger toggle
        document.addEventListener('DOMContentLoaded', () => {
            const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
            
            $navbarBurgers.forEach(el => {
                el.addEventListener('click', () => {
                    const target = el.dataset.target;
                    const $target = document.getElementById(target);
                    el.classList.toggle('is-active');
                    $target.classList.toggle('is-active');
                });
            });
        });
        
        // Loading functions
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
        
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }
        
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered:', registration.scope);
                        
                        // Check for updates
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // New version available
                                    if (confirm('Nova versão disponível! Deseja atualizar?')) {
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    })
                    .catch(err => console.log('SW registration failed:', err));
            });
        }
        
        // PWA Install Prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install button if exists
            const installBtn = document.getElementById('pwa-install-btn');
            if (installBtn) {
                installBtn.style.display = 'inline-flex';
                installBtn.addEventListener('click', async () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        console.log('Install prompt outcome:', outcome);
                        deferredPrompt = null;
                        installBtn.style.display = 'none';
                    }
                });
            }
        });
        
        // Handle app installed
        window.addEventListener('appinstalled', () => {
            console.log('PWA installed successfully');
            deferredPrompt = null;
        });
    </script>
    
    <?= $this->renderSection('js') ?>
</body>
</html>
