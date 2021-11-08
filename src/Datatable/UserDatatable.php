<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 11/06/20
 * Time: 16:08
 */

namespace App\Datatable;


use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use App\Entity\User;

/**
 * Class CategoryDatatable
 * @package App\Datatable
 */
class UserDatatable extends AbstractDatatable
{


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
            ->add('id', Column::class,[
                'title' => 'Id',
                'visible' => false,
            ])
            ->add('name',Column::class,[
                'title' => 'Nombre',
            ])
            ->add('username',Column::class,[
                'title' => 'Usuario',
            ])
            ->add('email',Column::class,[
                'title' => 'Correo',
            ])
            ->add('name',Column::class,[
                'title' => 'Nombre',
            ])
            ->add('phone',Column::class,[
                'title' => 'Teléfono',
            ])
            ->add('roles',Column::class,[
                'title' => 'Tipo de cuenta',
            ])
            ->add(null,ActionColumn::class,[
                'title' => $this->translator->trans('sg.datatables.actions.title'),
                'actions' => [
                    TableActions::default('userActivate', 'fa-check','',''),
                    TableActions::delete('userRemove'),
                ]
            ])
        ;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return User::class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'userDatatable';
    }
}