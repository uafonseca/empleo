<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 12/06/20
 * Time: 12:21
 */

namespace App\Datatable;


use App\Entity\Job;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;

/**
 * Class JobDatatable
 *
 * @package App\Datatable
 */
class JobDatatable extends AbstractDatatable
{

    /**
     * @param array $options
     * @throws \Exception
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
            ->add('user.name', Column::class,[
                'title' => 'Usuario',
            ])
            ->add('title', Column::class,[
                'title' => 'Título',
            ])
            ->add('category.name', Column::class,[
                'title' => 'Categoría',
            ])
            ->add('localtion', Column::class,[
                'title' => 'Ubicacción',
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
        return 'jobDatatable';
    }
}