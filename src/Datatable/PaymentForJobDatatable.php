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
        ]);
        $this->columnBuilder
            ->add('id', Column::class, [
                'title' => 'Id',
                'visible' => false,
            ])
            ->add('user.email', Column::class, [
                'title' => 'Usuario',
                'visible' => true,
            ])
            ->add('package.name', Column::class, [
                'title' => 'Paquete',
                'visible' => true,
            ])
            ->add('package.price', Column::class, [
                'title' => 'Precio',
                'visible' => true,
            ])
            ->add('date', VirtualColumn::class, [
                'title' => 'Fecha',
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