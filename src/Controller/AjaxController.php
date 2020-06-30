<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 23/02/2019
 * Time: 17:34
 */

namespace App\Controller;


use App\Entity\Anouncement;
use App\Entity\Category;
use App\Entity\Job;
use App\Entity\Metadata;
use App\Entity\Notification;
use App\Entity\Profession;
use App\Entity\Resume;
use App\Entity\User;
use App\Entity\UserJobMetadata;
use App\Repository\JobRepository;
use App\Repository\UserJobMetadataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\constants;

class AjaxController extends Controller
{

    /**
     * @Route("/ajax/social", name="ajax_social")
     */
    public function socialLinks(Request $request)
    {
        $fb = $request->request->get('fb');
        $twitter = $request->request->get('twitter');
        $google = $request->request->get('google');
        $linkedin = $request->request->get('linkedin');
        $printerest = $request->request->get('printerest');
        $instagram = $request->request->get('instagram');
        $behance = $request->request->get('behance');
        $dribbble = $request->request->get('dribbble');
        $github = $request->request->get('github');

        /** @var User $user */
        $user = $this->getUser();

        $user->setSocialLinks($this->externalLinkFilter($fb), 'fb');
        $user->setSocialLinks($this->externalLinkFilter($twitter), 'twitter');
        $user->setSocialLinks($this->externalLinkFilter($google), 'google');
        $user->setSocialLinks($this->externalLinkFilter($linkedin), 'linkedin');
        $user->setSocialLinks($this->externalLinkFilter($printerest), 'printerest');
        $user->setSocialLinks($this->externalLinkFilter($instagram), 'instagram');
        $user->setSocialLinks($this->externalLinkFilter($behance), 'behance');
        $user->setSocialLinks($this->externalLinkFilter($dribbble), 'dribbble');
        $user->setSocialLinks($this->externalLinkFilter($github), 'github');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('dashboard_resume_edit');
    }

    public function externalLinkFilter($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }

