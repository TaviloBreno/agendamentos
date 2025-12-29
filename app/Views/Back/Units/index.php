<?php
/**
 * View: Back/Units/index.php
 * 
 * Listagem de Unidades
 * 
 * VARIÁVEIS DISPONÍVEIS (vindas do Controller):
 * - $title (string): Título da página
 * - $pageHeading (string): Título exibido na página  
 * - $units (array<Unit>): Array de objetos Unit
 * 
 * ACESSO AOS DADOS DA ENTITY:
 * Como $returnType = Unit::class no Model, cada item em $units
 * é um objeto App\Entities\Unit com:
 * - Propriedades: $unit->name, $unit->email, $unit->phone
 * - Métodos: $unit->startHour(), $unit->isActive(), $unit->lucio()
 */
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('css') ?>
<!-- CSS específico desta página -->
<style>
    .unit-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .unit-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
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
        <i class="fas fa-check-circle mr-2"></i>
        <?= session('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <?= session('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Card de Listagem -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-building mr-2"></i>Lista de Unidades
        </h6>
        <span class="badge badge-primary">
            <?= isset($units) ? count($units) : 0 ?> registro(s)
        </span>
    </div>
    <div class="card-body">
        
        <?php if (isset($units) && count($units) > 0): ?>
            
            <!-- 
                FOREACH COM ENTITY
                ==================
                Cada $unit é um objeto App\Entities\Unit, não um array!
                Podemos acessar propriedades ($unit->name) e métodos ($unit->lucio())
            -->
            
            <!-- Listagem simples para teste -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Coordenador</th>
                            <th>Horário</th>
                            <th>Status</th>
                            <th>Método Entity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <!-- Acessando propriedades da Entity -->
                                <td><?= esc($unit->id) ?></td>
                                <td><?= esc($unit->name) ?></td>
                                <td><?= esc($unit->email) ?></td>
                                <td><?= esc($unit->phone) ?></td>
                                <td><?= esc($unit->coordinator) ?></td>
                                
                                <!-- Usando método da Entity: getWorkingHours() -->
                                <td><?= esc($unit->getWorkingHours()) ?></td>
                                
                                <!-- Usando método da Entity: getStatusBadge() -->
                                <td><?= $unit->getStatusBadge() ?></td>
                                
                                <!-- 
                                    PROVA DE CONCEITO: Método customizado da Entity
                                    Este método só funciona porque $returnType = Unit::class
                                    Se trocar para 'array' ou 'object', dará erro!
                                -->
                                <td>
                                    <small class="text-muted">
                                        <?= esc($unit->lucio()) ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Informação adicional sobre Entity -->
            <div class="alert alert-info mt-3 mb-0">
                <h6 class="alert-heading">
                    <i class="fas fa-info-circle mr-2"></i>Prova de Conceito: Entity
                </h6>
                <p class="mb-1">
                    A coluna "Método Entity" demonstra o uso do método <code>$unit->lucio()</code> 
                    definido em <code>App\Entities\Unit</code>.
                </p>
                <p class="mb-0">
                    <strong>Experimente:</strong> No <code>UnitModel</code>, altere 
                    <code>$returnType</code> para <code>'array'</code> ou <code>'object'</code> 
                    e veja que o método deixará de funcionar (erro "Call to undefined method").
                </p>
            </div>

        <?php else: ?>
            
            <!-- Estado vazio -->
            <div class="text-center py-5">
                <i class="fas fa-building fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Nenhuma unidade cadastrada</h5>
                <p class="text-muted mb-3">
                    Comece cadastrando a primeira unidade do sistema.
                </p>
                <a href="<?= route_to('super.units.new') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Cadastrar Unidade
                </a>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

<!-- Card de Debug (apenas para desenvolvimento) -->
<?php if (ENVIRONMENT === 'development'): ?>
<div class="card shadow mb-4 border-left-warning">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-bug mr-2"></i>Debug Info (apenas em development)
        </h6>
    </div>
    <div class="card-body">
        <p class="mb-2"><strong>Tipo de retorno:</strong></p>
        <pre class="bg-light p-2 rounded"><?php 
            if (isset($units) && count($units) > 0) {
                echo 'Tipo do primeiro item: ' . get_class($units[0]) . "\n";
                echo 'É uma Entity Unit? ' . ($units[0] instanceof \App\Entities\Unit ? 'SIM ✓' : 'NÃO ✗');
            } else {
                echo 'Nenhum registro para analisar';
            }
        ?></pre>
        
        <p class="mb-2 mt-3"><strong>Para testar diferentes returnTypes:</strong></p>
        <ol class="mb-0">
            <li>Abra <code>app/Models/UnitModel.php</code></li>
            <li>Altere <code>protected $returnType</code> para:
                <ul>
                    <li><code>Unit::class</code> → Retorna objetos Entity (atual)</li>
                    <li><code>'array'</code> → Retorna arrays associativos</li>
                    <li><code>'object'</code> → Retorna objetos stdClass</li>
                </ul>
            </li>
            <li>Recarregue esta página e observe as mudanças</li>
        </ol>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- JavaScript específico desta página -->
<script>
    $(document).ready(function() {
        console.log('View Units/index carregada');
        console.log('Total de unidades: <?= isset($units) ? count($units) : 0 ?>');
    });
</script>
<?= $this->endSection() ?>
