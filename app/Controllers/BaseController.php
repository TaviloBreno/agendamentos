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
     * HELPERS DISPONÍVEIS
     * =========================================================================
     * 
     * form    → form_open(), form_close(), form_hidden(), form_dropdown()
     * html    → anchor(), img(), link_tag()
     * general → showErrorInput(), hasErrorInput(), formatPhone(), formatDate()
     * 
     * Ao definir aqui, não precisa chamar helper() em cada view.
     * 
     * @var array<string>
     */
    protected $helpers = ['form', 'html', 'general'];

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

    /**
     * Limpa os dados do POST removendo campos de controle
     * 
     * =========================================================================
     * POR QUE ESTE MÉTODO EXISTE
     * =========================================================================
     * 
     * Quando usamos method spoofing (ex.: PUT via POST com _method),
     * o campo '_method' é enviado junto com os dados do formulário.
     * 
     * Se passarmos $this->request->getPost() diretamente para fill(),
     * a Entity receberá '_method' como propriedade, causando problemas.
     * 
     * Este método remove:
     * - _method    → Method spoofing do CodeIgniter
     * - csrf       → Token CSRF (nome configurado em Security.php)
     * - csrf_*     → Qualquer campo que comece com csrf_
     * 
     * =========================================================================
     * USO NO CONTROLLER
     * =========================================================================
     * 
     *   $data = $this->cleanRequest();
     *   $entity->fill($data);
     * 
     *   // Ou especificando campos extras para remover:
     *   $data = $this->cleanRequest(['campo_extra', 'outro_campo']);
     * 
     * @param array<string> $extraFields Campos adicionais para remover
     * @return array<string, mixed> Dados limpos do POST
     */
    protected function cleanRequest(array $extraFields = []): array
    {
        // Obtém todos os dados do POST
        $data = $this->request->getPost();
        
        // Campos de controle que devem ser removidos
        $controlFields = [
            '_method',  // Method spoofing (PUT, DELETE, PATCH via POST)
            'csrf',     // Token CSRF (nome padrão configurado)
        ];
        
        // Adiciona campos extras informados
        $fieldsToRemove = array_merge($controlFields, $extraFields);
        
        // Remove os campos de controle
        foreach ($fieldsToRemove as $field) {
            unset($data[$field]);
        }
        
        // Remove qualquer campo que comece com 'csrf_' (nomes alternativos)
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'csrf_')) {
                unset($data[$key]);
            }
        }
        
        return $data;
    }
}
