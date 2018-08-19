<?php

namespace Yarsha\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\ArticleBundle\Entity\Notice;
use Yarsha\JobSeekerBundle\Form\JobSeekerEducationPrototypeType;
use Yarsha\JobSeekerBundle\Form\JobSeekerLanguagePrototypeType;
use Yarsha\JobSeekerBundle\Form\JobSeekerReferencePrototypeType;
use Yarsha\MainBundle\Entity\Country;
use Yarsha\MainBundle\Entity\EducationDegree;
use Yarsha\MainBundle\Form\CountryType;
use Yarsha\MainBundle\Form\LocationType;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\JobSeekerBundle\Entity\JobSeekerEducation;
use Yarsha\JobSeekerBundle\Entity\JobSeekerExperience;
use Yarsha\JobSeekerBundle\Entity\JobSeekerLanguage;
use Yarsha\JobSeekerBundle\Entity\JobSeekerReference;
use Yarsha\JobSeekerBundle\Entity\JobSeekerSetting;
use Yarsha\JobSeekerBundle\Entity\JobSeekerTraining;
use Yarsha\JobSeekerBundle\Form\JobSeekerEducationType;
use Yarsha\JobSeekerBundle\Form\JobSeekerExperienceType;
use Yarsha\JobSeekerBundle\Form\JobSeekerLanguageType;
use Yarsha\JobSeekerBundle\Form\JobSeekerTrainingPrototypeType;
use Yarsha\JobSeekerBundle\Form\JobSeekerReferenceType;

class AjaxControllerController extends Controller
{

    /**
     * @Route("admin/employer/{id}/enable", name="yarsha_admin_jobseeker_enable")
     */
    public function jobSeekerEnableAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $jobSeekerRepo = $manager->getRepository('YarshaJobSeekerBundle:User');
        $jobSeeker = $jobSeekerRepo->find($id);
        if ($jobSeeker) {
            try {
                $jobSeeker->setEnabled(true);
                $manager->persist($jobSeeker);
                $manager->flush();
            } catch (\Exception $e) {
                return new Response('Error: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('yarsha_admin_jobseeker_list');
    }

    /**
     * @Route("admin/employer/{id}/disable", name="yarsha_admin_jobseeker_disable")
     */
    public function jobSeekerDisableAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $jobSeekerRepo = $manager->getRepository('YarshaJobSeekerBundle:User');
        $jobSeeker = $jobSeekerRepo->find($id);
        if ($jobSeeker) {
            try {
                $jobSeeker->setEnabled(false);
                $manager->persist($jobSeeker);
                $manager->flush();
            } catch (\Exception $e) {
                return new Response('Error: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('yarsha_admin_jobseeker_list');
    }


    /**
     * @Route("admin/employee/{id}/searcheable", name="yarsha_admin_jobseeker_searcheable")
     */
    public function jobSeekerCvSearcheableAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $jobSeekerRepo = $manager->getRepository('YarshaJobSeekerBundle:User');
        $jobSeeker = $jobSeekerRepo->find($id);
        if ($jobSeeker) {
            try {
                $jobSeeker->setIsSearchable(true);
                $manager->persist($jobSeeker);
                $manager->flush();
                $response['message'] = 'success';
                $response['value'] = true;

            } catch (\Exception $e) {
                $response['message'] = 'error';
            }

            return new JsonResponse($response);
        }


    }

    /**
     * @Route("admin/employee/{id}/unsearcheable", name="yarsha_admin_jobseeker_unsearcheable")
     */
    public function jobSeekerCvUnsearcheableAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $jobSeekerRepo = $manager->getRepository('YarshaJobSeekerBundle:User');
        $jobSeeker = $jobSeekerRepo->find($id);
        if ($jobSeeker) {
            try {
                $jobSeeker->setIsSearchable(false);
                $manager->persist($jobSeeker);
                $manager->flush();
                $response['message'] = 'success';
                $response['value'] = false;
            } catch (\Exception $e) {
                $response['message'] = 'error';
            }

            return new JsonResponse($response);

        }


    }




    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("admin/ajax/country/add", name="yarsha_admin_ajax_add_country")
     */
    public function addCountryAction(Request $request)
    {
        $id = $request->get('id');
        if ($id) {
            $country = $this->get('doctrine.orm.entity_manager')->getRepository(Country::class)->find($id);
            $data['id'] = $country->getId();
        } else {
            $country = new Country();
            $data['id'] = "";
        }

        $form = $this->createForm(CountryType::class, $country);

        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);

            $postData = $form->getData();
            $this->get('doctrine.orm.entity_manager')->persist($postData);
            try {
                $this->get('doctrine.orm.entity_manager')->flush();

                return $this->redirectToRoute('yarsha_admin_country_list');
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();

                return new JsonResponse($response, 200);
            }
        }

        $data['form'] = $form->createView();

        $response['template'] = $this->renderView('@YarshaAdmin/Location/create.html.twig', $data);

        return new JsonResponse($response);


    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("admin/ajax/location/{id}/update", name="yarsha_admin_ajax_update_location")
     */
    public function updateLocation(Request $request)
    {
        $id = $request->get('id');
        $data['id'] = $id;
        $location = $this->get('yarsha.service.location')->getLocationById($id);


        $form = $this->createForm(LocationType::class, $location, ['isUpdatingLocation' => true]);

        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $postData = $form->getData();

            $this->get('doctrine.orm.entity_manager')->persist($postData);
            try {
                $this->get('doctrine.orm.entity_manager')->flush();

                return new JsonResponse('Location Updated', 200);

            } catch (\Exception $e) {
                return new JsonResponse($e->getMessage(), 400);
            }
        }
        $data['form'] = $form->createView();

        $response['template'] = $this->renderView('@YarshaAdmin/Location/update_location.html.twig', $data);

        return new JsonResponse($response);


    }


