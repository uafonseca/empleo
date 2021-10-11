<?php
    /**
     * Created by PhpStorm.
     * User: Ubel
     * Date: 23/3/2019
     * Time: 20:28
     */
    
    namespace App\Service;

    use App\constants;
    use App\Entity\Anouncement;
    use App\Entity\Job;
    use App\Entity\User;
    use FOS\UserBundle\Event\GetResponseUserEvent;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Request;
    
    class Checker extends Controller
    {
        
        /**
         * Checker constructor.
         */
        public function __construct()
        {
        }
        
        public function checkJobs()
        {
            $em = $this->getDoctrine()->getManager();
            $jobs = $em->getRepository(Job::class)->findAll();
            foreach ($jobs as $job) {
                if ($job->getStatus() == constants::JOB_STATUS_ACTIVE) {
                    if ($job->getExpiredDate() < new \DateTime('now')) {
                        $job->setStatus(constants::JOB_STATUS_EXPIRED);
                    }
                }
                if ($job->getStatus() == constants::JOB_STATUS_PENDING) {
                    if ($job->getDate() >= new \DateTime('now')) {
                        $job->setStatus(constants::JOB_STATUS_ACTIVE);
                    }
                }
            }
            /**
			 * TODO temporal 
			 */
            // $em->flush();
            $aservices = $em->getRepository(Anouncement::class)->findAll();
            foreach ($aservices as $aservice) {
                if (($aservice->getDate() >= new \DateTime('now') && ($aservice->getExpiredDate() >= new \DateTime('now')))) {
                    $aservice->setStatus(constants::JOB_STATUS_ACTIVE);
                }
                if ($aservice->getStatus() == constants::JOB_STATUS_ACTIVE) {
                    if ($aservice->getExpiredDate() < new \DateTime('now')) {
                        $aservice->setStatus(constants::JOB_STATUS_EXPIRED);
                    }
                }
            }
            $em->flush();
        }
        
        public function isUserValid()
        {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            if (!$user->isVerificated()) {
                return false;
            }
            
            return true;
        }
    }
