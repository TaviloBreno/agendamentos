<?php

namespace App\Entities;

/**
 * Client Entity - Representa um cliente do sistema
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Entity para gerenciamento de clientes que fazem agendamentos.
 * Extende MyBaseEntity para herdar métodos de status (active).
 * 
 * FUNCIONALIDADES:
 * - Formatação de CPF e telefone
 * - Cálculo de idade
 * - Métodos para exibição formatada
 * 
 * @package    App\Entities
 * @author     Sistema de Agendamentos
 */
class Client extends MyBaseEntity
{
    /**
     * Campos permitidos para mass assignment
     * 
     * @var array<int, string>
     */
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'cpf',
        'birth_date',
        'gender',
        'address',
        'city',
        'state',
        'zip_code',
        'notes',
        'active',
    ];

    /**
     * Campos que devem ser convertidos para tipos específicos
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'id'         => 'integer',
        'active'     => 'int-bool',
        'birth_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Campos de data que devem ser mutados
     * 
     * @var array<int, string>
     */
    protected $dates = [
        'birth_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Labels de gênero
     */
    public static array $genderLabels = [
        'M' => 'Masculino',
        'F' => 'Feminino',
        'O' => 'Outro',
    ];

    /**
     * Lista de estados brasileiros
     */
    public static array $states = [
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MT' => 'Mato Grosso',
        'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins',
    ];

    // =========================================================================
    // MÉTODOS DE FORMATAÇÃO
    // =========================================================================

    /**
     * Retorna as iniciais do nome para avatar
     * 
     * @return string
     */
    public function initials(): string
    {
        $parts = explode(' ', $this->attributes['name'] ?? '');
        $initials = '';
        
        if (count($parts) >= 2) {
            $initials = strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
        } else {
            $initials = strtoupper(substr($parts[0] ?? '', 0, 2));
        }
        
        return $initials;
    }

    /**
     * Retorna URL do avatar usando ui-avatars.com
     * 
     * @param int $size Tamanho do avatar em pixels
     * @return string
     */
    public function avatarUrl(int $size = 64): string
    {
        $name = urlencode($this->attributes['name'] ?? 'Cliente');
        return "https://ui-avatars.com/api/?name={$name}&size={$size}&background=1cc88a&color=fff&bold=true";
    }

    /**
     * Retorna o CPF formatado (xxx.xxx.xxx-xx)
     * 
     * @return string|null
     */
    public function cpfFormatted(): ?string
    {
        $cpf = preg_replace('/[^0-9]/', '', $this->attributes['cpf'] ?? '');
        
        if (strlen($cpf) !== 11) {
            return $this->attributes['cpf'] ?? null;
        }
        
        return substr($cpf, 0, 3) . '.' . 
               substr($cpf, 3, 3) . '.' . 
               substr($cpf, 6, 3) . '-' . 
               substr($cpf, 9, 2);
    }

    /**
     * Retorna o telefone formatado
     * 
     * @return string|null
     */
    public function phoneFormatted(): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->attributes['phone'] ?? '');
        
        if (strlen($phone) === 11) {
            return '(' . substr($phone, 0, 2) . ') ' . 
                   substr($phone, 2, 5) . '-' . 
                   substr($phone, 7, 4);
        }
        
        if (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 2) . ') ' . 
                   substr($phone, 2, 4) . '-' . 
                   substr($phone, 6, 4);
        }
        
        return $this->attributes['phone'] ?? null;
    }

    /**
     * Retorna o CEP formatado (xxxxx-xxx)
     * 
     * @return string|null
     */
    public function zipCodeFormatted(): ?string
    {
        $zip = preg_replace('/[^0-9]/', '', $this->attributes['zip_code'] ?? '');
        
        if (strlen($zip) !== 8) {
            return $this->attributes['zip_code'] ?? null;
        }
        
        return substr($zip, 0, 5) . '-' . substr($zip, 5, 3);
    }

    /**
     * Retorna o gênero por extenso
     * 
     * @return string|null
     */
    public function genderLabel(): ?string
    {
        return self::$genderLabels[$this->attributes['gender'] ?? ''] ?? null;
    }

    /**
     * Retorna a data de nascimento formatada
     * 
     * @return string|null
     */
    public function birthDateFormatted(): ?string
    {
        if (empty($this->attributes['birth_date'])) {
            return null;
        }
        
        $date = $this->attributes['birth_date'];
        
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        
        return $date->format('d/m/Y');
    }

    /**
     * Calcula a idade do cliente
     * 
     * @return int|null
     */
    public function age(): ?int
    {
        if (empty($this->attributes['birth_date'])) {
            return null;
        }
        
        $birthDate = $this->attributes['birth_date'];
        
        if (is_string($birthDate)) {
            $birthDate = new \DateTime($birthDate);
        }
        
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        
        return $age;
    }

    /**
     * Retorna o endereço completo formatado
     * 
     * @return string|null
     */
    public function fullAddress(): ?string
    {
        $parts = [];
        
        if (!empty($this->attributes['address'])) {
            $parts[] = $this->attributes['address'];
        }
        
        if (!empty($this->attributes['city'])) {
            $cityState = $this->attributes['city'];
            if (!empty($this->attributes['state'])) {
                $cityState .= '/' . $this->attributes['state'];
            }
            $parts[] = $cityState;
        }
        
        if (!empty($this->attributes['zip_code'])) {
            $parts[] = 'CEP: ' . $this->zipCodeFormatted();
        }
        
        return !empty($parts) ? implode(' - ', $parts) : null;
    }

    /**
     * Retorna o nome do estado por extenso
     * 
     * @return string|null
     */
    public function stateName(): ?string
    {
        return self::$states[$this->attributes['state'] ?? ''] ?? null;
    }

    /**
     * Verifica se o cliente tem endereço cadastrado
     * 
     * @return bool
     */
    public function hasAddress(): bool
    {
        return !empty($this->attributes['address']) || 
               !empty($this->attributes['city']) || 
               !empty($this->attributes['state']);
    }

    /**
     * Retorna badge de status com estilo
     * 
     * @return string HTML do badge
     */
    public function statusBadge(): string
    {
        if ($this->isActive()) {
            return '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Ativo</span>';
        }
        
        return '<span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>Inativo</span>';
    }

    /**
     * Retorna a quantidade de agendamentos do cliente
     * 
     * @return int
     */
    public function appointmentsCount(): int
    {
        $appointmentModel = model('AppointmentModel');
        return $appointmentModel->where('client_email', $this->attributes['email'])->countAllResults();
    }

    /**
     * Retorna os últimos agendamentos do cliente
     * 
     * @param int $limit
     * @return array
     */
    public function recentAppointments(int $limit = 5): array
    {
        $appointmentModel = model('AppointmentModel');
        return $appointmentModel
            ->where('client_email', $this->attributes['email'])
            ->orderBy('date', 'DESC')
            ->orderBy('start_time', 'DESC')
            ->findAll($limit);
    }
}
