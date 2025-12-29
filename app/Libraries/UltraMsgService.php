<?php

namespace App\Libraries;

/**
 * UltraMsg Service - Integração com API UltraMsg para WhatsApp
 * 
 * Documentação: https://docs.ultramsg.com/
 * 
 * =========================================================================
 * CONFIGURAÇÃO
 * =========================================================================
 * 
 * Adicione no .env:
 * 
 * ULTRAMSG_INSTANCE_ID = your_instance_id
 * ULTRAMSG_TOKEN = your_token
 * 
 * @package    App\Libraries
 */
class UltraMsgService
{
    /**
     * Instance ID da conta UltraMsg
     */
    protected string $instanceId;
    
    /**
     * Token de autenticação
     */
    protected string $token;
    
    /**
     * URL base da API
     */
    protected string $baseUrl = 'https://api.ultramsg.com';
    
    /**
     * Timeout para requisições (segundos)
     */
    protected int $timeout = 30;
    
    /**
     * Construtor - Inicializa credenciais
     */
    public function __construct()
    {
        $this->instanceId = env('ULTRAMSG_INSTANCE_ID', '');
        $this->token = env('ULTRAMSG_TOKEN', '');
    }
    
    /**
     * Verifica se o serviço está configurado
     */
    public function isConfigured(): bool
    {
        return !empty($this->instanceId) && !empty($this->token);
    }
    
    /**
     * Define as credenciais manualmente (para configuração por usuário)
     */
    public function setCredentials(string $instanceId, string $token): self
    {
        $this->instanceId = $instanceId;
        $this->token = $token;
        return $this;
    }
    
    /**
     * Envia uma mensagem de texto
     * 
     * @param string $to Número de telefone (com código do país, ex: 5511999999999)
     * @param string $message Mensagem a ser enviada
     * @return array Resposta da API
     */
    public function sendMessage(string $to, string $message): array
    {
        return $this->request('messages/chat', [
            'to'   => $this->formatPhoneNumber($to),
            'body' => $message,
        ]);
    }
    
    /**
     * Envia uma imagem com legenda
     * 
     * @param string $to Número de telefone
     * @param string $imageUrl URL da imagem
     * @param string $caption Legenda (opcional)
     * @return array Resposta da API
     */
    public function sendImage(string $to, string $imageUrl, string $caption = ''): array
    {
        return $this->request('messages/image', [
            'to'      => $this->formatPhoneNumber($to),
            'image'   => $imageUrl,
            'caption' => $caption,
        ]);
    }
    
    /**
     * Envia um documento
     * 
     * @param string $to Número de telefone
     * @param string $documentUrl URL do documento
     * @param string $filename Nome do arquivo
     * @return array Resposta da API
     */
    public function sendDocument(string $to, string $documentUrl, string $filename = ''): array
    {
        return $this->request('messages/document', [
            'to'       => $this->formatPhoneNumber($to),
            'document' => $documentUrl,
            'filename' => $filename,
        ]);
    }
    
    /**
     * Envia uma mensagem de template (para mensagens pré-aprovadas)
     * 
     * @param string $to Número de telefone
     * @param string $template Nome do template
     * @param array $params Parâmetros do template
     * @return array Resposta da API
     */
    public function sendTemplate(string $to, string $template, array $params = []): array
    {
        return $this->request('messages/template', [
            'to'       => $this->formatPhoneNumber($to),
            'template' => $template,
            'params'   => json_encode($params),
        ]);
    }
    
    /**
     * Verifica o status da instância
     * 
     * @return array Informações da instância
     */
    public function getInstanceStatus(): array
    {
        return $this->request('instance/status', [], 'GET');
    }
    
    /**
     * Obtém QR Code para conexão
     * 
     * @return array QR Code em base64
     */
    public function getQrCode(): array
    {
        return $this->request('instance/qr', [], 'GET');
    }
    
    /**
     * Reinicia a instância
     * 
     * @return array Resposta da API
     */
    public function restartInstance(): array
    {
        return $this->request('instance/restart', []);
    }
    
    /**
     * Desconecta a instância
     * 
     * @return array Resposta da API
     */
    public function logout(): array
    {
        return $this->request('instance/logout', []);
    }
    
    /**
     * Executa uma requisição para a API
     * 
     * @param string $endpoint Endpoint da API
     * @param array $data Dados a serem enviados
     * @param string $method Método HTTP (POST ou GET)
     * @return array Resposta da API
     */
    protected function request(string $endpoint, array $data = [], string $method = 'POST'): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error'   => 'UltraMsg não configurado. Verifique ULTRAMSG_INSTANCE_ID e ULTRAMSG_TOKEN.',
            ];
        }
        
        $url = "{$this->baseUrl}/{$this->instanceId}/{$endpoint}";
        
        $ch = curl_init();
        
        if ($method === 'POST') {
            $data['token'] = $this->token;
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } else {
            $url .= '?token=' . $this->token;
            if (!empty($data)) {
                $url .= '&' . http_build_query($data);
            }
        }
        
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            log_message('error', "UltraMsg API Error: {$error}");
            return [
                'success' => false,
                'error'   => $error,
            ];
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            $result['success'] = true;
            return $result;
        }
        
        log_message('error', "UltraMsg API Error: HTTP {$httpCode} - " . ($result['error'] ?? $response));
        
        return [
            'success'   => false,
            'http_code' => $httpCode,
            'error'     => $result['error'] ?? 'Erro desconhecido',
            'response'  => $result,
        ];
    }
    
    /**
     * Formata número de telefone para padrão internacional
     * 
     * @param string $phone Número de telefone
     * @return string Número formatado
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove tudo que não é número
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Se não começa com código do país, adiciona 55 (Brasil)
        if (strlen($phone) === 10 || strlen($phone) === 11) {
            $phone = '55' . $phone;
        }
        
        return $phone;
    }
}
