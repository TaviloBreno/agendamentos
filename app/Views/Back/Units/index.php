<?php
/**
 * View: Back/Units/index.php
 * 
 * Listagem de Unidades com DataTables
 * 
 * =========================================================================
 * VARIÁVEIS DO CONTROLLER
 * =========================================================================
 * 
 * - $title (string): Título da página (meta tag <title>)
 * - $pageHeading (string): Título exibido na página  
 * - $units (string): HTML da tabela gerado pela UnitService
 * 
 * =========================================================================
 * VIEW LIMPA (PADRÃO SERVICE LAYER)
 * =========================================================================
 * 
 * Esta view NÃO contém:
 * - Loops foreach para montar tabela
 * - Lógica de formatação de dados
 * - Verificações de empty()
 * 
 * Tudo é tratado na UnitService. A view apenas exibe: <?= $units ?>
 * 
 * =========================================================================
 * ASSETS PAGE-LEVEL
 * =========================================================================
 * 
 * Os CSS e JS do DataTables são carregados apenas nesta view,
 * usando renderSection('css') e renderSection('js') do layout.
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('css') ?>
<!-- DataTables CSS - Page Level -->
<link href="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?= esc($pageHeading ?? 'Unidades') ?>
    </h1>
    <a href="<?= route_to('super.units.new') ?>" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Nova Unidade
    </a>
</div>

<!-- Flash Messages -->
<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i><?= session('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i><?= session('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- DataTables Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-building mr-2"></i><?= esc($title ?? 'Unidades') ?>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= $units ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- DataTables JS - Page Level -->
<script src="<?= base_url('back/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('back/js/demo/datatables-demo.js') ?>"></script>
<?= $this->endSection() ?>
