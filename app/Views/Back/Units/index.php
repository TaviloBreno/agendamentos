<?php
/**
 * View: Back/Units/index.php
 * 
 * Listagem de Unidades com DataTables
 * 
 * VARIÁVEIS DO CONTROLLER:
 * - $title (string): Título da página
 * - $pageHeading (string): Título exibido na página  
 * - $unitsTable (string): HTML da tabela gerado pela Table Class
 * 
 * ASSETS PAGE-LEVEL:
 * Os CSS e JS do DataTables são carregados apenas nesta view,
 * usando renderSection('css') e renderSection('js') do layout.
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('css') ?>
<!-- ============================================================
     CSS PAGE-LEVEL PLUGINS - DataTables
     Estes arquivos só são carregados nesta view específica.
     Verifique em "View Source" que não aparecem na home.
     ============================================================ -->
<link href="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?= isset($pageHeading) ? esc($pageHeading) : 'Unidades' ?>
    </h1>
    <a href="<?= route_to('super.units.new') ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Nova Unidade
    </a>
</div>

<!-- Mensagens de Feedback -->
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
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-building mr-2"></i>Lista de Unidades
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <!-- 
                TABELA GERADA PELA TABLE CLASS
                ==============================
                A variável $unitsTable contém o HTML completo da tabela,
                gerado no controller com CodeIgniter\View\Table.
                
                IMPORTANTE: O id="dataTable" é definido no template da Table Class.
                Se alterar o id, o DataTables não será aplicado (tabela "crua").
                Use Ctrl+F5 para hard refresh se necessário.
            -->
            <?= $unitsTable ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- ============================================================
     JS PAGE-LEVEL PLUGINS - DataTables
     Estes arquivos só são carregados nesta view específica.
     O datatables-demo.js inicializa o DataTables no #dataTable.
     ============================================================ -->
<script src="<?= base_url('back/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('back/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('back/js/demo/datatables-demo.js') ?>"></script>
<?= $this->endSection() ?>
