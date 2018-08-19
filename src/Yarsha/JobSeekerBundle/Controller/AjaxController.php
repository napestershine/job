<?php

namespace Yarsha\JobSeekerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\EmployerBundle\Entity\User;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;
use Yarsha\JobSeekerBundle\Entity\CoverLetter;
use Yarsha\JobSeekerBundle\Entity\JobSeekerEducation;
use Yarsha\JobSeekerBundle\Entity\JobSeekerExperience;
use Yarsha\JobSeekerBundle\Entity\JobSeekerLanguage;
use Yarsha\JobSeekerBundle\Entity\JobSeekerReference;
use Yarsha\JobSeekerBundle\Entity\JobSeekerSetting;
use Yarsha\JobSeekerBundle\Entity\JobSeekerTraining;
use Yarsha\JobSeekerBundle\Form\JobSeekerEducationPrototypeType;
use Yarsha\JobSeekerBundle\Form\JobSeekerEducationType;
use Yarsha\JobSeekerBundle\Form\JobSeekerExperienceType;
use Yarsha\JobSeekerBundle\Form\JobSeekerLanguagePrototypeType;
use Yarsha\JobSeekerBundle\Form\JobSeekerLanguageType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Yarsha\JobSeekerBundle\Form\JobSeekerReferencePrototypeType;
use Yarsha\JobSeekerBundle\Form\JobSeekerReferenceType;
use Yarsha\JobSeekerBundle\Form\JobSeekerRegistrationType;
use Yarsha\JobSeekerBundle\Form\JobSeekerSettingType;
use Yarsha\JobSeekerBundle\Form\JobSeekerTrainingType;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\JobSeekerBundle\Event\ProfileUpdateEvent;
use Yarsha\MainBundle\Service\ImageService;
use Yarsha\JobSeekerBundle\Form\JobSeekerTrainingPrototypeType;
use Yarsha\JobSeekerBundle\Form\JobSeekerGeneralInformation;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;

/**
 * Class AjaxController
 * @package Yarsha\JobSeekerBundle\Controller
 *
 */
class AjaxController extends Controller
{

    private $data = [];

