<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Models\TenantModel;
use App\Libraries\TenantService;

/**
 * TenantsController - CRUD de Empresas/Tenants
 * 
 * Gerencia as empresas cadastradas no sistema multi-tenant.
 * Apenas Super Admins podem acessar.
 */
class TenantsController extends BaseController
{
    protected TenantModel $tenantModel;
    protected TenantService $tenantService;

    public function __construct()
    {
        $this->tenantModel = model(TenantModel::class);
        $this->tenantService = new TenantService();
    }

    /**
     * Lista todas as empresas
     */
    public function index(): string
    {
        $tenants = $this->tenantModel->orderBy('name', 'ASC')->findAll();

        // Adicionar estatísticas
        foreach ($tenants as $tenant) {
            $tenant->stats = $this->tenantModel->getTenantStats($tenant->id);
        }

        $data = [
            'title' => 'Empresas',
            'pageHeading' => 'Gerenciar Empresas',
            'tenants' => $tenants,
        ];

        return view('Back/Tenants/index', $data);
    }

    /**
     * Formulário de criação
     */
    public function new(): string
    {
        $data = [
            'title' => 'Nova Empresa',
            'pageHeading' => 'Cadastrar Nova Empresa',
            'tenant' => null,
            'planOptions' => \App\Entities\Tenant::getPlanOptions(),
            'statusOptions' => \App\Entities\Tenant::getStatusOptions(),
        ];

        return view('Back/Tenants/form', $data);
    }

    /**
     * Processa criação
     */
    public function create()
    {
        $data = $this->getTenantPostData();

        $id = $this->tenantModel->insert($data);

        if ($id === false) {
            return redirect()->back()
                ->withInput()
                ->with('errorsValidation', $this->tenantModel->errors())
                ->with('danger', 'Verifique os erros de validação.');
        }

        return redirect()->to(route_to('super.tenants'))
            ->with('success', 'Empresa cadastrada com sucesso!');
    }

    /**
     * Exibe detalhes
     */
    public function show(int $id): string
    {
        $tenant = $this->tenantModel->find($id);

        if (!$tenant) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $stats = $this->tenantModel->getTenantStats($id);

        $data = [
            'title' => $tenant->name,
            'pageHeading' => 'Detalhes da Empresa',
            'tenant' => $tenant,
            'stats' => $stats,
        ];

        return view('Back/Tenants/show', $data);
    }

    /**
     * Formulário de edição
     */
    public function edit(int $id): string
    {
        $tenant = $this->tenantModel->find($id);

        if (!$tenant) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Editar ' . $tenant->name,
            'pageHeading' => 'Editar Empresa',
            'tenant' => $tenant,
            'planOptions' => \App\Entities\Tenant::getPlanOptions(),
            'statusOptions' => \App\Entities\Tenant::getStatusOptions(),
        ];

        return view('Back/Tenants/form', $data);
    }

    /**
     * Processa atualização
     */
    public function update(int $id)
    {
        $tenant = $this->tenantModel->find($id);

        if (!$tenant) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->getTenantPostData();

        $updated = $this->tenantModel->update($id, $data);

        if ($updated === false) {
            return redirect()->back()
                ->withInput()
                ->with('errorsValidation', $this->tenantModel->errors())
                ->with('danger', 'Verifique os erros de validação.');
        }

        return redirect()->to(route_to('super.tenants'))
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    /**
     * Processa exclusão
     */
    public function delete(int $id)
    {
        $tenant = $this->tenantModel->find($id);

        if (!$tenant) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Verificar se tem dados associados
        $stats = $this->tenantModel->getTenantStats($id);
        if ($stats['users'] > 0 || $stats['total_appointments'] > 0) {
            return redirect()->back()
                ->with('danger', 'Não é possível excluir esta empresa. Ela possui usuários ou agendamentos cadastrados.');
        }

        $this->tenantModel->delete($id);

        return redirect()->to(route_to('super.tenants'))
            ->with('success', 'Empresa excluída com sucesso!');
    }

    /**
     * Altera status
     */
    public function toggleStatus(int $id)
    {
        $tenant = $this->tenantModel->find($id);

        if (!$tenant) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $newStatus = $tenant->status === 'active' ? 'inactive' : 'active';
        $this->tenantModel->update($id, ['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Empresa ativada!' : 'Empresa desativada!';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Extrai dados do POST para Tenant
     */
    protected function getTenantPostData(): array
    {
        return [
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug'),
            'domain' => $this->request->getPost('domain') ?: null,
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'document' => $this->request->getPost('document'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'zip_code' => $this->request->getPost('zip_code'),
            'plan' => $this->request->getPost('plan'),
            'status' => $this->request->getPost('status'),
            'max_users' => $this->request->getPost('max_users'),
            'max_units' => $this->request->getPost('max_units'),
            'max_appointments_month' => $this->request->getPost('max_appointments_month'),
            'whatsapp_enabled' => $this->request->getPost('whatsapp_enabled') ? 1 : 0,
            'payments_enabled' => $this->request->getPost('payments_enabled') ? 1 : 0,
        ];
    }
}
