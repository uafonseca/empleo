<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 11/06/20
 * Time: 17:40
 */

namespace App\Service;


use App\Entity\Job;
use App\Entity\PaymentForJobs;
use App\Entity\PaymentForJobsMetadata;
use App\Entity\PaymentForServices;
use App\Entity\PaymentForServicesMetadata;
use App\Entity\User;
use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @param Job $job
     */
    public function update(Job $job){
        $this->entityManager->persist($job);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @return array
     */
    public function getCurrentJobPackage(User $user)
    {
        $metas = [];
        /** @var PaymentForJobsMetadata $paymentForJobsMetadatum */
        foreach ($user->getPaymentForJobsMetadata() as $paymentForJobsMetadatum) {
            if ($paymentForJobsMetadatum->getActive())
                $metas[] = $paymentForJobsMetadatum;
        }
        return $metas;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getCurrentServicesPackage(User $user)
    {
        $metas = [];
        /** @var PaymentForServicesMetadata $paymentForServicesMetadatum */
        foreach ($user->getPaymentForServicesMetadata() as $paymentForServicesMetadatum) {
            if ($paymentForServicesMetadatum->getActive())
                $metas[] = $paymentForServicesMetadatum;
        }
        return $metas;
    }

    /**
     * @param User $user
     * @return ArrayCollection
     */
    public function findAviableJobsPacks(User $user)
    {
        $activesPacks = $this->entityManager->getRepository(PaymentForJobsMetadata::class)
            ->findBy(array('active' => true, 'user' => $user));

        $allPackages = $this->entityManager->getRepository(PaymentForJobs::class)->findAll();

        $collection = new ArrayCollection($allPackages);

        /** @var PaymentForJobsMetadata  $activesPack */
        foreach ($activesPacks as $activesPack)
        {
            $collection->removeElement($activesPack->getPackage());
        }
        return $collection;
    }

    /**
     * @param User $user
     * @return ArrayCollection
     */
    public function findAiableServicesPacks(User $user)
    {
        $activesPacks = $this->entityManager->getRepository(PaymentForServicesMetadata::class)
            ->findBy(array('active' => true, 'user' => $user));

        $allPackages = $this->entityManager->getRepository(PaymentForServices::class)->findAll();

        $collection = new ArrayCollection($allPackages);

        /** @var PaymentForJobsMetadata  $activesPack */
        foreach ($activesPacks as $activesPack)
        {
            $collection->removeElement($activesPack->getPackage());
        }
        return $collection;
    }
}