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

    public  function  getLineFormatter()
    {
        return function ($row){
            $user = $this->getEntityManager()->getRepository(User::class)->find($row['id']);

            $row['roles'] = in_array('ROLE_SUPER_ADMIN',$user->getRoles()) ? 'Adminisrador' : '';
            $row['roles'] = in_array('ROLE_ADMIN',$user->getRoles()) ? 'Empleador' : 'Candidato';
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
            ->add('enabled', Column::class, [
                'title' => 'enabled',
                'visible' => false,
            ])
            ->add('name', Column::class, [
                'title' => 'Nombres',
            ])
            ->add('username', Column::class, [
                'title' => 'Usuario',
            ])
            ->add('email', Column::class, [
                'title' => 'Correo',
            ])
            ->add('name', Column::class, [
                'title' => 'Nombre',
            ])
            ->add('phone', Column::class, [
                'title' => 'Teléfono',
            ])
            ->add('roles', VirtualColumn::class, [
                'title' => 'Tipo de cuenta',
            ])
            ->add(null, ActionColumn::class, [
                'title' => $this->translator->trans('sg.datatables.actions.title'),
                'actions' => [
                    [
                        'route' => 'userActivate',
                        'route_parameters' => array_merge(array(
                            'id' => 'id'
                        )),
                        'icon' => 'fa fa-power-off text-danger cortex-table-action-icon',
                        'attributes' => array(
                            'class' => 'action-show',
                            'color' => 'red',
                            'title' => "Desactivar"
                        ),
                        'render_if' => function ($row) {
                            return $row['enabled'];
                        }
                    ],
                    [
                        'route' => 'userActivate',
                        'route_parameters' => array_merge(array(
                            'id' => 'id'
                        )),
                        'icon' => 'fa fa-check-circle text-success cortex-table-action-icon',
                        'attributes' => array(
                            'class' => 'action-show',
                            'color' => 'red',
                            'title' => "Activar"
                        ),
                        'render_if' => function ($row) {
                            return !$row['enabled'];
                        }
                    ],
                    TableActions::delete('userRemove'),
                    TableActions::edit('userEdit'),
                ]
            ]);
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