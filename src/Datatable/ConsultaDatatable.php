<?php

/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 12/06/20
 * Time: 12:21
 */

namespace App\Datatable;


use App\Entity\Consulta;
use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;

/**
 * Class MyJobDatatable
 *
 * @package App\Datatable
 */
class ConsultaDatatable extends AbstractDatatable
{

    public function getLineFormatter()
    {
        return function ($row) {
            /** @var Consulta $consulta */
            $consulta = $this->getEntityManager()->getRepository(Consulta::class)->find($row['id']);
            $row['date'] = $consulta->getCreatedAt()->format('d/m/Y h:i');
            $row['respuesta'] = $consulta->getRespuestas()->count() > 0;
            return $row;
        };
    }

    /**
     * @param array $options
     * @throws Exception
     */
    public function buildDatatable(array $options = array())
    {
        $this->ajax->set([
            'url' => $options['url'],
            'method' => 'POST',
        ]);

        $props = $options['props'];


        $this->options->set([
            'classes' => 'table table-hover',
            'individual_filtering' => false,
            'order_cells_top' => true,
        ]);

        $this->features->set([
            'processing' => true,
        ]);
        $this->columnBuilder
            ->add('id', Column::class, [
                'title' => 'Id',
                'visible' => false,
            ])
            ->add('date', VirtualColumn::class, [
                'title' => 'Fecha de creación'
            ])
            ->add('city.name', Column::class, [
                'title' => 'Ciudad',
                'default_content' => '-'
            ])
            ->add('type', Column::class, [
                'title' => 'Tipo'
            ]);
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            if (isset($props['type']) && $props['type'] === 'company') {
                $this->columnBuilder
                    ->add('user.companyName', Column::class, [
                        'title' => 'Empresa',
                        'default_content' => 'NA'
                    ]);
            } elseif (isset($props['type']) && $props['type'] === 'user') {
                $this->columnBuilder
                    ->add('user.phone', Column::class, [
                        'title' => 'Teléfono',
                        'default_content' => 'NA'
                    ])
                    ->add('user.email', Column::class, [
                        'title' => 'Correo',
                        'default_content' => 'NA'
                    ]);
            }
            $this->columnBuilder
                ->add(null, ActionColumn::class, [
                    'title' => 'Acciones',
                    'actions' => [
                        TableActions::add('respuesta_consulta_new'),
                        TableActions::show('respuesta_consulta_mostrar'),
                    ]
                ]);
        } else {
            $this->columnBuilder
                ->add(null, ActionColumn::class, [
                    'title' => 'Acciones',
                    'actions' => [
                        [
                            'route' => 'respuesta_consulta_index',
                            'route_parameters' => array_merge(array(
                                'id' => 'id'
                            )),
                            'icon' => 'fa fa-eye text-success cortex-table-action-icon',
                            'attributes' => array(
                                'class' => 'action-show',
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'title' => "Ver respuesta"
                            ),
                            'render_if' => function ($row) {
                                return $row['respuesta'];
                            }
                        ]
                    ]
                ]);
        }
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return Consulta::class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'consultas';
    }
}
