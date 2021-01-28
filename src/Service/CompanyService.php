<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 17/06/20
 * Time: 21:17
 */

namespace App\Service;


use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CompanyService
 * @package App\Service
 */
class CompanyService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var CompanyRepository
     */
    protected $repository;

    /**
     * CategoryService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Company::class);
    }

    /**
     * @return Company[]|object[]
     */
    public function findAll(){
        return $this->repository->findAll();
    }

    public function findActives()
    {
        return $this->repository->findAll();
    }
}