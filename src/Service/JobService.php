<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 11/06/20
 * Time: 17:40
 */

namespace App\Service;


use App\Entity\Job;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;

class JobService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var JobRepository
     */
    protected $repository;

    /**
     * CategoryService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Job::class);
    }

    /**
     * @return array
     */
    public function findByAllCategory(){
        return $this->repository->getCounter();
    }

    /**
     * @return array|object[]
     */
    public function findAll(){
        return $this->repository->findAll();
    }
}