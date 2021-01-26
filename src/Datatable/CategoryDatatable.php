<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 11/06/20
 * Time: 16:08
 */

namespace App\Datatable;


use App\Entity\Category;
use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;

/**
 * Class CategoryDatatable
 * @package App\Datatable
 */
class CategoryDatatable extends AbstractDatatable
{

    /**
     * @return callable|\Closure|null
     */
    public function getLineFormatter()
    {
        return function ($row){
            /** @var Category $category */
            $category = $this->getEntityManager()->getRepository(Category::class)->find($row['id']);

            $row['ico'] = '<p><span class="'.$category->getIco().'"></span> '.$category->getIco().'</p>';

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
            ->add('id', Column::class,[
                'title' => 'Id',
                'visible' => false,
            ])
            ->add('name',Column::class,[
                'title' => 'Nombre',
            ])
            ->add('ico',VirtualColumn::class,[
                'title' => 'Icono',
            ])
            ->add('description',Column::class,[
                'title' => 'Descripción',
            ])
            ->add(null,ActionColumn::class,[
                'title' => $this->translator->trans('sg.datatables.actions.title'),
                'actions' => [
                    TableActions::edit('category_edit'),
                    TableActions::delete('category_delete'),
                ]
            ])
        ;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return Category::class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'categoryDatatable';
    }
}