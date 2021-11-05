<?php

namespace App\Event;

use App\Entity\Job;
use Symfony\Contracts\EventDispatcher\Event;

class AlertEvent extends Event
{
    /** @var Job $job */
    private $job;   

    /**
     * Undocumented function
     *
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    /**
     * Undocumented function
     *
     * @param Job $job
     * @return void
     */
    public function setJob(Job $job){
        $this->job = $job;
    }

    /**
     * Undocumented function
     *
     * @return Job
     */
    public function getJob():Job{
        return $this->job;    
    }

}