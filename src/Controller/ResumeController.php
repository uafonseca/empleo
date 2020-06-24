<?php

namespace App\Controller;

use App\Entity\Calification;
use App\Entity\Education;
use App\Entity\Ocupation;
use App\Entity\ProfessionalSkill;
use App\Entity\ResumeMetadata;
use App\Entity\User;
use App\Form\CalificationType;
use App\Form\EducationType;
use App\Form\OcupationType;
use App\Form\ProfesionalSkillType;
use App\Form\UserSkillsType;
use App\Service\UserService;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Valid;


/**
 * Class ResumeController
 * @package App\Controller
 * @Route("/resume")
 */
class ResumeController extends AbstractController
{
    /** @var UserService  */
    private $userService;

    /**
     * ResumeController constructor.
     * @param $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/skills", name="resume_skills", options={"expose" = true})
     */
    public function skills(Request $request)
    {
        /** @var User $loggedUser */
        $loggedUser = $this->getUser();

        $form = $this->createForm(UserSkillsType::class, $loggedUser,[
            'action' => $this->generateUrl('resume_skills')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var ResumeMetadata $skill */
            foreach ($loggedUser->getSkils() as $skill){
                $skill->setType(ResumeMetadata::TYPE_SKILL);
                $skill->setUser($loggedUser);
            }

            $this->userService->update($loggedUser);
            $this->addFlash('success','Habilidades modificadas');

            return $this->redirectToRoute('dashboard_resume_edit');
        }

        return $this->render('resume/skills_modal.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *  @Route("/about_me", name="resume_about_me", options={"expose" = true})
     */
    public function editAboutMe(Request $request)
    {
        /** @var User $loggedUser */
        $loggedUser = $this->getUser();

        $form = $this->createFormBuilder($loggedUser->getResume(),[
            'action' => $this->generateUrl('resume_about_me')
        ])
            ->add('about_me',CKEditorType::class,[
                'label' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Guardar'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->getDoctrine()->getManager()->persist($loggedUser->getResume());
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','Datos guardados');

            return $this->redirectToRoute('dashboard_resume_edit');
        }
        return $this->render('resume/about_modal.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/social_network", name="resume_social_network", options={"expose" = true})
     */
    public function socialNetworks(Request $request){
        /** @var User $loggedUser */
        $loggedUser = $this->getUser();

        return $this->render('resume/social_networks_modal.html.twig', [
            'user' => $loggedUser
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/metadata_educacion", name="metadata_educacion", options={"expose" = true})
     */
    public function saveMetadataAntecedente(Request $request)
    {
        /** @var User $loggedUser */
        $loggedUser = $this->getUser();

        $form =$this->createFormBuilder($loggedUser->getResume(),[
            'action' => $this->generateUrl('metadata_educacion')
        ])
            ->add('education', CollectionType::class,[
                'entry_type' => EducationType::class,
                'constraints' => array(new Valid()),
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'block_name' => 'education',
                'attr' => [
                    'class' => 'education-collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var Education $education */
            foreach ($loggedUser->getResume()->getEducation() as $education)
            {
                $education->setResume($loggedUser->getResume());
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success','Datos guardados');

            return $this->redirectToRoute('dashboard_resume_edit');
        }

        return $this->render('resume/education_modal.html.twig',[
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/metadata_ocupation", name="metadata_ocupation", options={"expose" = true})
     */
    public function saveMetadataOcupation(Request $request)
    {
        /** @var User $loggedUser */
        $loggedUser = $this->getUser();

        $form =$this->createFormBuilder($loggedUser->getResume(),[
            'action' => $this->generateUrl('metadata_ocupation')
        ])
            ->add('ocupations', CollectionType::class,[
                'entry_type' => OcupationType::class,
                'constraints' => array(new Valid()),
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'block_name' => 'ocupations',
                'attr' => [
                    'class' => 'ocupations-collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var Ocupation $ocupation */
            foreach ($loggedUser->getResume()->getOcupations() as $ocupation)
            {
                $ocupation->setResume($loggedUser->getResume());
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success','Datos guardados');

            return $this->redirectToRoute('dashboard_resume_edit');
        }

        return $this->render('resume/ocupation_modal.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/metadata_professional_skills", name="metadata_professional_skills", options={"expose" = true})
     */
    public function saveMetadataSkill(Request $request)
    {
        /** @var User $loggedUser */
        $loggedUser = $this->getUser();

        $form =$this->createFormBuilder($loggedUser->getResume(),[
            'action' => $this->generateUrl('metadata_professional_skills')
        ])
            ->add('professionalSkills', CollectionType::class,[
                'entry_type' => ProfesionalSkillType::class,
                'constraints' => array(new Valid()),
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'block_name' => 'professionalSkills',
                'attr' => [
                    'class' => 'professionalSkills-collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
             /** @var ProfessionalSkill $professionalSkill */
            foreach ($loggedUser->getResume()->getProfessionalSkills() as $professionalSkill)
            {
                $professionalSkill->setResume($loggedUser->getResume());
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success','Datos guardados');

            return $this->redirectToRoute('dashboard_resume_edit');
        }

        return $this->render('resume/professional_skills_modal.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/metadata_calification", name="metadata_calification", options={"expose" = true})
     */
    public function saveMetadataCalification(Request $request)
    {
        /** @var User $loggedUser */
        $loggedUser = $this->getUser();

        $form =$this->createFormBuilder($loggedUser->getResume(),[
            'action' => $this->generateUrl('metadata_calification')
        ])
            ->add('califications', CollectionType::class,[
                'entry_type' => CalificationType::class,
                'constraints' => array(new Valid()),
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'block_name' => 'califications',
                'attr' => [
                    'class' => 'califications-collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var Calification $calification */
            foreach ($loggedUser->getResume()->getCalifications() as $calification)
            {
                $calification->setResume($loggedUser->getResume());
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success','Datos guardados');

            return $this->redirectToRoute('dashboard_resume_edit');
        }

        return $this->render('resume/califications_modal.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