        return $url;
    }

    /**
     * @Route("/ajax/metadata/save", name="ajax_metadata_save")
     */
    public function saveMetadata(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $resume = $entityManager->getRepository(Resume::class)->findOneBy(
            array("id" => $request->request->get('id'))
        );
        $option = $request->request->get('option');
        if ($option == "1") {
            $meta = new Metadata();
            $meta->setResume($resume);
            $meta->setType(constants::METADATA_EDUCATION_DAO);
            $meta->setHeader($request->request->get('title'));
            $meta->setContext($request->request->get('institute'));
            $meta->setExtra($request->request->get('period'));
            $meta->setDescription($request->request->get('description'));
            $meta->setDateCreate(new \DateTime("now"));
            $entityManager->persist($meta);
            $entityManager->flush();
        } else {
            $count = $request->request->get('count');
            for ($i = 1; $i < $count; $i++) {
                if (null !== $up = $entityManager->getRepository(Metadata::class)->find(
                        $request->request->get('rid-' . $i)
                    )) {
                    $up->setHeader($request->request->get('title-' . $i));
                    $up->setContext($request->request->get('institute-' . $i));
                    $up->setExtra($request->request->get('period-' . $i));
                    $up->setDescription($request->request->get('description-' . $i));
                    $up->setDateCreate(new \DateTime("now"));
                    $entityManager->persist($up);
                    $entityManager->flush();
                } else {
                    $meta = new Metadata();
                    $meta->setResume($resume);
                    $meta->setType(constants::METADATA_EDUCATION_DAO);
                    $meta->setHeader($request->request->get('title-' . $i));
                    $meta->setContext($request->request->get('institute-' . $i));
                    $meta->setExtra($request->request->get('period-' . $i));
                    $meta->setDescription($request->request->get('description-' . $i));
                    $meta->setDateCreate(new \DateTime("now"));
                    $entityManager->persist($meta);
                    $entityManager->flush();
                }
            }
        }
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setData(
            array(
                'response' => 'success',
                'data' => 'done',
            )
        );

        return $response;
    }

    /**
     * @Route("/ajax/metadata/remove", name="ajax_metadata_remove")
     */
    function removeMetadata(Request $request)
    {
        $id = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $meta = $entityManager->getRepository(Metadata::class)->find($id);
        $entityManager->remove($meta);
        $entityManager->flush();
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setData(
            array(
                'response' => 'success',
                'data' => 'done',
            )
        );

        return $response;
    }

    /**
     * @Route("/ajax/metadata/expe/save", name="ajax_metadata_exp_save")
     */
    function saveExperence(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $meta = new Metadata();
        $meta->setDateCreate(new \DateTime("now"));
        $meta->setType(constants::METADATA_EXPERIENCE_DAO);
        $meta->setHeader($request->request->get('title'));
        $meta->setContext($request->request->get('company'));
        $meta->setExtra($request->request->get('period'));
        $meta->setDescription($request->request->get('description'));
        $resume = $entityManager->getRepository(Resume::class)->findOneBy(
            array("id" => $request->request->get('id'))
        );
        $meta->setResume($resume);
        $entityManager->persist($meta);
        $entityManager->flush();
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setData(
            array(
                'response' => 'success',
                'data' => 'done',
            )
        );

        return $response;
    }

    /**
     * @Route("/ajax/metadata/porcent/save", name="ajax_metadata_porcent_save")
     */
    function savePorcent(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        if (!empty($request->request->get('counter'))) {
            $counter = $request->request->get('counter');
            for ($i = 1; $i <= $counter; $i++) {
                $meta = $entityManager->getRepository(Metadata::class)->find($request->request->get('meta_id-' . $i));
                $meta->setHeader($request->request->get('name-' . $i));
                $meta->setContext($request->request->get('porcent-' . $i));
                $entityManager->flush();
            }
            $response = new JsonResponse();
            $response->setData(
                array(
                    'response' => 'success',
                    'data' => 'done',
                )
            );

            return $response;
        } else {
            $meta = new Metadata();
            $meta->setDateCreate(new \DateTime("now"));
            $meta->setType(constants::METADATA_PORCENT_DAO);
            $meta->setHeader($request->request->get('name'));
            $meta->setContext($request->request->get('porcent'));
            $resume = $entityManager->getRepository(Resume::class)->findOneBy(
                array("id" => $request->request->get('id'))
            );
            $meta->setResume($resume);
            $entityManager->persist($meta);
            $entityManager->flush();
            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(
                array(
                    'response' => 'success',
                    'data' => 'done',
                )
            );

            return $response;
        }
    }

    /**
     * @Route("/ajax/metadata/qualification/save", name="ajax_metadata_qualification_save")
     */
    function saveQualification(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $meta = new Metadata();
        $meta->setDateCreate(new \DateTime("now"));
        $meta->setType(constants::METADATA_QUALIFICATION_DAO);
        $meta->setHeader($request->request->get('name'));
        $resume = $entityManager->getRepository(Resume::class)->findOneBy(
            array("id" => $request->request->get('id'))
        );
        $meta->setResume($resume);
        $entityManager->persist($meta);
        $entityManager->flush();
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setData(
            array(
                'response' => 'success',
                'data' => 'done',
            )
        );

        return $response;
    }

    /**
     * @Route("/ajax/bookmark", name="ajax_bookmark")
     */
    function bookMark(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $id = $request->request->get('id');
        if (false !== $key = array_search($id, array_values($user->getBookmarked()), true)) {
            $user->removeBookMarked($key);
        } else {
            $user->addBookMarket($id);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setData(
            array(
                'response' => 'success',
                'data' => $request->request->get('id'),
            )
        );

        return $response;
    }

    /**
     * @param Request $request
     * @param UserJobMetadataRepository $userJobMetadataRepository
     * @param JobRepository $jobRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/ajax/applied", name="ajax_applied")
     */
    function applied(Request $request, UserJobMetadataRepository $userJobMetadataRepository, JobRepository $jobRepository)
    {
        $user = $this->getUser();

        /** @var Job $job */
        $job = $jobRepository->find($request->request->get('id'));

        /** @var UserJobMetadata $metadata */
        $metadata = $userJobMetadataRepository->findByUserJob($user, $job);

        $entityManager = $this->getDoctrine()->getManager();

        if ($metadata != null) {
            if ($metadata->getStatus() === UserJobMetadata::STATUS_CANCELED){
                $metadata->setStatus(UserJobMetadata::STATUS_APPLIED);
                $metadata->setAppiled(true);
                $this->notificate(
                    constants::NOTIFICATIONS_JOB_APPLIED_OK,
                    "Cancelaste tu propuesta: " . $job->getTitle(),
                    $user
                );
            }else{
                if ($metadata->getStatus() === UserJobMetadata::STATUS_APPLIED){
                    $metadata->setStatus(UserJobMetadata::STATUS_CANCELED);
                    $metadata->setAppiled(false);
                    $this->notificate(
                        constants::NOTIFICATIONS_JOB_APPLIED_CANCEL,
                        "Cancelaste tu propuesta: " . $job->getTitle(),
                        $user
                    );
                }
            }
        } else {
            $metadata = new UserJobMetadata();
            $metadata->setAppiled(true);
            $metadata
                ->setStatus(UserJobMetadata::STATUS_APPLIED)
                ->setDate(new \DateTime('now'))
                ->setUser($user)
                ->setJob($job);
            $entityManager->persist($metadata);
            $job->addUser($user);
            $entityManager->flush();
        }

        return new JsonResponse([
            'response' => 'success',
            'data' => $metadata->isAppiled(),
        ]);

    }

    function notificate($type, $context, $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $notification = new Notification();
        $notification->setDate(new \DateTime());
        $notification->setType($type);
        $notification->setContext($context);
        $notification->setUser($user);
        $notification->setActive(true);
        $entityManager->persist($notification);
        $entityManager->flush();
    }

    /**
     * @Route("/mail/sender", name="mail_sender")
     */
    public function sendEmail(Request $request, \Swift_Mailer $mailer)
    {
        try {
            $message = (new \Swift_Message('Notificación'))
                ->setFrom('emplearecuador@gmail.com')
                ->setBody(
                    $this->renderView(
                        'mail/contact.html.twig',
                        [
                            'remit' => $request->request->get('remit'),
                            'body' => $request->request->get('body'),
                            'email' => $request->request->get('email'),
                        ]
                    ),
                    'text/html'
                )
                ->setTo($request->request->get('destinate'));
            $mailer->send($message);
            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(
                array(
                    'response' => 'success',
                    'data' => 'success',
                )
            );
        } catch (\Exception $exception) {
            $response = new JsonResponse();
            $response->setStatusCode(423);
            $response->setData(
                array(
                    'response' => 'error',
                    'data' => 'error',
                )
            );
        }

        return $response;
    }

    /**
     * @Route("/ajax/contrata", name="ajax_contrata")
     */
    public function contrataAction(Request $request, \Swift_Mailer $mailer)
    {
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $service = $em->getRepository(Anouncement::class)->find($id);
        $serviceCreator = $service->getUser();
        $serviceCandidate = $this->get('security.token_storage')->getToken()->getUser();
        if ($serviceCandidate->getServicesRequest() != null && in_array($service->getId(), $serviceCandidate->getServicesRequest())) {
            $response = new JsonResponse();
            $response->setStatusCode(423);
            $response->setData(
                array(
                    'response' => 'Servicio solicitado previamente',
                    'data' => 'error',
                )
            );
            return $response;
        }
        try {
            $message = (new \Swift_Message('Notificación'))
                ->setFrom('emplearecuador@gmail.com')
                ->setBody(
                    $this->renderView(
                        'mail/contrata.html.twig',
                        [
                            'serviceCreator' => $serviceCreator,
                            'serviceCandidate' => $serviceCandidate,
                        ]
                    ),
                    'text/html'
                )
                ->setTo($serviceCreator->getEmail());
            $mailer->send($message);

            $this->notificate(
                constants::NOTIFICATIONS_JOB_APPLIED_OK,
                "Aplicaste al servicio: " . $service->getTitle(),
                $serviceCandidate
            );
            $this->notificate(
                constants::NOTIFICATIONS_JOB_APPLIED_ADMIN,
                "El usuario " . $serviceCandidate->getUsername() . " ha aplicado a su publicación:" . $service->getTitle(),
                $service->getUser()
            );
            $serviceCandidate->addServiceRequest($service->getId());
            $serviceCreator->addCandidate($serviceCandidate->getId());
            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(
                array(
                    'response' => 'success',
                    'data' => 'success',
                )
            );
            $em->flush();
            return $response;
        } catch (\Exception $exception) {
            $response = new JsonResponse();
            $response->setStatusCode(423);
            $response->setData(
                array(
                    'response' => $exception->getMessage(),
                    'data' => 'error',
                )
            );

            return $response;
        }

    }

    /**
     * @Route("/ajax/send/email/candidate", name="ajax_send_email_candidate")
     */
    public function sendEmailTocandidate(Request $request, \Swift_Mailer $mailer)
    {
        $subject = $request->request->get('email_subject');
        $body = $request->request->get('email_body');
        $email = $request->request->get('email');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        try {
            $message = (new \Swift_Message('Notificación: ' . $subject))
                ->setFrom('emplearecuador@gmail.com')
                ->setBody(
                    $this->renderView(
                        'mail/service_contact.html.twig',
                        [
                            'remit' => $user,
                            'body' => $body,
                        ]
                    ),
                    'text/html'
                )
                ->setTo($email);
            $mailer->send($message);
            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(
                array(
                    'response' => 'success',
                    'data' => 'success',
                )
            );
            return $response;

        } catch (\Exception $exception) {
            $response = new JsonResponse();
            $response->setStatusCode(423);
            $response->setData(
                array(
                    'response' => 'error',
                    'data' => 'error',
                )
            );
            return $response;
        }
    }
}