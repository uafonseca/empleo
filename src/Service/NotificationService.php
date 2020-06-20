<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 16/06/20
 * Time: 12:32
 */

namespace App\Service;


use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class NotificationService
 * @package App\Service
 */
class NotificationService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var NotificationRepository
     */
    protected $repository;

    /**
     * CategoryService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Notification::class);
    }

    /**
     * @return Notification[]|object[]
     */
    public function findAll(){
        return $this->repository->findAll();
    }

    /**
     * @return mixed
     */
    public function orderByDate(User $user){
        return $this->repository->orderByDate($user);
    }


    /**
     * @param Notification $notification
     */
    public function remove(Notification $notification)
    {
        $this->entityManager->remove($notification);
        $this->entityManager->flush();
    }
}