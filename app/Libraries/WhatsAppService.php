<?php

namespace App\Libraries;

use App\Entities\Appointment;

/**
 * WhatsAppService - Integração com WhatsApp via Evolution API ou similar
 * 
 * Esta classe gerencia o envio de mensagens via WhatsApp.
 * Suporta múltiplos provedores: Evolution API, Twilio, Z-API, etc.
 */
class WhatsAppService
{
    protected string $provider;
    protected string $apiUrl;
    protected string $apiKey;
    protected string $instanceId;
    protected bool $enabled;

    public function __construct()
    {
        // Configurações do WhatsApp (adicione em .env)
        $this->enabled = env('WHATSAPP_ENABLED', false);
        $this->provider = env('WHATSAPP_PROVIDER', 'evolution'); // evolution, twilio, zapi
        $this->apiUrl = env('WHATSAPP_API_URL', '');
        $this->apiKey = env('WHATSAPP_API_KEY', '');
        $this->instanceId = env('WHATSAPP_INSTANCE_ID', '');
    }

    /**
     * Verifica se o serviço está habilitado
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiUrl) && !empty($this->apiKey);
    }

    /**
     * Envia mensagem de confirmação de agendamento
     */
    public function sendAppointmentConfirmation(Appointment $appointment): bool
    {
        if (!$this->isEnabled()) {
            log_message('info', 'WhatsApp: Serviço desabilitado');
            return false;
        }

        if (empty($appointment->client_phone)) {
            return false;
        }

        $message = $this->buildConfirmationMessage([
            'client_name' => $appointment->client_name,
            'date' => date('d/m/Y', strtotime($appointment->date)),
            'time' => substr($appointment->start_time, 0, 5),
            'service' => $appointment->service_name ?? '-',
            'professional' => $appointment->professional_name ?? '-',
            'unit' => $appointment->unit_name ?? '-',
            'address' => $appointment->unit_address ?? '-',
        ]);

        return $this->sendMessage($this->formatPhone($appointment->client_phone), $message);
    }

    /**
     * Envia lembrete de agendamento
     */
    public function sendAppointmentReminder(Appointment $appointment): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if (empty($appointment->client_phone)) {
            return false;
        }

        $message = $this->buildReminderMessage([
            'client_name' => $appointment->client_name,
            'date' => date('d/m/Y', strtotime($appointment->date)),
            'time' => substr($appointment->start_time, 0, 5),
            'service' => $appointment->service_name ?? '-',
            'professional' => $appointment->professional_name ?? '-',
            'unit' => $appointment->unit_name ?? '-',
            'address' => $appointment->unit_address ?? '-',
        ]);

