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
            ->add('type', Column::class, [
                'title' => 'Tipo'
            ]);
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