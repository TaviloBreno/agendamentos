<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

/**
 * Unit Entity - Representa uma unidade/filial/loja
 * 
 * =========================================================================
 * CRIAÇÃO VIA CLI:
 * =========================================================================
 * php spark make:entity Unit
 * 
 * Isso cria: app/Entities/Unit.php
 * 
 * =========================================================================
 * O QUE É UMA ENTITY?
 * =========================================================================
 * 
 * Uma Entity representa uma única linha da tabela como um OBJETO.
 * 
 * VANTAGENS:
 * 1. Acesso a propriedades: $unit->name em vez de $unit['name']
 * 2. Métodos de comportamento: $unit->isActive(), $unit->getFullAddress()
 * 3. Mutators/Accessors: Transformar dados ao setar/obter
 * 4. Campos de data como objetos Time: $unit->created_at->format('d/m/Y')
 * 5. Encapsulamento de regras de negócio no objeto
 * 
 * SEM ENTITY (retorna array):
 *   $unit = $model->find(1);
 *   echo $unit['name'];        // Array associativo
 * 
 * COM ENTITY (retorna objeto Unit):
 *   $unit = $model->find(1);
 *   echo $unit->name;          // Propriedade do objeto
 *   echo $unit->startHour();   // Método do objeto
 * 
 * @package    App\Entities
 * @author     Sistema de Agendamentos
 * 
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $phone
 * @property string      $coordinator
 * @property string      $address
 * @property array|null  $services
 * @property string      $start_time
 * @property string      $end_time
 * @property string      $service_time
 * @property int         $active
 * @property Time|null   $created_at
 * @property Time|null   $updated_at
 */
class Unit extends Entity
{
    /**
     * Campos que devem ser tratados como datas
     * 
     * Esses campos são automaticamente convertidos para instâncias de
     * CodeIgniter\I18n\Time (similar ao DateTime, mas com mais recursos).
     * 
     * Isso permite: $unit->created_at->format('d/m/Y H:i')
     * 
     * @var array<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Casting de tipos para propriedades
     * 
     * Converte automaticamente os valores ao acessar/definir.
     * 
     * Tipos disponíveis:
     * - integer, float, double, string, boolean
     * - array, object, json, json-array
     * - datetime, timestamp, uri
     * 
     * IMPORTANTE: 'active' usa 'int-bool' para:
     * - Retornar boolean ao acessar (true/false)
     * - Aceitar tanto 0/1 quanto true/false ao definir
     * - Persistir como 0/1 no banco (TINYINT)
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'id'       => 'integer',
        'active'   => 'int-bool', // 0/1 no banco, true/false no PHP
        'services' => 'json-array', // Converte JSON para array PHP automaticamente
    ];

    /**
     * Mapeamento de propriedades para colunas do banco
     * 
     * Útil quando o nome da propriedade difere do nome da coluna.
     * Ex: $unit->horarioInicio mapeia para coluna start_time
     * 
     * @var array<string, string>
     */
    protected $datamap = [];

    // =========================================================================
    // MÉTODO DE PROVA DE CONCEITO
    // =========================================================================

    /**
     * Método de exemplo para demonstrar comportamento na Entity
     * 
     * PROVA DE CONCEITO:
     * Este método só está disponível quando $returnType = Unit::class no Model.
     * Se trocar para 'array' ou 'object', este método não existirá no retorno.
     * 
     * Para verificar no debug (dd), procure por 'validMethods' no dump.
     * 
     * @return string
     */
    public function lucio(): string
    {
        return 'Lúcio Antônio de Souza';
    }

    // =========================================================================
    // ACCESSORS (Getters) - Transformam dados ao OBTER
    // =========================================================================

    /**
     * Retorna o horário de início formatado
     * 
     * Exemplo de método de comportamento na Entity.
     * Demonstra a vantagem de ter lógica encapsulada no objeto.
     * 
     * Uso: $unit->startHour() ou $unit->getStartTimeFormatted()
     * 
     * @return string
     */
    public function startHour(): string
    {
        return $this->attributes['start_time'] ?? '00:00';
    }

    /**
     * Alias para startHour() seguindo convenção getXxx
     * 
     * @return string
     */
    public function getStartTimeFormatted(): string
    {
        return $this->startHour();
    }

    /**
     * Retorna o horário de término formatado
     * 
     * @return string
     */
    public function endHour(): string
    {
        return $this->attributes['end_time'] ?? '00:00';
    }