        return $this->sendMessage($this->formatPhone($appointment->client_phone), $message);
    }

    /**
     * Envia notificação de cancelamento
     */
    public function sendAppointmentCancellation(Appointment $appointment, ?string $reason = null): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if (empty($appointment->client_phone)) {
            return false;
        }

        $message = $this->buildCancellationMessage([
            'client_name' => $appointment->client_name,
            'date' => date('d/m/Y', strtotime($appointment->date)),
            'time' => substr($appointment->start_time, 0, 5),
            'reason' => $reason,
        ]);

        return $this->sendMessage($this->formatPhone($appointment->client_phone), $message);
    }

    /**
     * Envia notificação de status alterado
     */
    public function sendStatusChange(Appointment $appointment, string $oldStatus): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if (empty($appointment->client_phone)) {
            return false;
        }

        $statusLabels = [
            'scheduled' => 'Agendado',
            'confirmed' => 'Confirmado',
            'completed' => 'Concluído',
            'cancelled' => 'Cancelado',
            'no_show' => 'Não Compareceu',
        ];

        $message = "🔔 *Atualização do Agendamento*\n\n";
        $message .= "Olá, {$appointment->client_name}!\n\n";
        $message .= "Seu agendamento foi atualizado:\n";
        $message .= "📅 *Data:* " . date('d/m/Y', strtotime($appointment->date)) . "\n";
        $message .= "⏰ *Horário:* " . substr($appointment->start_time, 0, 5) . "\n";
        $message .= "📊 *Novo Status:* " . ($statusLabels[$appointment->status] ?? $appointment->status) . "\n";

        return $this->sendMessage($this->formatPhone($appointment->client_phone), $message);
    }

    /**
     * Envia lembretes diários (para CRON)
     */
    public function sendDailyReminders(): array
    {
        if (!$this->isEnabled()) {
            return ['sent' => 0, 'failed' => 0, 'skipped' => 0];
        }

        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        $appointmentModel = model('AppointmentModel');
        $appointments = $appointmentModel
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('date', $tomorrow)
            ->findAll();

        $results = ['sent' => 0, 'failed' => 0, 'skipped' => 0];

        foreach ($appointments as $appointment) {
            try {
                if ($this->sendAppointmentReminder($appointment)) {
                    $results['sent']++;
                } else {
                    $results['skipped']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                log_message('error', 'WhatsApp reminder error: ' . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Envia mensagem personalizada
     */
    public function sendCustomMessage(string $phone, string $message): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return $this->sendMessage($this->formatPhone($phone), $message);
    }

    // =========================================================================
    // MÉTODOS PRIVADOS
    // =========================================================================

    /**
     * Envia mensagem através do provedor configurado
     */
    protected function sendMessage(string $phone, string $message): bool
    {
        try {
            return match ($this->provider) {
                'evolution' => $this->sendViaEvolution($phone, $message),
                'twilio' => $this->sendViaTwilio($phone, $message),
                'zapi' => $this->sendViaZApi($phone, $message),
                default => $this->sendViaEvolution($phone, $message),
            };
        } catch (\Exception $e) {
            log_message('error', "WhatsApp send error [{$this->provider}]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia via Evolution API
     */
    protected function sendViaEvolution(string $phone, string $message): bool
    {
        $url = rtrim($this->apiUrl, '/') . "/message/sendText/{$this->instanceId}";

        $data = [
            'number' => $phone,
            'text' => $message,
        ];

        $response = $this->makeRequest($url, $data, [
            'apikey: ' . $this->apiKey,
        ]);

        return isset($response['key']['id']);
    }

    /**
     * Envia via Twilio
     */
    protected function sendViaTwilio(string $phone, string $message): bool
    {
        // Implementação para Twilio
        // Requer: TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, TWILIO_WHATSAPP_NUMBER
        
        $accountSid = env('TWILIO_ACCOUNT_SID');
        $authToken = env('TWILIO_AUTH_TOKEN');
        $fromNumber = env('TWILIO_WHATSAPP_NUMBER');

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";

        $data = [
            'From' => "whatsapp:{$fromNumber}",
            'To' => "whatsapp:+{$phone}",
            'Body' => $message,
        ];

        $response = $this->makeRequest($url, $data, [], 'POST', "{$accountSid}:{$authToken}");

        return isset($response['sid']);
    }

    /**
     * Envia via Z-API
     */
    protected function sendViaZApi(string $phone, string $message): bool
    {
        $url = rtrim($this->apiUrl, '/') . "/send-text";

        $data = [
            'phone' => $phone,
            'message' => $message,
        ];

        $response = $this->makeRequest($url, $data, [
            'Client-Token: ' . $this->apiKey,
        ]);

        return isset($response['messageId']);
    }

    /**
     * Faz requisição HTTP
     */
    protected function makeRequest(string $url, array $data, array $headers = [], string $method = 'POST', ?string $auth = null): array
    {
        $curl = curl_init();

        $defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        $allHeaders = array_merge($defaultHeaders, $headers);

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $allHeaders,
        ];

        if ($auth) {
            $options[CURLOPT_USERPWD] = $auth;
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            log_message('error', "WhatsApp CURL error: {$error}");
            return [];
        }

        if ($httpCode >= 400) {
            log_message('error', "WhatsApp HTTP error {$httpCode}: {$response}");
            return [];
        }

        return json_decode($response, true) ?? [];
    }

    /**
     * Formata número de telefone para padrão internacional
     */
    protected function formatPhone(string $phone): string
    {
        // Remove caracteres não numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Se não começar com código do país, adiciona Brasil (55)
        if (strlen($phone) <= 11) {
            $phone = '55' . $phone;
        }

        return $phone;
    }

    /**
     * Monta mensagem de confirmação
     */
    protected function buildConfirmationMessage(array $data): string
    {
        $message = "✅ *Agendamento Confirmado!*\n\n";
        $message .= "Olá, {$data['client_name']}!\n\n";
        $message .= "Seu agendamento foi realizado com sucesso:\n\n";
        $message .= "📅 *Data:* {$data['date']}\n";
        $message .= "⏰ *Horário:* {$data['time']}\n";
        $message .= "💼 *Serviço:* {$data['service']}\n";
        $message .= "👤 *Profissional:* {$data['professional']}\n";
        $message .= "📍 *Local:* {$data['unit']}\n";
        $message .= "🗺️ *Endereço:* {$data['address']}\n\n";
        $message .= "Em caso de dúvidas ou para cancelamento, entre em contato conosco.\n\n";
        $message .= "_Mensagem automática - Não responda._";

        return $message;
    }

    /**
     * Monta mensagem de lembrete
     */
    protected function buildReminderMessage(array $data): string
    {
        $message = "⏰ *Lembrete de Agendamento*\n\n";
        $message .= "Olá, {$data['client_name']}!\n\n";
        $message .= "Este é um lembrete do seu agendamento para *amanhã*:\n\n";
        $message .= "📅 *Data:* {$data['date']}\n";
        $message .= "⏰ *Horário:* {$data['time']}\n";
        $message .= "💼 *Serviço:* {$data['service']}\n";
        $message .= "👤 *Profissional:* {$data['professional']}\n";
        $message .= "📍 *Local:* {$data['unit']}\n";
        $message .= "🗺️ *Endereço:* {$data['address']}\n\n";
        $message .= "Aguardamos você! 😊\n\n";
        $message .= "_Mensagem automática - Não responda._";

        return $message;
    }

    /**
     * Monta mensagem de cancelamento
     */
    protected function buildCancellationMessage(array $data): string
    {
        $message = "❌ *Agendamento Cancelado*\n\n";
        $message .= "Olá, {$data['client_name']}!\n\n";
        $message .= "Seu agendamento foi cancelado:\n\n";
        $message .= "📅 *Data:* {$data['date']}\n";
        $message .= "⏰ *Horário:* {$data['time']}\n";

        if (!empty($data['reason'])) {
            $message .= "\n📝 *Motivo:* {$data['reason']}\n";
        }

        $message .= "\nSe desejar, você pode realizar um novo agendamento.\n\n";
        $message .= "_Mensagem automática - Não responda._";

        return $message;
    }
}
