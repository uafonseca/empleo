<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 12/06/20
 * Time: 12:21
 */

namespace App\Datatable;


use App\constants;
use App\Entity\Job;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;

/**
 * Class JobDatatable
 *
 * @package App\Datatable
 */
class JobDatatable extends AbstractDatatable
{

    public function getLineFormatter()
    {
        return function ($row)
        {
            /** @var Job $job */
            $job = $this->getEntityManager()->getRepository(Job::class)->find($row['id']);

            switch ($job->getStatus())
            {
                case constants::JOB_STATUS_LOOCK:
                    $row['status'] = '<span class="badge badge-danger">BLOQUEADO</span>';
                    break;
                case constants::JOB_STATUS_ACTIVE:
                    $row['status'] = '<span class="badge badge-success">ACTIVO</span>';
                    break;
                case constants::JOB_STATUS_EXPIRED:
                    $row['status'] = '<span class="badge badge-warning">EXPIRADO</span>';
                    break;
                default:
                    $row['status'] = '<span class="badge badge-info">PENDIENTE</span>';
                    break;
            }
            return $row;

        };
    }

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
            ->add('status',VirtualColumn::class,[
                'title' => 'Estado',
            ])
            ->add(null,ActionColumn::class,[
                'title' => $this->translator->trans('sg.datatables.actions.title'),
                'actions' => [
                    TableActions::show('job_show_new'),
                    TableActions::delete('job_delete_backend'),
                ]
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