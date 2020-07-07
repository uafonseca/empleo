<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 26/06/20
 * Time: 13:56
 */

namespace App\Twig;


use App\Entity\Job;
use App\Entity\User;
use App\Entity\UserJobMetadata;
use App\Repository\UserJobMetadataRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class JobTwigExtension extends AbstractExtension
{

    /** @var UserJobMetadataRepository  */
    private $userJobMetadataRepository;

    /**
     * JobTwigExtension constructor.
     * @param UserJobMetadataRepository $userJobMetadataRepository
     */
    public function __construct(UserJobMetadataRepository $userJobMetadataRepository)
    {
        $this->userJobMetadataRepository = $userJobMetadataRepository;
    }


    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('get_job_applications', [$this, 'getJobApplications']),
            new TwigFunction('get_status_candidate', [$this, 'getStatusCandidate']),
            new TwigFunction('get_status_job', [$this, 'getJobStatus']),
        ];
    }

    /**
     * @param Job $job
     * @return int
     */
    public function getJobApplications(Job $job)
    {
        $count = 0;
        /** @var UserJobMetadata $metadata */
        foreach ($job->getUserJobMetadata() as $metadata){
            if ($metadata->getStatus() == UserJobMetadata::STATUS_APPLIED)
                $count ++ ;
        }
        return $count;
    }

    /**
     * @param Job $job
     * @param User $user
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getStatusCandidate(Job $job, User $user)
    {
        $state = $this->userJobMetadataRepository->findByUserJob($user, $job);

        if ($state !=null)
        {
            return $state->getStatus();
        }
        return 'Unknown';
    }

    /**
     * @param User $user
     * @param Job $job
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getJobStatus(User $user, Job $job){
        /** @var UserJobMetadata $state */
        $state = $this->userJobMetadataRepository->findByUserJob($user, $job);

        if ($state !=null)
        {
            return $state->isAppiled();
        }
        return false;
    }
}