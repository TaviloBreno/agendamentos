<?php

namespace App\Libraries;

use App\Models\ServiceModel;
use App\Entities\Service;

/**
 * ServiceService - Service para lógica de negócio de Serviços
 * 
 * =========================================================================
 * RESPONSABILIDADES DESTA SERVICE
 * =========================================================================
 * 
 * - Montar tabelas HTML para listagem
 * - Formatar dados para exibição
 * - Centralizar queries específicas
 * - Aplicar regras de negócio relacionadas a Serviços
 * 
 * @package    App\Libraries
 * @author     Sistema de Agendamentos
 */
class ServiceService extends MyBaseService
{
    /**
     * Opções de duração em minutos para dropdown
     * 
     * @var array<int, string>
     */
    private static array $durationOptions = [
        15  => '15 minutos',
        30  => '30 minutos',
        45  => '45 minutos',
        60  => '1 hora',
        90  => '1 hora e 30 minutos',
        120 => '2 horas',
        150 => '2 horas e 30 minutos',
        180 => '3 horas',
        240 => '4 horas',
    ];

    /**
     * Renderiza a tabela de Serviços para listagem
     * 
     * @return string HTML da tabela
     */
    public function renderServices(): string
    {
        $serviceModel = new ServiceModel();
        $services = $serviceModel->orderBy('name', 'ASC')->findAll();

        // Configuração da Table Class (herdada da MyBaseService)
        $this->htmlTable->setHeading([
            'ID',
            'Nome',
            'Duração',
            'Preço',
            'Status',
            'Ações',
        ]);

        // Popula a tabela com os dados
        foreach ($services as $service) {
            $this->htmlTable->addRow([
                $service->id,
                esc($service->name),
                $service->durationFormatted(),
                $service->priceFormatted(),
                $service->statusBadge(),
                $this->renderBtnActions($service),
            ]);
        }

        return $this->htmlTable->generate();
    }

    /**
     * Renderiza os botões de ação para cada serviço
     * 
     * @param Service $service
     * @return string HTML dos botões
     */
    public function renderBtnActions(Service $service): string
    {
        // Botão principal com dropdown
        $html = '<div class="btn-group">';
        $html .= '<a href="' . route_to('super.services.show', $service->id) . '" class="btn btn-sm btn-info">';
        $html .= '<i class="fas fa-eye"></i>';
        $html .= '</a>';
        $html .= '<button type="button" class="btn btn-sm btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false">';
        $html .= '<span class="sr-only">Toggle Dropdown</span>';
        $html .= '</button>';
        $html .= '<div class="dropdown-menu dropdown-menu-right">';
        
        // Link Editar
        $html .= '<a class="dropdown-item" href="' . route_to('super.services.edit', $service->id) . '">';
        $html .= '<i class="fas fa-edit fa-fw mr-2 text-primary"></i> Editar';
        $html .= '</a>';
        
        // Botão Ativar/Desativar via View Cell
        $html .= view_cell('ButtonsCell::action', [
            'route'      => route_to('super.services.action', $service->id),
            'text'       => $service->textToAction(),
            'icon'       => $service->iconToAction(),
            'colorClass' => $service->classToAction(),
        ]);
        
        $html .= '<div class="dropdown-divider"></div>';
        
        // Botão Excluir via View Cell
        $html .= view_cell('ButtonsCell::delete', [
            'route'       => route_to('super.services.delete', $service->id),
            'confirmText' => "Deseja realmente excluir o serviço \"{$service->name}\"?",
        ]);
        
        $html .= '</div>'; // dropdown-menu
        $html .= '</div>'; // btn-group

        return $html;
    }

    /**
     * Retorna as opções de duração para dropdown
     * 
     * @return array<int, string>
     */
    public static function getDurationOptions(): array
    {
        return self::$durationOptions;
    }

    /**
     * Renderiza o campo select de duração
     * 
     * @param int|null $selected Valor selecionado
     * @param string $name Nome do campo
     * @param string $class Classes CSS
     * @return string HTML do select
     */
    public function renderDurationSelect(?int $selected = null, string $name = 'duration', string $class = ''): string
    {
        $html = '<select name="' . esc($name) . '" id="' . esc($name) . '" class="form-control ' . esc($class) . '" required>';
        $html .= '<option value="">Selecione a duração...</option>';
        
        foreach (self::$durationOptions as $value => $label) {
            $isSelected = ($selected !== null && (int) $selected === $value) ? 'selected' : '';
            $html .= '<option value="' . $value . '" ' . $isSelected . '>' . esc($label) . '</option>';
        }
        
        $html .= '</select>';
        
        return $html;
    }
}