    /**
     * Retorna o expediente completo formatado
     * 
     * Exemplo: "08:00 às 18:00"
     * 
     * @return string
     */
    public function getWorkingHours(): string
    {
        return "{$this->startHour()} às {$this->endHour()}";
    }

    /**
     * Verifica se a unidade está ativa
     * 
     * Uso: if ($unit->isActive()) { ... }
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        return (int) ($this->attributes['active'] ?? 0) === 1;
    }

    /**
     * Retorna o status como texto legível
     * 
     * @return string
     */
    public function getStatusLabel(): string
    {
        return $this->isActive() ? 'Ativa' : 'Inativa';
    }

    /**
     * Retorna badge HTML para o status
     * 
     * Útil para exibição em views/tabelas
     * 
     * @return string
     */
    public function getStatusBadge(): string
    {
        if ($this->isActive()) {
            return '<span class="badge badge-success">Ativa</span>';
        }
        
        return '<span class="badge badge-secondary">Inativa</span>';
    }

    /**
     * Retorna os IDs dos serviços como array
     * 
     * O cast 'json-array' já faz isso, mas este método
     * garante retorno de array mesmo se null.
     * 
     * @return array<int>
     */
    public function getServicesArray(): array
    {
        return $this->attributes['services'] ?? [];
    }

    /**
     * Verifica se a unidade oferece determinado serviço
     * 
     * @param int $serviceId
     * @return bool
     */
    public function hasService(int $serviceId): bool
    {
        $services = $this->getServicesArray();
        return in_array($serviceId, $services, true);
    }

    /**
     * Retorna a data de criação formatada
     * 
     * @param string $format Formato da data (padrão: d/m/Y H:i)
     * @return string
     */
    public function getCreatedAtFormatted(string $format = 'd/m/Y H:i'): string
    {
        if ($this->created_at instanceof Time) {
            return $this->created_at->format($format);
        }
        
        return 'N/A';
    }

    // =========================================================================
    // MUTATORS (Setters) - Transformam dados ao DEFINIR
    // =========================================================================

    /**
     * Mutator para o campo name
     * 
     * Automaticamente chamado quando fazemos: $unit->name = 'valor'
     * 
     * @param string $value
     * @return $this
     */
    public function setName(string $value): self
    {
        // Capitaliza cada palavra e remove espaços extras
        $this->attributes['name'] = mb_convert_case(trim($value), MB_CASE_TITLE, 'UTF-8');
        
        return $this;
    }

    /**
     * Mutator para o campo email
     * 
     * @param string $value
     * @return $this
     */
    public function setEmail(string $value): self
    {
        // Sempre armazena em minúsculas
        $this->attributes['email'] = mb_strtolower(trim($value));
        
        return $this;
    }

    /**
     * Mutator para o campo phone
     * 
     * Remove caracteres não numéricos e formata
     * 
     * @param string $value
     * @return $this
     */
    public function setPhone(string $value): self
    {
        // Remove tudo que não for número
        $phone = preg_replace('/\D/', '', $value);
        
        // Formata como (99) 99999-9999 ou 99999-9999
        if (strlen($phone) === 11) {
            $phone = sprintf('(%s) %s-%s', 
                substr($phone, 0, 2),
                substr($phone, 2, 5),
                substr($phone, 7)
            );
        } elseif (strlen($phone) === 10) {
            $phone = sprintf('(%s) %s-%s',
                substr($phone, 0, 2),
                substr($phone, 2, 4),
                substr($phone, 6)
            );
        }
        
        $this->attributes['phone'] = $phone;
        
        return $this;
    }

    /**
     * Define os serviços a partir de um array
     * 
     * @param array<int>|string|null $value
     * @return $this
     */
    public function setServices($value): self
    {
        if (is_array($value)) {
            $this->attributes['services'] = json_encode(array_map('intval', $value));
        } elseif (is_string($value)) {
            $this->attributes['services'] = $value;
        } else {
            $this->attributes['services'] = null;
        }
        
        return $this;
    }

    // =========================================================================
    // MÉTODOS AUXILIARES
    // =========================================================================

    /**
     * Retorna dados resumidos da unidade
     * 
     * Útil para listagens, dropdowns, etc.
     * 
     * @return array<string, mixed>
     */
    public function toSummary(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'working_hours' => $this->getWorkingHours(),
            'active'        => $this->isActive(),
        ];
    }
}
