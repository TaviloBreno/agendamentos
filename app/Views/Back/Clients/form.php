<?= $this->extend('Back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-plus text-primary mr-2"></i><?= esc($title) ?>
    </h1>
    <a href="<?= route_to('super.clients') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar
    </a>
</div>

<!-- Alertas -->
<?= view('Back/layout/partials/alerts') ?>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit mr-1"></i>Dados do Cliente
        </h6>
    </div>
    <div class="card-body">
        <form action="<?= route_to('super.clients.create') ?>" method="POST">
            <?= csrf_field() ?>
            
            <!-- Dados Pessoais -->
            <h6 class="text-primary mb-3"><i class="fas fa-user mr-1"></i>Dados Pessoais</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= old('name', $client->name ?? '') ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">E-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= old('email', $client->email ?? '') ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="text" class="form-control phone-mask" id="phone" name="phone" 
                               value="<?= old('phone', $client->phone ?? '') ?>" placeholder="(00) 00000-0000">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cpf">CPF</label>
                        <input type="text" class="form-control cpf-mask" id="cpf" name="cpf" 
                               value="<?= old('cpf', $client->cpf ?? '') ?>" placeholder="000.000.000-00">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="birth_date">Data de Nascimento</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date" 
                               value="<?= old('birth_date', $client->birth_date ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="gender">Gênero</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="">Selecione...</option>
                            <option value="M" <?= old('gender', $client->gender ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= old('gender', $client->gender ?? '') === 'F' ? 'selected' : '' ?>>Feminino</option>
                            <option value="O" <?= old('gender', $client->gender ?? '') === 'O' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Endereço -->
            <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt mr-1"></i>Endereço</h6>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="zip_code">CEP</label>
                        <input type="text" class="form-control cep-mask" id="zip_code" name="zip_code" 
                               value="<?= old('zip_code', $client->zip_code ?? '') ?>" placeholder="00000-000">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address">Endereço</label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?= old('address', $client->address ?? '') ?>" placeholder="Rua, número, complemento">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="city">Cidade</label>
                        <input type="text" class="form-control" id="city" name="city" 
                               value="<?= old('city', $client->city ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="state">UF</label>
                        <select class="form-control" id="state" name="state">
                            <option value="">-</option>
                            <?php foreach ($states as $uf => $name): ?>
                                <option value="<?= $uf ?>" <?= old('state', $client->state ?? '') === $uf ? 'selected' : '' ?>>
                                    <?= $uf ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Observações e Status -->
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <label for="notes">Observações</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Informações adicionais sobre o cliente..."><?= old('notes', $client->notes ?? '') ?></textarea>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="active">Status</label>
                        <select class="form-control" id="active" name="active">
                            <option value="1" <?= old('active', $client->active ?? 1) == 1 ? 'selected' : '' ?>>Ativo</option>
                            <option value="0" <?= old('active', $client->active ?? 1) == 0 ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr>

            <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Salvar Cliente
                </button>
                <a href="<?= route_to('super.clients') ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- jQuery Mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(document).ready(function() {
    // Máscaras
    $('.phone-mask').mask('(00) 00000-0000');
    $('.cpf-mask').mask('000.000.000-00');
    $('.cep-mask').mask('00000-000');
    
    // Busca CEP
    $('#zip_code').on('blur', function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
                if (!data.erro) {
                    $('#address').val(data.logradouro);
                    $('#city').val(data.localidade);
                    $('#state').val(data.uf);
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
