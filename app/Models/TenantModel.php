<?php

namespace App\Models;

use App\Entities\Tenant;
use CodeIgniter\Model;

/**
 * TenantModel - Model para gerenciamento de tenants
 */
class TenantModel extends Model
{
    protected $table            = 'tenants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Tenant::class;
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'name',
        'slug',
        'domain',
        'email',
        'phone',
        'document',
        'logo',
        'address',
        'city',
        'state',
        'zip_code',
        'settings',
        'plan',
        'plan_expires_at',
        'max_users',
        'max_units',
        'max_appointments_month',
        'whatsapp_enabled',
        'payments_enabled',
        'status',
        'trial_ends_at',
    ];

    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[150]',
        'slug'     => 'required|alpha_dash|min_length[3]|max_length[100]|is_unique[tenants.slug,id,{id}]',
        'email'    => 'required|valid_email|max_length[150]|is_unique[tenants.email,id,{id}]',
        'phone'    => 'permit_empty|max_length[20]',
        'document' => 'permit_empty|max_length[20]',
        'plan'     => 'required|in_list[free,basic,professional,enterprise]',
        'status'   => 'required|in_list[active,inactive,suspended,trial]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'O nome da empresa é obrigatório.',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
        ],
        'slug' => [
            'required'   => 'O slug é obrigatório.',
            'alpha_dash' => 'O slug deve conter apenas letras, números e hífens.',
            'is_unique'  => 'Este slug já está em uso.',
        ],
        'email' => [
            'required'    => 'O email é obrigatório.',
            'valid_email' => 'Digite um email válido.',
            'is_unique'   => 'Este email já está em uso.',
        ],
    ];

    // =========================================================================
    // QUERIES ESPECIAIS
    // =========================================================================

    /**
     * Busca tenant por slug
     */
    public function findBySlug(string $slug): ?Tenant
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Busca tenant por domínio
     */
    public function findByDomain(string $domain): ?Tenant
    {
        return $this->where('domain', $domain)->first();
    }

    /**
     * Busca tenant ativo por slug ou domínio
     */
    public function findActiveBySlugOrDomain(string $identifier): ?Tenant
    {
        return $this->groupStart()
            ->where('slug', $identifier)
            ->orWhere('domain', $identifier)
            ->groupEnd()
            ->whereIn('status', ['active', 'trial'])
            ->first();
    }

    /**
     * Retorna tenants ativos
     */
    public function getActive(): array
    {
        return $this->whereIn('status', ['active', 'trial'])
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Retorna para dropdown
     */
    public function getForDropdown(): array
    {
        $tenants = $this->select('id, name')
            ->whereIn('status', ['active', 'trial'])
            ->orderBy('name', 'ASC')
            ->findAll();

        $result = [];
        foreach ($tenants as $tenant) {
            $result[$tenant->id] = $tenant->name;
        }

        return $result;
    }

    /**
     * Busca com estatísticas
     */
    public function getWithStats(): array
    {
        $tenants = $this->findAll();
        
        foreach ($tenants as $tenant) {
            $tenant->stats = $this->getTenantStats($tenant->id);
        }
        
        return $tenants;
    }

    /**
     * Retorna estatísticas de um tenant
     */
    public function getTenantStats(int $tenantId): array
    {
        $db = \Config\Database::connect();
        
        return [
            'users' => $db->table('users')
                ->where('tenant_id', $tenantId)
                ->countAllResults(),
            'units' => $db->table('units')
                ->where('tenant_id', $tenantId)
                ->countAllResults(),
            'appointments_month' => $db->table('appointments')
                ->where('tenant_id', $tenantId)
                ->where('MONTH(date)', date('m'))
                ->where('YEAR(date)', date('Y'))
                ->countAllResults(),
            'total_appointments' => $db->table('appointments')
                ->where('tenant_id', $tenantId)
                ->countAllResults(),
        ];
    }

    // =========================================================================
    // LIFECYCLE CALLBACKS
    // =========================================================================

    protected $beforeInsert = ['generateSlug', 'setTrialDefaults', 'setLimitsFromPlan'];
    protected $beforeUpdate = ['setLimitsFromPlan'];

    /**
     * Gera slug automaticamente se não fornecido
     */
    protected function generateSlug(array $data): array
    {
        if (empty($data['data']['slug']) && !empty($data['data']['name'])) {
            $slug = url_title($data['data']['name'], '-', true);
            
            // Verificar unicidade
            $count = 0;
            $originalSlug = $slug;
            while ($this->where('slug', $slug)->countAllResults() > 0) {
                $count++;
                $slug = $originalSlug . '-' . $count;
            }
            
            $data['data']['slug'] = $slug;
        }
        
        return $data;
    }

    /**
     * Define configurações padrão do trial
     */
    protected function setTrialDefaults(array $data): array
    {
        if (empty($data['data']['status'])) {
            $data['data']['status'] = 'trial';
        }
        
        if ($data['data']['status'] === 'trial' && empty($data['data']['trial_ends_at'])) {
            $data['data']['trial_ends_at'] = date('Y-m-d', strtotime('+14 days'));
        }
        
        return $data;
    }

    /**
     * Define limites baseado no plano
     */
    protected function setLimitsFromPlan(array $data): array
    {
        if (!empty($data['data']['plan'])) {
            $limits = Tenant::$planLimits[$data['data']['plan']] ?? Tenant::$planLimits['free'];
            
            // Só define se não foi especificado manualmente
            if (!isset($data['data']['max_users'])) {
                $data['data']['max_users'] = $limits['users'];
            }
            if (!isset($data['data']['max_units'])) {
                $data['data']['max_units'] = $limits['units'];
            }
            if (!isset($data['data']['max_appointments_month'])) {
                $data['data']['max_appointments_month'] = $limits['appointments_month'];
            }
        }
        
        return $data;
    }

    // =========================================================================
    // VALIDAÇÃO
    // =========================================================================

    /**
     * Verifica se o CNPJ é válido
     */
    public function validateCNPJ(string $cnpj): bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $sum = 0;
        $factor = 5;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $factor;
            $factor = ($factor == 2) ? 9 : $factor - 1;
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($cnpj[12] != $digit1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $sum = 0;
        $factor = 6;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $factor;
            $factor = ($factor == 2) ? 9 : $factor - 1;
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;

        return $cnpj[13] == $digit2;
    }
}
