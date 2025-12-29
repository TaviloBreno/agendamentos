<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * HomeController - Controller do Painel Administrativo
 * 
 * Demonstração do fluxo de dados: Controller → View → Layout
 * 
 * FLUXO DE RENDERIZAÇÃO DO CODEIGNITER 4:
 * =========================================
 * 1. O usuário acessa uma URL (ex: /admin)
 * 2. O Router direciona para este Controller
 * 3. O método index() é executado
 * 4. Criamos um array $data com os dados a serem enviados
 * 5. Chamamos view('caminho/da/view', $data)
 * 6. A view recebe cada índice do array como variável individual
 * 7. A view estende o layout e insere o conteúdo nas seções
 * 8. O HTML final é enviado ao navegador
 * 
 * MÉTODO view():
 * ==============
 * view(string $name, array $data = [], array $options = [])
 * 
 * - $name: Caminho da view (sem extensão .php)
 * - $data: Array associativo com dados para a view
 * - $options: Opções como 'cache' (NÃO usar para conteúdo dinâmico!)
 * 
 * IMPORTANTE: Cada chave do array $data vira uma variável na view
 * Exemplo: $data['title'] → acessível como $title na view
 */
class HomeController extends BaseController
{
    /**
     * Página inicial do painel administrativo
     * 
     * @return string
     */
    public function index(): string
    {
        /**
         * CONVENÇÃO DO CODEIGNITER: Array $data
         * =====================================
         * Utilizamos um array associativo chamado $data para organizar
         * todos os dados que serão enviados para a view.
         * 
         * Cada índice do array se torna uma variável disponível na view:
         * - $data['title']     → $title
         * - $data['pageTitle'] → $pageTitle
         * - $data['user']      → $user
         */
        $data = [
            // Título da página (usado na tag <title> do layout)
            'title' => 'Dashboard | Sistema de Agendamentos',
            
            // Título exibido no conteúdo da página
            'pageHeading' => 'Painel Principal',
            
            // Dados do usuário logado (simulação - futuramente virá da sessão)
            'userName' => 'João Silva',
            
            // Informações adicionais para demonstração
            'systemVersion' => '1.0.0',
            'totalAgendamentos' => 150,
            'agendamentosHoje' => 12,
            'clientesAtivos' => 45,
            
            // Array de exemplo (para demonstrar dados complexos)
            'ultimosAgendamentos' => [
                ['cliente' => 'Maria Santos', 'data' => '28/12/2025', 'hora' => '14:00'],
                ['cliente' => 'Pedro Oliveira', 'data' => '28/12/2025', 'hora' => '15:30'],
                ['cliente' => 'Ana Costa', 'data' => '28/12/2025', 'hora' => '16:00'],
            ],
        ];
        
        /**
         * RETORNO DA VIEW
         * ===============
         * O método view() aceita:
         * 
         * 1º parâmetro: Caminho da view (relativo a app/Views/)
         *    'Back/Home/index' → app/Views/Back/Home/index.php
         * 
         * 2º parâmetro: Array de dados ($data)
         *    Cada chave vira uma variável na view
         * 
         * 3º parâmetro (opcional): Array de opções
         *    ['cache' => 60] → Cache de 60 segundos
         *    ⚠️ NÃO usar cache para conteúdo dinâmico!
         * 
         * DEBUG TOOLBAR:
         * ==============
         * Para inspecionar os dados enviados:
         * 1. Acesse a página no navegador
         * 2. Clique no ícone do CodeIgniter (canto inferior)
         * 3. Vá na aba "Vars" (Variables)
         * 4. Expanda "View Data" para ver todas as variáveis
         */
        return view('Back/Home/index', $data);
    }
}