    /**
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("admin/ajax/country/{id}/location/add", name="yarsha_admin_ajax_add_location")
     */
    public function addLocation(Request $request)
    {
        $countryId = $request->get('id');
        $data['id'] = $countryId;
        $country = $this->get('yarsha.service.location')->getCountryById($countryId);
        $form = $this->createFormBuilder()
            ->add('locations', CollectionType::class, [
                'label_attr' => [
                    'class' => 'hidden',
                ],
                'entry_type' => LocationType::class,
                'allow_add' => true,
                'allow_delete' => true,

            ])
            ->getForm();
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $locations = $form->get('locations')->getData();

            foreach ($locations as $location) {
                $location->setCountry($country);
                $this->get('doctrine.orm.entity_manager')->persist($location);
            }

            try {
                $this->get('doctrine.orm.entity_manager')->flush();

                return $this->redirectToRoute("yarsha_admin_location_list", ['id' => $country->getId()]);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

        }
        $data['form'] = $form->createView();

        $response['template'] = $this->renderView('@YarshaAdmin/Location/create_location.html.twig', $data);

        return new JsonResponse($response);

    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/ajax/notice/{id}/view", name="yarsha_ajax_notice_view")
     */
    public function viewNotice($id)
    {
        $notice = $this->getDoctrine()->getManager()->getRepository(Notice::class)->find($id);
        $data['notice'] = $notice;

        if (!$notice) {
            $data['success'] = false;
        } else {
            $data['success'] = true;
            $data['noticedata'] = $this->renderView('YarshaAdminBundle:Notice:view.html.twig', $data);
        }

        return new JsonResponse($data);
    }

    /**
     * @param $id
     * @param $position
     * @return JsonResponse
     * @Route("/ajax/education/sort",name="yarsha_admin_ajax_education_sort")
     */
    public function sortEducationDegreeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data['success'] = false;
        $data['errorMessage'] = false;
        $data = $request->get('item');
        $counter = 1;
        foreach ($data as $position) {
            $education = $em->getRepository(EducationDegree::class)->find($position);
            $education->setSortOrder($counter);
            $em->persist($education);
            $counter++;
        }

        try {
            $em->flush();
            $data['success'] = true;
        } catch (Exception $e) {
            $data['errorMessage'] = "unable to sort";
        }

        return new JsonResponse($data);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/ajax/organization/{id}/contactperson/email", name="yarsha_ajax_admin_get_organization_contact_person_email")
     */
    public function getOrganizationContactPersonEmail(Request $request)
    {
        $data['success'] = false;
        $id = $request->get('id');
        $organization = $this->getDoctrine()->getManager()->getRepository(Organization::class)->find($id);
        if (!$id or !$organization) {
            $data['message'] = 'Unable to get data.';

            return new JsonResponse($data);
        }

        $contactPersons = $organization->getContactPersons();
        if (count($contactPersons) > 0) {
            $contactPerson = $contactPersons[0];
            $data['contactEmail'] = $contactPerson->getEmail();
            $data['success'] = true;
        } else {
            $data['message'] = 'No contact person.';
        }

        return new JsonResponse($data);
    }


    /**
     *
     * @Route("admin/ajax/add/{seekerId}/education", name="yarsha_admin_job_seeker_ajax_add_education")
     * @Route("admin/ajax/update/{educationId}/education", name="yarsha_admin_job_seeker_ajax_update_education")
     */
    public function addEducationAction(Request $request)
    {
        $educationId = $request->get('educationId');
        $seeker = $request->get('seekerId');
        if ($seeker) {
            $seekerService = $this->get('yarsha.service.job_seeker');
            $user = $seekerService->getSeekerById($seeker);
        }   else    {
            $response['message'] = "Something went wrong.";
            return new JsonResponse($response);
        }
        if ($educationId) {
            $data['id'] = $educationId;
            $data['seeker'] = '';
        } else {
            $data['id'] = '';
            $data['seeker'] = $seeker;
        }

        $form = $this->createForm(JobSeekerEducationPrototypeType::class, $user);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $educations = $form->get('educations')->getData();
            $user->setEducations($educations);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            try {
                $em->flush();
                $response['message'] = 'Education Added';
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }

            return new JsonResponse($response);
        }
        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('@YarshaAdmin/Details/education.html.twig', $data);

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("admin/ajax/education/{id}/delete", name="yarsha_admin_ajax_seeker_education_delete")
     */
    public function deleteEducation(Request $request)
    {
        $data['success'] = false;
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($id) {
            $education = $em->getRepository(JobSeekerEducation::class)->findOneBy([
                'id' => $id
            ]);
            if ($education) {
                $em->remove($education);
            }
            try {
                $em->flush();
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['error'] = $e->getMessage();
            }
        }

        return $this->json($data);
    }

    /**
     *
     * @Route("admin/ajax/add/{seekerId}/experience", name="yarsha_admin_job_seeker_ajax_add_experience")
     * @Route("admin/ajax/update/{experienceId}/experience", name="yarsha_admin_job_seeker_ajax_update_experience")
     */
    public function addExperienceAction(Request $request)
    {
        $response['success'] = true;
        $experienceId = $request->get('experienceId');
        $seeker = $request->get('seekerId');
        if ($experienceId) {
            $experience = $this->getDoctrine()->getRepository(JobSeekerExperience::class)->find($experienceId);
            $data['id'] = $experienceId;
            $data['seeker'] = '';
        } else {
            $experience = new JobSeekerExperience();
            $data['id'] = '';
            $data['seeker'] = $seeker;
        }

        $form = $this->createForm(JobSeekerExperienceType::class, $experience);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $formData = $form->getData();
            if ($seeker) {
                $seekerService = $this->get('yarsha.service.job_seeker');
                $user = $seekerService->getSeekerById($seeker);
                $formData->setJobSeeker($user);
            }

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($formData);
            try {
                $em->flush();
                //$this->profileUpdateEventAction($user);
                $response['message'] = 'Experience Added';

            } catch (\Exception $e) {
                $response['success'] = false;
                $data['errorMessage'] = $e->getMessage();
                $response['message'] = $e->getMessage();
            }

        }


        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaAdminBundle:Details:experience.html.twig', $data);

        return new JsonResponse($response);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("admin/ajax/seeker/experience/{id}/delete",name="yarsha_admin_ajax_job_seeker_experience_delete")
     */
    public function deleteExperience(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        //$user = $this->getUser();
        if ($id) {
            $em = $this->getDoctrine()->getManager();
            $experience = $em->getRepository(JobSeekerExperience::class)->findOneBy([
                'id' => $id
            ]);
            $em->remove($experience);
            try {
                $em->flush();
                // $this->profileUpdateEventAction($user);
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['error'] = $e->getMessage();
            }
        }

        return $this->json($data);
    }

    /**
     *
     * @Route("admin/ajax/add/{seekerId}/training", name="yarsha_admin_job_seeker_ajax_add_training")
     * @Route("admin/ajax/update/{trainingId}/training", name="yarsha_admin_job_seeker_ajax_update_training")
     */
    public function addTrainingAction(Request $request)
    {
        $trainingId = $request->get('trainingId');
        $seekerId = $request->get('seekerId');
        if ($seekerId) {
            $seeker = $this->get('yarsha.service.job_seeker')->getSeekerById($seekerId);
            if(!$seeker){
                return new JsonResponse(['message' => 'Something went wrong.']);
            }
        }
        if ($trainingId) {
            $training = $this->getDoctrine()->getRepository(JobSeekerTraining::class)->find($trainingId);
            $data['id'] = $trainingId;
            $data['seeker'] = '';
        } else {
            $training = new JobSeekerTraining();
            $data['id'] = '';
            $data['seeker'] = $seekerId;
        }

        $form = $this->createForm(JobSeekerTrainingPrototypeType::class, $seeker);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $trainings = $form->get('trainings')->getData();
            $seeker->setTrainings($trainings);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($seeker);
            try {
                $em->flush();
                $response['message'] = 'Training Added';
                $this->addFlash('success', 'Seeker training updated.');
                return $this->redirectToRoute('yarsha_admin_jobseeker_other_information', ['id' => $seekerId]);
                //$this->profileUpdateEventAction($user);
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }

            return new JsonResponse($response);

        }


        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaAdminBundle:Details:training.html.twig', $data);

        return new JsonResponse($response);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("admin/ajax/jobseeker/training/{id}/delete", name="yarsha_admin_ajax_job_seeker_delete_training")
     */
    public function deleteJobSeekerTraining(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        $em = $this->getDoctrine()->getManager();
        if ($id) {
            $training = $em->getRepository(JobSeekerTraining::class)->find($id);
            $em->remove($training);
            try {
                $em->flush();
                //$this->profileUpdateEventAction($user);
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['message'] = $e->getMessage();
            }
        }

        return new JsonResponse($data);
    }


    /**
     *
     * @Route("admin/ajax/add/{seekerId}/language", name="yarsha_admin_job_seeker_ajax_add_language")
     * @Route("admin/ajax/update/{languageId}/language", name="yarsha_admin_job_seeker_ajax_update_language")
     */
    public function addLanguageAction(Request $request)
    {
        $languageId = $request->get('languageId');
        $seekerId = $request->get('seekerId');
        if ($seekerId) {
            $seekerService = $this->get('yarsha.service.job_seeker');
            $user = $seekerService->getSeekerById($seekerId);
            if(!$user){
                return new JsonResponse(['message' => 'Something went wrong.']);
            }
        }

        if ($languageId) {
            $language = $this->getDoctrine()->getRepository(JobSeekerLanguage::class)->find($languageId);
            $data['id'] = $languageId;
            $data['seeker'] = '';
        } else {
            $language = new JobSeekerLanguage();
            $data['id'] = '';
            $data['seeker'] = $seekerId;
        }

        $form = $this->createForm(JobSeekerLanguagePrototypeType::class, $user);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $languages = $form->get('languages')->getData();
            $user->setLanguages($languages);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);

            try {
                $em->flush();
                //$this->profileUpdateEventAction($user);
                $response['message'] = 'Language Added';
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }

            return new JsonResponse($response);

        }


        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaAdminBundle:Details:language.html.twig', $data);

