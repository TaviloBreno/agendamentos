<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

/**
 * BaseApiController - Controller base para API REST
 * 
 * Fornece métodos comuns para todos os controllers da API
 */
class BaseApiController extends ResourceController
{
    use ResponseTrait;

    protected $format = 'json';

    /**
     * Retorna resposta de sucesso padronizada
     */
    protected function respondSuccess($data = null, string $message = 'Sucesso', int $code = 200)
    {
        return $this->respond([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Retorna resposta de erro padronizada
     */
    protected function respondError(string $message = 'Erro', int $code = 400, $errors = null)
    {
        return $this->respond([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }

    /**
     * Valida token de API
     */
    protected function validateApiKey(): bool
    {
        $apiKey = $this->request->getHeaderLine('X-API-Key');
        
        if (empty($apiKey)) {
            return false;
        }
        
        // Verificar API key no banco ou configuração
        $validKey = env('api.key', 'your-api-key-here');
        
        return $apiKey === $validKey;
    }

    /**
     * Retorna dados paginados
     */
    protected function paginatedResponse($model, array $conditions = [], int $perPage = 20)
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        
        foreach ($conditions as $field => $value) {
            $model->where($field, $value);
        }
        
        $data = $model->paginate($perPage);
        $pager = $model->pager;
        
        return $this->respondSuccess([
            'items'       => $data,
            'pagination'  => [
                'current_page' => $pager->getCurrentPage(),
                'per_page'     => $perPage,
                'total_items'  => $pager->getTotal(),
                'total_pages'  => $pager->getPageCount(),
            ],
        ]);
    }
}
