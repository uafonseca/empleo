<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 10/06/20
 * Time: 14:37
 */

namespace App\Service;


use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var CategoryRepository
     */
    protected $repository;

    /**
     * CategoryService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Category::class);
    }

    public function findAll(){
        return $this->repository->findAll();
    }

    public function findAndCount(){
        return $this->repository->counter();
    }
}