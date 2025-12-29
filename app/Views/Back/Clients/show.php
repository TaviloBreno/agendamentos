<?= $this->extend('Back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user text-primary mr-2"></i>Detalhes do Cliente
    </h1>
    <div>
        <a href="<?= route_to('super.clients.edit', $client->id) ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-edit fa-sm mr-1"></i> Editar
        </a>
        <a href="<?= route_to('super.clients') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar
        </a>
    </div>
</div>

<!-- Alertas -->
<?= view('Back/layout/partials/alerts') ?>

<div class="row">
    <!-- Coluna Principal -->
    <div class="col-lg-8">
        <!-- Dados Pessoais -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user mr-1"></i>Dados Pessoais
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="35%">Nome:</th>
                                <td><?= esc($client->name) ?></td>
                            </tr>
                            <tr>
                                <th>E-mail:</th>
                                <td>
                                    <a href="mailto:<?= esc($client->email) ?>">
                                        <i class="fas fa-envelope mr-1"></i><?= esc($client->email) ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Telefone:</th>
                                <td>
                                    <?php if ($client->phone): ?>
                                        <a href="tel:<?= esc($client->phone) ?>">
                                            <i class="fas fa-phone mr-1"></i><?= esc($client->phoneFormatted()) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Não informado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>CPF:</th>
                                <td>
                                    <?php if ($client->cpf): ?>
                                        <i class="fas fa-id-card mr-1"></i><?= esc($client->cpfFormatted()) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Não informado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="40%">Data de Nascimento:</th>
                                <td>
                                    <?php if ($client->birth_date): ?>
                                        <i class="fas fa-birthday-cake mr-1"></i>
                                        <?= esc($client->birthDateFormatted()) ?>
                                        <?php if ($client->age()): ?>
                                            <span class="text-muted">(<?= $client->age() ?> anos)</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Não informado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Gênero:</th>
                                <td>
                                    <?php if ($client->gender): ?>
                                        <?= esc($client->genderLabel()) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Não informado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td><?= $client->statusBadge() ?></td>
                            </tr>
                            <tr>
                                <th>Cadastro:</th>
                                <td>
                                    <i class="fas fa-clock mr-1"></i>
                                    <?= $client->created_at->format('d/m/Y H:i') ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Endereço -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-map-marker-alt mr-1"></i>Endereço
                </h6>
            </div>
            <div class="card-body">
                <?php if ($client->hasAddress()): ?>
                    <p class="mb-0">
                        <i class="fas fa-home mr-2 text-muted"></i>
                        <?php if ($client->address): ?>
                            <?= esc($client->address) ?><br>
                        <?php endif; ?>
                        <?php if ($client->city || $client->state): ?>
                            <span class="ml-4">
                                <?= esc($client->city) ?>
                                <?php if ($client->state): ?>
                                    / <?= esc($client->state) ?>
                                    <?php if ($client->stateName()): ?>
                                        (<?= esc($client->stateName()) ?>)
                                    <?php endif; ?>
                                <?php endif; ?>
                            </span><br>
                        <?php endif; ?>
                        <?php if ($client->zip_code): ?>
                            <span class="ml-4">CEP: <?= esc($client->zipCodeFormatted()) ?></span>
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle mr-1"></i>Endereço não cadastrado.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Observações -->
        <?php if ($client->notes): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-sticky-note mr-1"></i>Observações
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-0"><?= nl2br(esc($client->notes)) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Histórico de Agendamentos -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-alt mr-1"></i>Histórico de Agendamentos
                </h6>
                <span class="badge badge-primary"><?= $client->appointmentsCount() ?> agendamento(s)</span>
            </div>
            <div class="card-body">
                <?php if (!empty($recentAppointments)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Horário</th>
                                    <th>Serviço</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentAppointments as $appointment): ?>
                                <tr>
                                    <td><?= esc($appointment->dateFormatted()) ?></td>
                                    <td><?= esc($appointment->timeRange()) ?></td>
                                    <td>
                                        <?php 
                                        $service = $appointment->getService();
                                        echo $service ? esc($service->name) : '-';
                                        ?>
                                    </td>
                                    <td><?= $appointment->statusBadge() ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle mr-1"></i>Nenhum agendamento encontrado.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Coluna Lateral -->
    <div class="col-lg-4">
        <!-- Card Avatar -->
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <img src="<?= $client->avatarUrl(150) ?>" alt="<?= esc($client->name) ?>" 
                     class="rounded-circle mb-3" width="150" height="150">
                <h5 class="font-weight-bold"><?= esc($client->name) ?></h5>
                <p class="text-muted mb-2"><?= esc($client->email) ?></p>
                <?= $client->statusBadge() ?>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt mr-1"></i>Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= route_to('super.appointments.new') ?>?client_id=<?= $client->id ?>" 
                       class="btn btn-success btn-block mb-2">
                        <i class="fas fa-calendar-plus mr-1"></i> Novo Agendamento
                    </a>
                    <a href="<?= route_to('super.clients.edit', $client->id) ?>" 
                       class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-edit mr-1"></i> Editar Cliente
                    </a>
                    <form action="<?= route_to('super.clients.action', $client->id) ?>" method="POST" class="mb-2">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        <button type="submit" class="btn btn-<?= $client->isActive() ? 'warning' : 'success' ?> btn-block">
                            <i class="fas fa-<?= $client->isActive() ? 'ban' : 'check' ?> mr-1"></i>
                            <?= $client->isActive() ? 'Desativar' : 'Ativar' ?> Cliente
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contato -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-address-book mr-1"></i>Contato
                </h6>
            </div>
            <div class="card-body">
                <a href="mailto:<?= esc($client->email) ?>" class="btn btn-outline-primary btn-block mb-2">
                    <i class="fas fa-envelope mr-1"></i> Enviar E-mail
                </a>
                <?php if ($client->phone): ?>
                <a href="https://wa.me/55<?= preg_replace('/[^0-9]/', '', $client->phone) ?>" 
                   target="_blank" class="btn btn-outline-success btn-block mb-2">
                    <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                </a>
                <a href="tel:<?= esc($client->phone) ?>" class="btn btn-outline-info btn-block">
                    <i class="fas fa-phone mr-1"></i> Ligar
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
