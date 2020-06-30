<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 30/06/20
 * Time: 13:56
 */

namespace App\Service;


use App\Entity\PaymentForJobsMetadata;
use App\Entity\User;
use App\Repository\PaymentForJobsMetadataRepository;
use Doctrine\ORM\EntityManagerInterface;

class PaymentForJobsMetadataService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var PaymentForJobsMetadataRepository
     */
    protected $repository;

    /**
     * CategoryService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(PaymentForJobsMetadata::class);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function checkFreePack(User $user)
    {
        return $this->repository->checkUser($user);
    }
}