<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 22/06/20
 * Time: 13:01
 */

namespace App\Service;

use App\Entity\PaymentForJobsMetadata;
use App\Entity\User;
use App\Repository\PaymentForJobsMetadataRepository;
use App\Repository\PaymentForServicesMetadataRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class UserService
 * @package App\Service
 */
class UserService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserRepository */
    private $repository;

    /** @var PaymentForJobsMetadataRepository */
    private $metadataRepository;

    /** @var PaymentForServicesMetadataRepository */
    private $metadataRepositoryServices;


    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param PaymentForJobsMetadataRepository $metadataRepository
     * @param PaymentForServicesMetadataRepository $metadataRepositoryServices
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PaymentForJobsMetadataRepository $metadataRepository,
        PaymentForServicesMetadataRepository $metadataRepositoryServices
    )
    {
        $this->entityManager = $entityManager;
        $this->metadataRepository = $metadataRepository;
        $this->metadataRepositoryServices = $metadataRepositoryServices;
        $this->repository = $this->entityManager->getRepository(User::class);
    }

    /**
     * @param User $user
     */
    public function update(User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function isReadyToGetJob(User $user)
    {
        $arr = $this->metadataRepository->checkUser($user);
        return $arr == null ? null : $arr[0];
    }

    public function isReadyToGetService(User $user)
    {
        $arr = $this->metadataRepositoryServices->checkUser($user);
        return $arr == null ? null : $arr[0];
    }
}