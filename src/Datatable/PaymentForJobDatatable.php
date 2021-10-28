<?php

/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 11/06/20
 * Time: 16:08
 */

namespace App\Datatable;

use App\Entity\Category;
use App\Entity\PaymentForJobs;
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
class PaymentForJobDatatable extends AbstractDatatable
{
    public function getLineFormatter()
    {
        return function ($row) {
            /** @var PaymentForJobsMetadata obj */
            $obj = $this->getEntityManager()
                ->getRepository(PaymentForJobsMetadata::class)
                ->find($row['id']);

            $row['date'] = $obj->getDatePurchase()->format('d/m/Y h:i');
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
            'scroll_x' => true
        ]);

        $this->columnBuilder
            ->add('id', Column::class, [
                'title' => 'Id',
                'visible' => false,
            ])
            ->add('date', VirtualColumn::class, [
                'title' => 'F. Venta',
            ])
            ->add('package.name', Column::class, [
                'title' => 'Plan',
                'visible' => true,
            ])
            ->add('package.price', Column::class, [
                'title' => 'Valor(USD)',
                'visible' => true,
            ])
            ->add('transaccion', Column::class, [
                'title' => 'Transacción',
                'visible' => true,
            ])
            ->add('user.companyName', Column::class, [
                'title' => 'Empresa',
                'visible' => true,
            ])
            ->add('user.ruc', Column::class, [
                'title' => 'RUC',
                'visible' => true,
            ])
            ->add('user.email', Column::class, [
                'title' => 'Contacto',
                'visible' => true,
            ])
            ->add('user.address', Column::class, [
                'title' => 'Dirección',
                'visible' => true,
            ])
            ->add('user.phone', Column::class, [
                'title' => 'Teléfono',
                'visible' => true,
            ]);
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return PaymentForJobsMetadata::class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'paymentForJob';
    }
}