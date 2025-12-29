<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * Helpers carregados globalmente para todos os controllers
     * 
     * =========================================================================
     * FORM HELPER
     * =========================================================================
     * 
     * O helper 'form' disponibiliza funções para geração de formulários:
     * 
     * - form_open()   → Abre <form> com CSRF automático
     * - form_close()  → Fecha </form>
     * - form_hidden() → Gera <input type="hidden">
     * - form_input()  → Gera <input type="text">
     * - form_label()  → Gera <label>
     * 
     * Ao definir aqui, não precisa chamar helper('form') em cada view.
     * 
     * @var array<string>
     */
    protected $helpers = ['form', 'html'];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    // =========================================================================
    // MÉTODOS UTILITÁRIOS PARA CONTROLLERS FILHOS
    // =========================================================================

    /**
     * Valida se o método HTTP da requisição é o esperado
     * 
     * =========================================================================
     * SEGURANÇA EM ROTAS RESTFUL
     * =========================================================================
     * 
     * Mesmo com rotas definidas para métodos específicos (POST, PUT, DELETE),
     * é uma boa prática validar no controller para reforçar segurança.
     * 
     * Uso:
     *   public function create()
     *   {
     *       $this->checkMethod('POST'); // Lança 404 se não for POST
     *       // ... processa criação
     *   }
     * 
     * @param string $expectedMethod Método esperado (GET, POST, PUT, DELETE, PATCH)
     * @return bool True se o método corresponde
     * @throws \CodeIgniter\Exceptions\PageNotFoundException Se método diferente
     */
    protected function checkMethod(string $expectedMethod): bool
    {
        $currentMethod = strtolower($this->request->getMethod());
        $expectedMethod = strtolower($expectedMethod);
        
        if ($currentMethod !== $expectedMethod) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Método HTTP inválido. Esperado: {$expectedMethod}, Recebido: {$currentMethod}"
            );
        }
        
        return true;
    }

    /**
     * Retorna os dados do formulário filtrados
     * 
     * Helper para obter apenas os campos especificados do POST.
     * 
     * @param array<string> $fields Campos a serem obtidos
     * @return array<string, mixed> Dados filtrados
     */
    protected function getPostData(array $fields): array
    {
        $data = [];
        
        foreach ($fields as $field) {
            $data[$field] = $this->request->getPost($field);
        }
        
        return $data;
    }
}