    public function resumeAction(Request $request)
    {

        $form = $this->getUploadResumeForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->uploadCvAction($form);
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        }
    }

    /**
     * @param Request $request
     * @Route("/cv/upload", name="yarsha_ajax_job_seeker_upload_cv")
     */
    public function uploadCvAction(Request $request)
    {
        $data = [];
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('curriculumVitaeFile', FileType::class, [
                'data_class' => null,
                'required' => true,
                'label' => 'Upload Curriculum Vitae'
            ])
            ->getForm();
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $file = $form->get('curriculumVitaeFile')->getData();
            if ($file instanceof UploadedFile) {
                $user->setCurriculumVitaeFile($file);
                $user->uploadCv();
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                try {
                    $em->flush();
                    $this->profileUpdateEventAction($user);
                    $data['success'] = true;
                } catch (\Exception $e) {
                    $data['message'] = $e->getMessage();
                }
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     * @Route("/ajax/uploadcvform", name="yarsha_ajax_job_seeker_upload_resume_form_template")
     */
    public function getUploadCVForm()
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('curriculumVitaeFile', FileType::class, [
                'data_class' => null,
                'required' => true,
                'label' => 'Upload Curriculum Vitae'
            ])
            ->getForm();

        $data['uploadcvform'] = $form->createView();
        $response['template'] = $this->renderView('@YarshaJobSeeker/Resume/uploadresumeform.html.twig', $data);

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/upload/profile-pic", name="yarsha_job_seeker_ajax_upload_profile_pic")
     */
    public function cropImageAction(Request $request)
    {
        $data = [
            'message' => '',
            'result' => '',
            'state' => 500
        ];
        $user = $this->getUser();
        if ($user) {
            if ($request->isXmlHttpRequest()) {
                $file = $request->files->get('avatar_file');
                $source = $request->get('avatar_src');
                $data = $request->get('avatar_data');
                $files['error'] = $file->getError();
                $files['tmp_name'] = $file->getRealPath();
                $response = new ImageService(
                    $source ? $source : null,
                    $data ? $data : null,
                    $files ? $files : null
                );
                $data = [
                    'message' => $response->getMsg(),
                    'result' => $response->getResult(),
                    'state' => 200
                ];
                if ($response->success == true) {
                    $user->setPath($response->getFilename() . $response->getExtension());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    try {
                        $em->flush();
                    } catch (\Exception $e) {
                        $data['message'] = "Something went wrong";
                    }
                }
            }
        }

        return new JsonResponse($data, 200);
    }


    /**
     *
     * @Route("/ajax/add/education", name="yarsha_job_seeker_ajax_add_education")
     * @Route("/ajax/update/{educationId}/education", name="yarsha_job_seeker_ajax_update_education")
     */
    public function addEducationAction(Request $request)
    {
        $educationId = $request->get('educationId');
        if ($educationId) {
            $education = $this->getDoctrine()->getRepository(JobSeekerEducation::class)->find($educationId);
            $data['id'] = $educationId;
        } else {
            $education = new JobSeekerEducation();
            $data['id'] = '';
        }

        $form = $this->createForm(JobSeekerEducationType::class, $education);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $formData = $form->getData();
            $user = $this->getUser();
            $formData->setJobSeeker($user);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($formData);
            try {
                $em->flush();
                $response['message'] = 'Education Added';
                $this->profileUpdateEventAction($user);
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }

            return new JsonResponse($response);
        }
        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaJobSeekerBundle:Details:education.html.twig', $data);

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/education/{id}/delete", name="yarsha_ajax_seeker_education_delete")
     */
    public function deleteEducation(Request $request)
    {
        $data['success'] = false;
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($id) {
            $education = $em->getRepository(JobSeekerEducation::class)->findOneBy([
                'jobSeeker' => $user,
                'id' => $id
            ]);
            if ($education) {
                $em->remove($education);
            }
            try {
                $em->flush();
                $this->profileUpdateEventAction($user);
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['error'] = $e->getMessage();
            }
        }

        return $this->json($data);
    }

    /**
     *
     * @Route("/ajax/add/experience", name="yarsha_job_seeker_ajax_add_experience")
     * @Route("/ajax/update/{experienceId}/experience", name="yarsha_job_seeker_ajax_update_experience")
     */
    public function addExperienceAction(Request $request)
    {
        $response['success'] = true;
        $experienceId = $request->get('experienceId');
        if ($experienceId) {
            $experience = $this->getDoctrine()->getRepository(JobSeekerExperience::class)->find($experienceId);
            $data['id'] = $experienceId;
        } else {
            $experience = new JobSeekerExperience();
            $data['id'] = '';
        }

        $form = $this->createForm(JobSeekerExperienceType::class, $experience);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $formData = $form->getData();
            $user = $this->getUser();
            $formData->setJobSeeker($user);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($formData);
            try {
                $em->flush();
                $this->profileUpdateEventAction($user);
                $response['message'] = 'Experience Added';

            } catch (\Exception $e) {
                $response['success'] = false;
                $data['errorMessage'] = $e->getMessage();
                $response['message'] = $e->getMessage();
            }

        }


        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaJobSeekerBundle:Details:experience.html.twig', $data);

        return new JsonResponse($response);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/seeker/experience/{id}/delete",name="yarsha_ajax_job_seeker_experience_delete")
     */
    public function deleteExperience(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        $user = $this->getUser();
        if ($id) {
            $em = $this->getDoctrine()->getManager();
            $experience = $em->getRepository(JobSeekerExperience::class)->findOneBy([
                'jobSeeker' => $user,
                'id' => $id
            ]);
            $em->remove($experience);
            try {
                $em->flush();
                $this->profileUpdateEventAction($user);
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['error'] = $e->getMessage();
            }
        }

        return $this->json($data);
    }

    /**
     *
     * @Route("/ajax/add/training", name="yarsha_job_seeker_ajax_add_training")
     * @Route("/ajax/update/{trainingId}/training", name="yarsha_job_seeker_ajax_update_training")
     */
    public function addTrainingAction(Request $request)
    {
        $trainingId = $request->get('trainingId');
        if ($trainingId) {
            $training = $this->getDoctrine()->getRepository(JobSeekerTraining::class)->find($trainingId);
            $data['id'] = $trainingId;
        } else {
            $training = new JobSeekerTraining();
            $data['id'] = '';
        }

        $form = $this->createForm(JobSeekerTrainingType::class, $training);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $formData = $form->getData();
            $user = $this->getUser();
            $formData->setJobSeeker($user);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($formData);
            try {
                $em->flush();
                $response['message'] = 'Training Added';
                $this->profileUpdateEventAction($user);
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }

            return new JsonResponse($response);

        }


        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaJobSeekerBundle:Details:training.html.twig', $data);

        return new JsonResponse($response);

    }





    /**
     *
     * @Route("/ajax/add/language", name="yarsha_job_seeker_ajax_add_language")
     * @Route("/ajax/update/{languageId}/language", name="yarsha_job_seeker_ajax_update_language")
     */
    public function addLanguageAction(Request $request)
    {
        $languageId = $request->get('languageId');
        if ($languageId) {
            $language = $this->getDoctrine()->getRepository(JobSeekerLanguage::class)->find($languageId);
            $data['id'] = $languageId;
        } else {
            $language = new JobSeekerLanguage();
            $data['id'] = '';
        }

        $form = $this->createForm(JobSeekerLanguageType::class, $language);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $formData = $form->getData();


            $user = $this->getUser();
            $formData->setJobSeeker($user);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($formData);
            try {
                $em->flush();
                $this->profileUpdateEventAction($user);
                $response['message'] = 'Language Added';
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }

            return new JsonResponse($response);

        }


        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaJobSeekerBundle:Details:language.html.twig', $data);

        return new JsonResponse($response);

    }


    /**
     *
     * @Route("/ajax/add/reference", name="yarsha_job_seeker_ajax_add_reference")
     * @Route("/ajax/update/{referenceId}/reference", name="yarsha_job_seeker_ajax_update_reference")
     */
    public function addReferenceAction(Request $request)
    {
        $referenceId = $request->get('referenceId');
        if ($referenceId) {
            $language = $this->getDoctrine()->getRepository(JobSeekerReference::class)->find($referenceId);
            $data['id'] = $referenceId;
        } else {
            $language = new JobSeekerReference();
            $data['id'] = '';
        }

        $form = $this->createForm(JobSeekerReferenceType::class, $language);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $formData = $form->getData();


            $user = $this->getUser();
            $formData->setJobSeeker($user);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($formData);
            try {
                $em->flush();
                $this->profileUpdateEventAction($user);
                $response['message'] = 'Reference Added';
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }

            return new JsonResponse($response);

        }


        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaJobSeekerBundle:Details:reference.html.twig', $data);

        return new JsonResponse($response);

    }




    /**
     *
     * @Route("/ajax/add/setting", name="yarsha_job_seeker_ajax_add_setting")
     * @Route("/ajax/update/{settingId}/setting", name="yarsha_job_seeker_ajax_update_setting")
     */
    public function addSettingAction(Request $request)
    {
        $settingId = $request->get('settingId');
        if ($settingId) {
            $setting = $this->getDoctrine()->getRepository(JobSeekerSetting::class)->find($settingId);
            $data['id'] = $settingId;
        } else {
            $setting = new JobSeekerSetting();
            $data['id'] = '';
        }
        $form = $this->createForm(JobSeekerSettingType::class, $setting);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $formData = $form->getData();
            $user = $this->getUser();
            $formData->setJobSeeker($user);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($formData);
            try {
                $em->flush();
                $this->profileUpdateEventAction($user);
                $response['message'] = 'Setting Added';
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }

            return new JsonResponse($response);
        }
        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaJobSeekerBundle:Details:setting.html.twig', $data);

        return new JsonResponse($response);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("seeker/ajax/follow/{id}/employer", name="yarsha_frontend_follow_employer")
     */
    public function followEmployerAction(Request $request)
    {
        $response = [
            'success' => false,
            'errorMessage' => ''
        ];

        $organizationService = $this->get('yarsha.service.organization');
        $organization = $organizationService->getOrganizationById($request->get('id'));
        $seeker = $this->getUser();
        if ($organization and ($seeker instanceof JobSeeker)) {
            try {
                $organization->addFollower($seeker);
                $organizationService->getEntityManager()->persist($organization);
                $organizationService->getEntityManager()->flush();
                $response['success'] = true;
            } catch (\Exception $e) {
                $response['errorMessage'] = $e->getMessage();
            }
        }

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("seeker/ajax/unfollow/{id}/employer", name="yarsha_frontend_unfollow_employer")
     */
    public function unfollowEmployerAction(Request $request)
    {
        $response = [
            'success' => false,
            'errorMessage' => ''
        ];

        $organizationService = $this->get('yarsha.service.organization');
        $organization = $organizationService->getOrganizationById($request->get('id'));
        $seeker = $this->getUser();
        if ($organization and ($seeker instanceof JobSeeker)) {
            try {
                $organization->removeFollower($seeker);
                $organizationService->getEntityManager()->persist($organization);
                $organizationService->getEntityManager()->flush();
                $response['success'] = true;
            } catch (\Exception $e) {
                $response['errorMessage'] = $e->getMessage();
            }
        }

        return new JsonResponse($response);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/seeker/ajax/{id}/coverletter/default", name="yarsha_frontend_ajax_set_default_cover_letter")
     */
    public function setCoverLetterAsDefault($id)
    {
        $em = $this->getDoctrine()->getManager();
        $coverLetter = $em->getRepository(CoverLetter::class)->find($id);
        if (!$coverLetter) {
            $data['message'] = "Cover letter with that id does not exist.";
        }
        $coverLetters = $em->getRepository(CoverLetter::class)->findBy([
            'user' => $this->getUser()
        ]);

        foreach ($coverLetters as $c) {
            $c->setDefault(false);
        }

        $coverLetter->setDefault(true);
        $coverLetter->setUser($this->getUser());
        $em->persist($coverLetter);

        try {
            $em->flush();
            $data['success'] = true;
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
            $data['success'] = false;
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/seeker/ajax/updateProfile", name="yarsha_ajax_job_seeker_updateProfile")
     */
    public function updateProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $seeker = $this->getUser();
        $form = $this->createForm(JobSeekerRegistrationType::class, $seeker,
            ['registrationType' => false, 'is_updating' => true]);

        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $seekerData = $form->getData();
                $em->persist($seekerData);
                try {
                    $em->flush();
                    $this->profileUpdateEventAction($seeker);
                    $response['success'] = true;
                    $response['error'] = false;
                } catch (\Exception $e) {
                    $response['success'] = false;
                    $data['errorMessage'] = "Something went wrong. Please check values and submit.";
                }
            } else {
                $response['success'] = false;
            }
        }

        $data['form'] = $form->createView();
        $response['template'] = $this->renderView("YarshaJobSeekerBundle:Details:personalInformation.html.twig", $data);

        return new JsonResponse($response);
    }

    /**
     * @return JsonResponse
     * @Route("/ajax/uploadresumeform", name="yarsha_ajax_job_seeker_upload_resume_form")
     */
    public function getUploadResumeForm()
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('curriculumVitaeFile', FileType::class, [
                'data_class' => null,
                'required' => true,
                'label' => 'Change Curriculum Vitae'
            ])
            ->getForm();
        $data['form'] = $form->createView();
        $data['seeker'] = $user;
        $response['template'] = $this->renderView("@YarshaJobSeeker/Resume/vieweditresume.html.twig", $data);

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/jobseeker/training/{id}/delete", name="yarsha_ajax_job_seeker_delete_training")
     */
    public function deleteJobSeekerTraining(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($id) {
            $training = $em->getRepository(JobSeekerTraining::class)->find($id);
            $em->remove($training);
            try {
                $em->flush();
                $this->profileUpdateEventAction($user);
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['message'] = $e->getMessage();
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/jobseeker/language/{id}/delete", name="yarsha_ajax_job_seeker_delete_language")
     */
    public function deleteJobSeekerLanguage(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($id) {
            $language = $em->getRepository(JobSeekerLanguage::class)->find($id);
            $em->remove($language);
            try {
                $em->flush();
                $this->profileUpdateEventAction($user);
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['message'] = $e->getMessage();
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/jobseeker/reference/{id}/delete", name="yarsha_ajax_job_seeker_delete_reference")
     */
    public function deleteJobSeekerReference(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($id) {
            $reference = $em->getRepository(JobSeekerReference::class)->find($id);
            $em->remove($reference);
            try {
                $em->flush();
                $this->profileUpdateEventAction($user);
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['message'] = $e->getMessage();
            }
        }

        return new JsonResponse($data);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/seeker/ajax/updateGeneralInformation", name="yarsha_ajax_job_seeker_general_information")
     */
    public function updateGeneralInformationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $seeker = $this->getUser();
        $form = $this->createForm(JobSeekerGeneralInformation::class, $seeker);

        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $seekerData = $form->getData();
            $em->persist($seekerData);
            try {
                $em->flush();
                $this->profileUpdateEventAction($seeker);
                $response['success'] = true;
                $response['error'] = false;
            } catch (Exception $e) {
                $response['success'] = false;
                $response['error'] = $e->getMessage();
            }

            return new JsonResponse($response);
        }
        $data['form'] = $form->createView();
        $response['template'] = $this->renderView("YarshaJobSeekerBundle:Details:generalInformation.html.twig", $data);

        return new JsonResponse($response);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/form_validation", name="yarsha_ajax_seeker_form_validation")
     */
    public function seekerFormValidation(Request $request)
    {
        $username = $request->get('username');
        $contactEmail = $request->get('email');
        $type = $request->get('type');
        $em = $this->get('doctrine.orm.entity_manager');

        if ($type == '0') {

            if ($username) {

                $user = $em->getRepository(JobSeeker::class)->findOneBy(['username' => $username]);
                if ($user) {
                    $response['has_user'] = true;

                } else {
                    $response['has_user'] = false;
                    $response['formType'] = 'seeker';
                }
            }
            if ($contactEmail) {

                $userByEmail = $em->getRepository(JobSeeker::class)->findOneBy(['contactEmail' => $contactEmail]);
                if ($userByEmail) {
                    $response['has_user_by_email'] = true;
                } else {
                    $response['has_user_by_email'] = false;
                    $response['formType'] = 'seeker';
                }
            }

        } else {

            if ($username) {

                $user = $em->getRepository(user::class)->findOneBy(['username' => $username]);
                if ($user) {
                    $response['has_user'] = true;
                } else {
                    $response['has_user'] = false;
                    $response['formType'] = 'employer';
                }
            }
            if ($contactEmail) {

                $userByEmail = $em->getRepository(OrganizationContactPerson::class)->findOneBy(['email' => $contactEmail]);
                if ($userByEmail) {
                    $response['has_user_by_email'] = true;
                } else {
                    $response['has_user_by_email'] = false;
                    $response['formType'] = 'employer';
                }
            }
        }

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/registrationform/validate", name="yarsha_ajax_registration_form_validate");
     */
    function formValidationSeekerRegistration(Request $request)
    {
        $errors = [];
        $form = $this->createForm(JobSeekerRegistrationType::class, null);
        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);
            if (!$form->isValid()) {
                $errors = [
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorMessages($form)
                ];
            } else {
                $errors = [
                    'result' => 1,
                    'message' => 'ok',
                    'data' => ''
                ];
            }
        }

        return new JsonResponse($errors);
    }

    protected function getErrorMessages(\Symfony\Component\Form\Form $form)
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    /**
     * @param $user
     */
    public function profileUpdateEventAction($user)
    {
        $eventDispatcher = $this->get('event_dispatcher');
        $profileUpdateEvent = new ProfileUpdateEvent($user);
        $eventDispatcher->dispatch(MainBundleEvents::EVENT_JOB_SEEKER_PROFILE_UPDATE, $profileUpdateEvent);
    }

}
