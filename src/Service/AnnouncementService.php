<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 16/06/20
 * Time: 10:15
 */

namespace App\Service;

use App\Entity\Anouncement;
use App\Repository\AnouncementRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AnnouncementService
 * @package App\Service
 */
class AnnouncementService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var AnouncementRepository
     */
    protected $repository;

    /**
     * CategoryService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Anouncement::class);
    }

    public function findAll(){
        return $this->repository->findAll();
    }
}