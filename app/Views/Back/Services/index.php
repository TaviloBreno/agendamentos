<?php
/**
 * View: Back/Services/index.php
 * 
 * Listagem de Serviços com DataTables
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
        <?= esc($pageHeading ?? 'Serviços') ?>
    </h1>
    <a href="<?= route_to('super.services.new') ?>" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Novo Serviço
    </a>
</div>

<!-- DataTables Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-concierge-bell mr-2"></i><?= esc($title ?? 'Serviços') ?>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= $services ?>
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
