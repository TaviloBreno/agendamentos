<?php

namespace App\Libraries;

use App\Models\ProfessionalModel;
use App\Entities\Professional;

/**
 * ProfessionalService - Service para lógica de negócio de Profissionais
 * 
 * =========================================================================
 * RESPONSABILIDADES
 * =========================================================================
 * 
 * - Montar tabelas HTML para listagem
 * - Formatar dados para exibição
 * - Centralizar queries específicas
 * 
 * @package    App\Libraries
 */
class ProfessionalService extends MyBaseService
{
    /**
     * Renderiza a tabela de Profissionais para listagem
     * 
     * @return string HTML da tabela
     */
    public function renderProfessionals(): string
    {
        $professionalModel = new ProfessionalModel();
        $professionals = $professionalModel->orderBy('name', 'ASC')->findAll();

        // Configuração da Table Class (herdada da MyBaseService)
        $this->htmlTable->setHeading([
            'ID',
            'Profissional',
            'E-mail',
            'Especialidade',
            'Serviços',
            'Status',
            'Ações',
        ]);

        // Popula a tabela com os dados
        foreach ($professionals as $professional) {
            $this->htmlTable->addRow([
                $professional->id,
                $this->renderProfessionalCell($professional),
                esc($professional->email),
                esc($professional->specialtyFormatted()),
                '<span class="badge badge-info">' . $professional->servicesCount() . '</span>',
                $professional->statusBadge(),
                $this->renderBtnActions($professional),
            ]);
        }

        return $this->htmlTable->generate();
    }

    /**
     * Renderiza a célula do profissional com avatar
     * 
     * @param Professional $professional
     * @return string HTML
     */
    protected function renderProfessionalCell(Professional $professional): string
    {
        $html = '<div class="d-flex align-items-center">';
        $html .= '<img src="' . $professional->avatarUrl() . '" class="rounded-circle mr-2" width="32" height="32" alt="' . esc($professional->name) . '">';
        $html .= '<div>';
        $html .= '<strong>' . esc($professional->name) . '</strong>';
        if ($professional->phone) {
            $html .= '<br><small class="text-muted">' . esc($professional->phone) . '</small>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Renderiza os botões de ação para cada profissional
     * 
     * @param Professional $professional
     * @return string HTML dos botões
     */
    public function renderBtnActions(Professional $professional): string
    {
        $html = '<div class="btn-group">';
        $html .= '<a href="' . route_to('super.professionals.show', $professional->id) . '" class="btn btn-sm btn-info">';
        $html .= '<i class="fas fa-eye"></i>';
        $html .= '</a>';
        $html .= '<button type="button" class="btn btn-sm btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false">';
        $html .= '<span class="sr-only">Toggle Dropdown</span>';
        $html .= '</button>';
        $html .= '<div class="dropdown-menu dropdown-menu-right">';
        
        // Link Editar
        $html .= '<a class="dropdown-item" href="' . route_to('super.professionals.edit', $professional->id) . '">';
        $html .= '<i class="fas fa-edit fa-fw mr-2 text-primary"></i> Editar';
        $html .= '</a>';
        
        // Botão Ativar/Desativar
        $html .= view_cell('ButtonsCell::action', [
            'route'      => route_to('super.professionals.action', $professional->id),
            'text'       => $professional->textToAction(),
            'icon'       => $professional->iconToAction(),
            'colorClass' => $professional->classToAction(),
        ]);
        
        $html .= '<div class="dropdown-divider"></div>';
        
        // Botão Excluir
        $html .= view_cell('ButtonsCell::delete', [
            'route'       => route_to('super.professionals.delete', $professional->id),
            'confirmText' => "Deseja realmente excluir o profissional \"{$professional->name}\"?",
        ]);
        
        $html .= '</div>'; // dropdown-menu
        $html .= '</div>'; // btn-group

        return $html;
    }
}