        return new JsonResponse($response);

    }

    /**
     *
     * @Route("admin/ajax/add/{seekerId}/reference", name="yarsha_admin_job_seeker_ajax_add_reference")
     * @Route("admin/ajax/update/{referenceId}/reference", name="yarsha_admin_job_seeker_ajax_update_reference")
     */
    public function addReferenceAction(Request $request)
    {
        $referenceId = $request->get('referenceId');
        $seekerId = $request->get('seekerId');
        if ($seekerId) {
            $seekerService = $this->get('yarsha.service.job_seeker');
            $user = $seekerService->getSeekerById($seekerId);
            if(!$user){
                return new JsonResponse(['message' => 'Something went wrong.']);
            }
        }
        if ($referenceId) {
//            $language = $this->getDoctrine()->getRepository(JobSeekerReference::class)->find($referenceId);
            $data['id'] = $referenceId;
            $data['seeker'] = '';
        } else {
//            $language = new JobSeekerReference();
            $data['id'] = '';
            $data['seeker'] = $seekerId;
        }

        $form = $this->createForm(JobSeekerReferencePrototypeType::class, $user);
        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $references = $form->get('references')->getData();
            $user->setReferences($references);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            try {
                $em->flush();
                //$this->profileUpdateEventAction($user);
                $response['message'] = 'Reference Added';
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }

            return new JsonResponse($response);

        }


        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaAdminBundle:Details:reference.html.twig', $data);

        return new JsonResponse($response);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("admin/ajax/jobseeker/language/{id}/delete", name="yarsha_admin_ajax_job_seeker_delete_language")
     */
    public function deleteJobSeekerLanguage(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        $em = $this->getDoctrine()->getManager();
        //$user = $this->getUser();
        if ($id) {
            $language = $em->getRepository(JobSeekerLanguage::class)->find($id);
            $em->remove($language);
            try {
                $em->flush();
                //$this->profileUpdateEventAction($user);
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
     * @Route("admin/ajax/jobseeker/reference/{id}/delete", name="yarsha_admin_ajax_job_seeker_delete_reference")
     */
    public function deleteJobSeekerReference(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        $em = $this->getDoctrine()->getManager();
        //$user = $this->getUser();
        if ($id) {
            $reference = $em->getRepository(JobSeekerReference::class)->find($id);
            $em->remove($reference);
            try {
                $em->flush();
                //$this->profileUpdateEventAction($user);
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['message'] = $e->getMessage();
            }
        }

        return new JsonResponse($data);
    }


}
