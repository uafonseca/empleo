<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 22/06/20
 * Time: 13:01
 */

namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(User::class);
    }

    /**
     * @param User $user
     */
    public function update(User $user){
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}