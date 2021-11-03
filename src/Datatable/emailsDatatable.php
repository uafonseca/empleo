<?php

/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 12/06/20
 * Time: 12:21
 */

namespace App\Datatable;

use App\constants;
use App\Entity\ContactMessage;
use App\Entity\Job;
use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Editable\CombodateEditable;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;

/**
 * Class MyJobDatatable
 *
 * @package App\Datatable
 */
class emailsDatatable extends AbstractDatatable
{

    public function getLineFormatter()
    {
        return function ($row) {
            /** @var ContactMessage $msg */
            $msg = $this->getEntityManager()->getRepository(ContactMessage::class)->find($row['id']);

            $row['date'] = $msg->getDate()->format('d/m/Y h:i');
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
                'title' => 'Fecha',
            ])
            ->add('context', Column::class, [
                'title' => 'Contenido',
            ]);

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $this->columnBuilder
                ->add('destinatario.name', Column::class, [
                    'title' => 'Destinatario',
                ]);
        } else {
            $this->columnBuilder
                ->add('creator.name', Column::class, [
                    'title' => 'Enviado por',
                ]);
        }
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return ContactMessage::class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mesage';
    }
}
