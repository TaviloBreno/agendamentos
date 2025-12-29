<?php
/**
 * View: Home/index.php
 * 
 * Esta view estende o layout principal e define apenas o conteúdo específico.
 * 
 * Fluxo de renderização do CodeIgniter 4:
 * 1. Controller chama view() passando dados (ex: ['title' => 'Dashboard'])
 * 2. A view é processada e encontra $this->extend() 
 * 3. O layout é carregado e as seções são inseridas nos locais de renderSection()
 * 4. O HTML final é enviado ao navegador
 * 
 * Variáveis esperadas do Controller:
 * - $title (string, opcional): Título da página
 * - $pageHeading (string, opcional): Título exibido na página
 * - Outras variáveis conforme necessidade da página
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('css') ?>
<!-- CSS específico desta página -->
<style>
    /* Estilos específicos para a página inicial podem ser adicionados aqui */
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
<h1 class="h3 mb-4 text-gray-800"><?= isset($pageHeading) ? esc($pageHeading) : 'Página Inicial' ?></h1>

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
                <p>Este é o painel administrativo do sistema de agendamentos.</p>
                <p class="mb-0">Utilize o menu lateral para navegar entre as funcionalidades.</p>
            </div>
        </div>
    </div>

    <!-- Card de Informações -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow welcome-card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-info-circle mr-2"></i>Informações
                </h6>
            </div>
            <div class="card-body">
                <p><strong>Data atual:</strong> <?= date('d/m/Y') ?></p>
                <p class="mb-0"><strong>Versão do sistema:</strong> <?= isset($systemVersion) ? esc($systemVersion) : '1.0.0' ?></p>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- JavaScript específico desta página -->
<script>
    // Scripts específicos para a página inicial
    $(document).ready(function() {
        console.log('Página inicial carregada com sucesso!');
        
        // Exemplo de como você pode adicionar funcionalidades específicas
        // que serão carregadas apenas nesta página
    });
</script>
<?= $this->endSection() ?>