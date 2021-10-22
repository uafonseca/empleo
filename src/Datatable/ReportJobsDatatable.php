<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 11/06/20
 * Time: 16:08
 */

namespace App\Datatable;

use App\Entity\Job;
use App\Entity\PaymentForJobsMetadata;
use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;

/**
 * Class CategoryDatatable
 * @package App\Datatable
 */
class ReportJobsDatatable extends AbstractDatatable
{
    public function getLineFormatter()
    {
        return function ($row) {
            /** @var Job obj */
            $obj = $this->getEntityManager()
                ->getRepository(Job::class)
                ->find($row['id']);

                $row['date'] = $obj->getDateCreated()->format('d/m/Y h:i');
            return $row;
        };
    }
    /**
     * @param array $options
     * @throws Exception
     */
    public function buildDatatable(array $options = [])
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
                'title' => 'Fecha de publicación',
                'visible' => true,
            ])
            ->add('category.name', Column::class, [
                'title' => 'Categoría',
                'visible' => true,
            ])
            ->add('user.companyName', Column::class, [
                'title' => 'Empresa a la que aplica',
                'visible' => true,
            ])
            ->add('user.name', Column::class, [
                'title' => 'Nombre de usuario',
                'visible' => true,
            ])
            ->add('user.email', Column::class, [
                'title' => 'Correo',
                'visible' => true,
            ])
            ->add('user.phone', Column::class, [
                'title' => 'Teléfono',
                'visible' => true,
            ])
           ;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return Job::class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'paymentForJob';
    }
}