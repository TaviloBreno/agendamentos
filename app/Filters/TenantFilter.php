<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\TenantModel;

/**
 * TenantFilter - Identifica e configura o tenant ativo
 * 
 * Este filtro identifica o tenant através de:
 * 1. Subdomínio (ex: clinica.seusite.com)
 * 2. Path (ex: seusite.com/t/clinica)
 * 3. Domínio customizado (ex: clinica.com.br)
 * 4. Sessão do usuário logado
 */
class TenantFilter implements FilterInterface
{
    /**
     * @var TenantModel
     */
    protected TenantModel $tenantModel;

    public function __construct()
    {
        $this->tenantModel = model(TenantModel::class);
    }

    /**
     * Executa antes do controller
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $tenant = $this->identifyTenant($request);
        
        if ($tenant) {
            // Verificar se o tenant está ativo
            if (!in_array($tenant->status, ['active', 'trial'])) {
                return $this->tenantInactive($tenant);
            }
            
            // Verificar se o trial expirou
            if ($tenant->isTrialExpired()) {
                return $this->trialExpired($tenant);
            }
            
            // Armazenar tenant no serviço global
            service('tenant')->setTenant($tenant);
            
            // Também na sessão para persistência
            session()->set('tenant_id', $tenant->id);
            session()->set('tenant_slug', $tenant->slug);
        }
        
        return null;
    }

    /**
     * Executa após o controller
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    /**
     * Identifica o tenant através de múltiplos métodos
     */
    protected function identifyTenant(RequestInterface $request): ?\App\Entities\Tenant
    {
        $tenant = null;
        
        // 1. Verificar path /t/{slug}
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        if (preg_match('#^/t/([a-zA-Z0-9\-]+)#', $path, $matches)) {
            $slug = $matches[1];
            $tenant = $this->tenantModel->findActiveBySlugOrDomain($slug);
        }
        
        // 2. Verificar subdomínio
        if (!$tenant) {
            $host = $request->getServer('HTTP_HOST') ?? '';
            $subdomain = $this->extractSubdomain($host);
            
            if ($subdomain && !in_array($subdomain, ['www', 'app', 'api', 'admin'])) {
                $tenant = $this->tenantModel->findActiveBySlugOrDomain($subdomain);
            }
        }
        
        // 3. Verificar domínio customizado
        if (!$tenant) {
            $host = $request->getServer('HTTP_HOST') ?? '';
            $tenant = $this->tenantModel->findByDomain($host);
        }
        
        // 4. Verificar sessão
        if (!$tenant && session()->has('tenant_id')) {
            $tenantId = session()->get('tenant_id');
            $tenant = $this->tenantModel->find($tenantId);
            
            // Verificar se ainda está ativo
            if ($tenant && !in_array($tenant->status, ['active', 'trial'])) {
                session()->remove('tenant_id');
                session()->remove('tenant_slug');
                $tenant = null;
            }
        }
        
        // 5. Verificar header X-Tenant-ID (para API)
        if (!$tenant) {
            $headerTenantId = $request->getHeaderLine('X-Tenant-ID');
            if ($headerTenantId) {
                $tenant = $this->tenantModel->find((int) $headerTenantId);
            }
        }
        
        return $tenant;
    }

    /**
     * Extrai subdomínio do host
     */
    protected function extractSubdomain(string $host): ?string
    {
        // Remove porta se houver
        $host = preg_replace('/:\d+$/', '', $host);
        
        // Divide por pontos
        $parts = explode('.', $host);
        
        // Se tiver mais de 2 partes (ex: clinica.seusite.com.br)
        // O primeiro elemento é o subdomínio
        if (count($parts) > 2) {
            return $parts[0];
        }
        
        return null;
    }

    /**
     * Resposta para tenant inativo
     */
    protected function tenantInactive(\App\Entities\Tenant $tenant)
    {
        $response = service('response');
        
        if (service('request')->isAJAX()) {
            return $response->setJSON([
                'success' => false,
                'error' => 'tenant_inactive',
                'message' => 'Esta empresa está temporariamente indisponível.',
            ])->setStatusCode(403);
        }
        
        return $response->setBody(view('errors/tenant_inactive', ['tenant' => $tenant]));
    }

    /**
     * Resposta para trial expirado
     */
    protected function trialExpired(\App\Entities\Tenant $tenant)
    {
        $response = service('response');
        
        if (service('request')->isAJAX()) {
            return $response->setJSON([
                'success' => false,
                'error' => 'trial_expired',
                'message' => 'O período de teste desta empresa expirou.',
            ])->setStatusCode(403);
        }
        
        return $response->setBody(view('errors/trial_expired', ['tenant' => $tenant]));
    }
}
