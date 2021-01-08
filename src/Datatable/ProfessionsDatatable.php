<?php
/**
 * Created by PhpStorm.
 * Name:  Ubel Angel Fonseca Cedeño
 * Email: ubelangelfonseca@gmail.com
 * Date:  7/1/21
 * Time:  09:33
 */

namespace App\Datatable;


use App\Entity\Profession;
use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;

/**
 * Class ProfessionsDatatable
 * @package App\Datatable
 */
class ProfessionsDatatable extends AbstractDatatable
{
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
            ->add('id', Column::class,[
                'title' => 'Id',
                'visible' => false,
            ])
            ->add('name',Column::class,[
                'title' => 'Nombre',
            ])
            ->add('description',Column::class,[
                'title' => 'Descripción',
            ])
            ->add(null,ActionColumn::class,[
                'title' => $this->translator->trans('sg.datatables.actions.title'),
                'actions' => [
                    TableActions::edit('profession_edit'),
                    TableActions::delete('profession_delete'),
                ]
            ])
        ;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return Profession::class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'professions_datatable';
    }
}