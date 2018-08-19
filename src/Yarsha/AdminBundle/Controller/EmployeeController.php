<?php

namespace Yarsha\AdminBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Yarsha\ArticleBundle\Entity\Testimonial;
use Yarsha\JobSeekerBundle\Entity\CoverLetter;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;
use Yarsha\JobSeekerBundle\Entity\EmployeeJobBasket;
use Yarsha\JobSeekerBundle\Entity\JobSeekerSetting;
use Yarsha\JobSeekerBundle\Entity\ProfileCompletion;
use Yarsha\JobSeekerBundle\Entity\User;
use Yarsha\EmployerBundle\Entity\User as Employer;
use Yarsha\JobSeekerBundle\Form\JobSeekerRegistrationType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Yarsha\JobSeekerBundle\YarshaJobSeekerEvents;
use Yarsha\MainBundle\Entity\Emails;
use Yarsha\MainBundle\MainBundleConstants;
use Yarsha\JobSeekerBundle\Entity\JobSeekerCallRecord;
use Yarsha\AdminBundle\Form\SeekerCallRecordType;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * Class EmployeeController
 * @package Yarsha\AdminBundle\Controller
 * @Breadcrumb("Employee", routeName="yarsha_admin_jobseeker_list")
 */
class EmployeeController extends Controller
{

    /**
     * @Route("admin/jobseeker/list", name="yarsha_admin_jobseeker_list")
     * @Breadcrumb("list")
     */
    public function jobSeekerlistAction(Request $request)
    {

        $filters = $request->query->all();
        $jobSeekerService = $this->get('yarsha.admin.service.jobseeker');
        $jobSeekers = $jobSeekerService->getPaginatedJobSeekers($filters);
        $data['jobSeekers'] = $jobSeekers;

        return $this->render('YarshaAdminBundle:employee:list.html.twig', $data);
    }


    /**
     * @Route("admin/jobseeker/search", name = "yarsha_admin_jobseeker_search")
     */
    public function searchJobseekerAction(Request $request)
    {
        $searchText = $request->get('searchText');
        $jobSeekerService = $this->get('yarsha.admin.service.jobseeker');
        $jobSeekers = $jobSeekerService->getPaginatedJobSeekersBySearchText($searchText);

        return $this->render('YarshaAdminBundle:employee:list.html.twig',
            ['jobSeekers' => $jobSeekers]);
    }

    /**
     * @Route("admin/jobseeker/{id}/profile", name="yarsha_admin_jobseeker_profile")
     */
    public function jobSeekerProfile($id)
    {
        $jobSeekerService = $this->get('yarsha.admin.service.jobseeker');
        $jobseeker = $jobSeekerService->getJobSeekerById($id);
        if (!$jobseeker) {
            return new Response('<h1>User with that id does not exist.</h1>');
        }

        return $this->render('YarshaAdminBundle:employee:profile.html.twig', ['jobseeker' => $jobseeker]);
    }

    /**
     * @Route("admin/jobseeker/{id}/appliedjobs", name="yarsha_admin_jobseekers_appliedjobs")
     */
    public function jobSeekerAppliedJobs(Request $request)
    {
        $filters = $request->query->all();
        $id = $request->get('id');
        $jobSeekerService = $this->get('yarsha.admin.service.jobseeker');
        $seeker = $jobSeekerService->getJobSeekerById($id);
        $appliedJobs = $jobSeekerService->getPaginatedAppliedJobs($id, $filters);
        $data['appliedJobs'] = $appliedJobs;

        $this->get('apy_breadcrumb_trail')->add($seeker->getFirstName() . ' ' . $seeker->getLastName(),
            'yarsha_admin_seeker_detail', ['id' => $seeker->getId()])
            ->add('Applied Jobs', 'yarsha_admin_jobseekers_appliedjobs', ['id' => $seeker->getId()])
            ->add('List');

        return $this->render('YarshaAdminBundle:employee:appliedjobs.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws NotFoundHttpException
     *
     * @Route("/admin/applicants/list", name="yarsha_admin_applicants_list")
     */
    public function listApplicantsAction(Request $request)
    {
        $filters = $request->query->all();

        $orgId = $request->get('organization');
        $jobId = $request->get('job');
        $breadcrumb = $this->get('apy_breadcrumb_trail');

        $organization = null;

        if ($orgId) {
            $data['organization'] = $organization = $this->get('yarsha.service.organization')->getOrganizationById($orgId);
            if (!$organization) {
                throw new NotFoundHttpException;
            }
            $breadcrumb->add('Employers', 'yarsha_admin_organization_list');
            $breadcrumb->add($organization->getName(), 'yarsha_admin_organization_detail', ['id' => $orgId]);
        }

        if ($jobId) {
            $data['job'] = $job = $this->get('yarsha.service.job')->getJobById($jobId);
            if (!$job) {
                throw new NotFoundHttpException;
            }

            ($organization)
                ? $breadcrumb->add("Jobs", 'yarsha_admin_organization_job_list', ['id' => $orgId])
                : $breadcrumb->add("Jobs", 'yarsha_admin_job_list');;

            $param['id'] = $jobId;
            if ($organization) {
                $param['ref'] = $orgId;
            }

            $breadcrumb->add(substr($job->getTitle(), 0, 10) . '...', 'yarsha_admin_job_detail', $param);
        }

        $breadcrumb->add('Applicants');

        $data['organization'] = $organization;

        $data['applicants'] = $this->get('yarsha.service.job_seeker')->getPaginatedApplicantsList($filters);

        return $this->render('YarshaAdminBundle:employee:applicants.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/applicant/delete", name="yarsha_admin_ajax_applicant_delete")
     */
    public function deleteApplicantAction(Request $request)
    {
        $Id = $request->get('employee');
        $em = $this->get('doctrine.orm.entity_manager');
        $applicant = $em->getRepository(EmployeeAppliedJob::class)->find($Id);
        if (!$applicant) {
            return new JsonResponse(['status' => 'error', 'message' => 'No applicant Found'], 400);
        }

        try {
            $em->remove($applicant);
            $em->flush();
            $this->addFlash('success', 'Applicant Deleted Successfully.');
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'success']);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/jobseeker/add", name="yarsha_admin_jobseeker_add")
     * @Route("/admin/jobseeker/{id}/edit", name="yarsha_admin_jobseeker_edit")
     */
    public function addJobseekerAction(Request $request)
    {
        $data['is_updating'] = false;
        $userManager = $this->get('yarsha_job_seeker.user_manager');
        $id = $request->get('id');
        if ($id) {
            $data['is_updating'] = true;
            $seeker = $this->get('yarsha.service.job_seeker')->getSeekerById($id);
        } else {

            $seeker = $userManager->createUser();
        }
        $form = $this->createForm(JobSeekerRegistrationType::class, $seeker,
            ['is_updating' => $data['is_updating'], 'admin' => true, 'registrationType' => true]);

        $form->handleRequest($request);

        $breadcrumb = $this->get('apy_breadcrumb_trail');
        $breadcrumb->add($data['is_updating'] ? $seeker->getFirstName() : 'New');

        if ($form->isSubmitted() and $form->isValid()) {
            try {

                $user = $form->getData();

                $profile_picture = $user->getFile();
                if ($profile_picture) {
                    $user->setFile($profile_picture);
                    $user->upload();
                }

                if ($data['is_updating'] == false) {
                    $user->setEnabled(true);
                    $user->setPlainPassword($user->getPassword());
                    $user->addRole('ROLE_JOB_SEEKER');
                    $username = $user->getUsername();
                    $email = (strpos($username, '@')) ? $username : $username . '@kantipurjob.com';
                    $user->setUsername($username);
                    $user->setEmail($email);
                }
                $userManager->updateUser($user);
                $this->get('doctrine.orm.entity_manager')->flush();

                $message = $data['is_updating'] ? 'Job Seeker successfully updated.' : 'Job Seeker successfully added.';
                $this->addFlash('success', $message);

                return $this->redirectToRoute('yarsha_admin_jobseeker_list');

            } catch
            (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }
        $data['form'] = $form->createView();

        return $this->render('YarshaAdminBundle:employee:addEmployee.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/seeker/delete", name="yarsha_admin_ajax_seeker_delete")
     */
    public function deleteSeekerAction(Request $request)
    {
        $seekerId = $request->get('seeker');
        $seeker = $this->get('yarsha.service.job_seeker')->getSeekerById($seekerId);
        if (!$seeker) {
            return new JsonResponse(['status' => 'error', 'message' => 'No Seeker Found'], 400);
        }

        try {
            $em = $this->get('doctrine.orm.entity_manager');
            $seeker->setDeleted(true);
            $seeker->setEnabled(false);
            $em->persist($seeker);
            $em->flush();
            $this->addFlash('success', 'Seeker Deleted Successfully.');
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'success']);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/seeker/{id}/detail", name="yarsha_admin_seeker_detail")
     */
    public function detailAction(Request $request)
    {
        $id = $request->get('id');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $seeker = $seekerService->getSeekerById($id);

        if (!$seeker) {
            throw new NotFoundHttpException;
        }

        $this->get('apy_breadcrumb_trail')->add($seeker->getFirstName());
        $data['seeker'] = $seeker;
        $userManagement = $this->get('yarsha_job_seeker.user_manager');
        $form = $this->getUploadResumeForm($seeker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $file = $form->get('curriculumVitaeFile')->getData();
                if ($file) {
                    $seeker->setCurriculumVitaeFile($file);
                    $seeker->uploadCv();
                    $userManagement->updateUser($seeker);
                    $this->get('doctrine.orm.entity_manager')->flush();
                }
                $this->addFlash('success', 'CV successfully uploaded.');

            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }
        $data['form'] = $form->createView();

        return $this->render('YarshaAdminBundle:employee:detail.html.twig', $data);
    }

    /**
     * @Route("admin/jobseeker/{id}/appliedcompany", name="yarsha_admin_jobseekers_appliedcompany")
     */
    public function jobSeekerAppliedCompany(Request $request)
    {
        $filters = $request->query->all();
        $id = $request->get('id');
        $jobSeekerService = $this->get('yarsha.admin.service.jobseeker');
        $seeker = $jobSeekerService->getJobSeekerById($id);
        $appliedcompanies = $jobSeekerService->getPaginatedAppliedCompanies($id, $filters);
        $data['appliedcompanies'] = $appliedcompanies;

        $this->get('apy_breadcrumb_trail')->add($seeker->getFirstName() . ' ' . $seeker->getLastName(),
            'yarsha_admin_seeker_detail', ['id' => $seeker->getId()])
            ->add('Applied Companies', 'yarsha_admin_jobseekers_appliedcompany', ['id' => $seeker->getId()])
            ->add('List');

        return $this->render('YarshaAdminBundle:employee:appliedcompany.html.twig', $data);
    }

    /**
     * @Route("admin/jobseeker/{id}/followedcompany", name="yarsha_admin_jobseekers_followedcompany")
     */
    public function jobSeekerFollowedCompany(Request $request)
    {
        $filters = $request->query->all();
        $id = $request->get('id');
        $jobSeekerService = $this->get('yarsha.admin.service.jobseeker');
        $seeker = $jobSeekerService->getJobSeekerById($id);
        $followedcompanies = $jobSeekerService->getPaginatedFollowedOrganization($id, $filters);
        $data['followedcompanies'] = $followedcompanies;

        $this->get('apy_breadcrumb_trail')->add($seeker->getFirstName() . ' ' . $seeker->getLastName(),
            'yarsha_admin_seeker_detail', ['id' => $seeker->getId()])
            ->add('Followed Companies', 'yarsha_admin_jobseekers_followedcompany', ['id' => $seeker->getId()])
            ->add('List');

        return $this->render('YarshaAdminBundle:employee:followedcompany.html.twig', $data);
    }

    /**
     *
     * @Route("admin/jobseeker/{id}/blacklist",name="yarsha_admin_jobseeker_blacklist")
     */
    public function blacklistAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $id = $request->get('id');
        $jobSeekerService = $this->get('yarsha.admin.service.jobseeker');
        $seeker = $jobSeekerService->getJobSeekerById($id);

        if ($seeker) {

            if ($seeker->isBlackListed() == true) {
                $seeker->setEnabled(true);
                $seeker->setBlacklisted(false);

            } else {
                $seeker->setEnabled(false);
                $seeker->setBlacklisted(true);

            }
            $em->persist($seeker);
            $em->flush();

            $this->addFlash('success',
                $seeker->isBlackListed() == false ? 'Employee successfully remove from blcklist.' : 'Employee successfully added to blcklist.');

            return $this->redirectToRoute('yarsha_admin_jobseeker_list');

        } else {
            $this->addFlash('error', 'Something wrong');

            return $this->redirectToRoute('yarsha_admin_jobseeker_list');
        }


    }

    public function getUploadResumeForm($seeker)
    {

        $form = $this->createFormBuilder($seeker)
            ->add('curriculumVitaeFile', FileType::class, [
                'data_class' => null,
                'required' => true
            ])
            ->getForm();

        return $form;
    }

    /**
     * @param $filename
     * @param Request $request
     * @Route("admin/{id}/seekercv/download", name="yarsha_admin_cv_download")
     */
    public function downloadCvAction(Request $request)
    {
        $id = $request->get('id');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $seeker = $seekerService->getSeekerById($id);
        $filename = $seeker->getCurriculumVitaePath();
        $path = __DIR__ . '/../../../../web/uploads/seekers/';
        $download_file = $path . $filename;
        if (file_exists($download_file)) {
            $extension = explode('.', $filename);
            $extension = $extension[count($extension) - 1];
            header('Content-Transfer-Encoding: binary');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
            header('Accept-Ranges: bytes');
            header('Content-Length: ' . filesize($download_file));
            header('Content-Encoding: none');
            header('Content-Type: application/' . $extension);
            header('Content-Disposition: attachment; filename=' . $filename);
            readfile($download_file);
            exit;
        } else {

            echo 'File does not exists on given path';
        }
        exit;
    }

    /**
     * @param Request $request
     * @Route("admin/{id}/seeker-cv/download", name="yarsha_admin_seeker_cv_download")
     */
    public function downloadedGeneratedResume(Request $request)
    {
        $id = $request->get('id');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $seeker = $seekerService->getSeekerById($id);
        if (!$seeker) {
            throw new NotFoundHttpException('Seeker does not exist.');
        }

        return $seekerService->downloadGeneratedResume($seeker);
    }


    /**
     * @param Request $request
     * @return Response
     * @Route("admin/trashed/seekers", name="yarsha_admin_trashed_seekers_list")
     */
    public function listTrashsedSeekersAction(Request $request)
    {
        $filters = $request->query->all();
        $trashedSeekers = $this->getDoctrine()->getManager()->getRepository(User::class)->getTrashedSeekers($filters);
        $data['trashedSeekers'] = $trashedSeekers;

        return $this->render('@YarshaAdmin/employee/trashedEmployees.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/restore/{id}/seeker", name="yarsha_admin_ajax_restore_job_seeker")
     */
    public function restoreTrashedUserAction(Request $request)
    {
        $data['success'] = false;
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $seeker = $em->getRepository(User::class)->find($id);
        if ($seeker) {
            $seeker->setDeleted(false);
            $em->persist($seeker);
            try {
                $em->flush();
                $data['message'] = "Employee successfully restored.";
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['errorMessage'] = "Something went wrong. Unable to restore employee.";
            }
        } else {
            $data['errorMessage'] = "Employee does not exist.";
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/delete/{id}/seeker/permanently", name="yarsha_admin_ajax_delete_employee_permenently")
     */
    public function deleteEmployeePermanently(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        $em = $this->get('doctrine.orm.default_entity_manager');
        $seeker = $em->getRepository(User::class)->find($id);
        if ($seeker) {
            try {
                $this->removeElements($seeker->getEducations());
                $this->removeElements($seeker->getExperiences());
                $this->removeElements($seeker->getTrainings());
                $this->removeElements($seeker->getLanguages());
                $this->removeElements($seeker->getReferences());

                $setting = $em->getRepository(JobSeekerSetting::class)->findOneBy(['jobSeeker' => $seeker]);
                if ($setting) {
                    $em->remove($setting);
                    $em->flush();
                    $em->clear();
                }

                $phoneCall = $em->getRepository(JobSeekerCallRecord::class)->findOneBy(['seeker' => $seeker]);
                if ($phoneCall) {
                    $em->remove($phoneCall);
                    $em->flush();
                    $em->clear();
                }


                $appliedJobs = $em->getRepository(EmployeeAppliedJob::class)->getLatestJobsAppliedbySeeker($seeker);
                $this->removeElements($appliedJobs);

                $savedJobs = $em->getRepository(EmployeeJobBasket::class)->findBy(['employee' => $seeker]);
                $this->removeElements($savedJobs);

                $testimonials = $em->getRepository(Testimonial::class)->findBy([
                    'userId' => $seeker->getId(),
                    'userType' => MainBundleConstants::USER_TYPE_EMPLOYEE
                ]);
                $this->removeElements($testimonials);

                $coverLetters = $em->getRepository(CoverLetter::class)->findBy(['user' => $seeker]);
                $this->removeElements($coverLetters);
                $em->getRepository(User::class)->removeProfileStatusForSeeker($seeker);
                $em->getRepository(User::class)->removeFollowedCompaniesBySeeker($seeker->getId());
                $detachedEntity = $em->merge($seeker);
                $em->remove($detachedEntity);
                $em->flush();

                $data['success'] = true;
                $data['message'] = "Employee deleted permanently.";
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }

        return new JsonResponse($data);
    }


    public function removeElements($collections)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        foreach ($collections as $collection) {
            $em->remove($collection);
        }
        $em->flush();
        $em->clear();
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/admin/employee/{id}/changepassword", name="yarsha_admin_change_employee_password")
     */
    function changeEmployeePassword(Request $request)
    {
        $id = $request->get('id');
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $form = $this->getPasswordResetForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();
            $user->setPlainPassword($userData['plainPassword']);
            $userManager = $this->get('yarsha_job_seeker.user_manager');
            try {
                $userManager->updateUser($user);
                $eventDispatcher = $this->get('event_dispatcher');
                $eventDispatcher->dispatch(YarshaJobSeekerEvents::USER_PASSWORD_CHANGED);
                $this->addFlash('success', 'Password Changed. User new password in next login.');
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }

            return $this->redirectToRoute('yarsha_admin_jobseeker_list');
        }
        $this->get('apy_breadcrumb_trail')->add($user->getFullName());
        $data['title'] = 'Employee';
        $data['form'] = $form->createView();

        return $this->render('@YarshaAdmin/employee/resetpassword.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/employer/{id}/resetpasswordlink/send", name="yarsha_admin_ajax_reset_password_link_send")
     */
    public function sendResetPasswordLink(Request $request)
    {
        $id = $request->get('id');
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $dispatcher = $this->get('event_dispatcher');
        $event = new GetResponseNullableUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if ($user != null) {
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            if (null === $user->getConfirmationToken() or $user->getConfirmationToken() == '') {
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $token = $tokenGenerator->generateToken();
                $user->setConfirmationToken($token);
            } else {
                $token = $user->getConfirmationToken();
            }
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $to = $user->getContactEmail();
            $fullname = $user->getfirstName();

            $message = \Swift_Message::newInstance()
                ->setSubject('Password Resetting')
                ->setFrom('noreply@kantipurjob.com', 'Kantipur Job')
                ->setTo($to)
                ->setBody(
                    $this->renderView(
                        'Emails/password_resetting.html.twig',
                        [
                            'name' => $fullname,
                            'token' => $token,
                            'message' => 'Reset Password'
                        ]
                    ),
                    'text/html'
                );
            try {
                $this->get('mailer')->send($message);
                $response['message'] = "Password reset link sent to user.";
            } catch (\Exception $e) {
                $response['error'] = $e->getMessage();
            }

            $user->setPasswordRequestedAt(new \DateTime());
            $this->get('yarsha_job_seeker.user_manager')->updateUser($user);
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);
            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        } else {
            $response['error'] = 'Invalid request. User does not exist.';
        }

        return new JsonResponse($response);
    }

    public function getPasswordResetForm()
    {
        $form = $this->createFormBuilder()
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password and confirm password field must match.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field',
                        'pattern' => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}',
                        'title' => "Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                    ]
                ],
                'required' => true,
                'first_options' => ['label' => 'New Password'],
                'second_options' => ['label' => 'Repeat Password']
            ])
            ->add('change', SubmitType::class, [
                'attr' => [
                    'value' => 'Change',
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();

        return $form;
    }


    /**
     * @param Request $request
     * @Route("/admin/add/{seekerId}/callrecord", name="yarsha_admin_seeker_call_record_add")
     */
    public function addCallRecords(Request $request)
    {
        $data['is_updating'] = false;
        $em = $this->get('doctrine.orm.entity_manager');
        $adminId = $this->getUser()->getId();
        $seekerId = $request->get('seekerId');
        if ($seekerId) {
            $seeker_call = $em->getRepository(JobSeekerCallRecord::class)->findOneBy([
                'seeker' => $seekerId
            ]);
        } else {
            throw new NotFoundHttpException();
        }
        $data['id'] = $seekerId;

        if ($seeker_call) {
            $data['is_updating'] = true;
            $callRecord = $seeker_call;
        } else {
            $callRecord = new JobSeekerCallRecord();
        }

        $message = $data['is_updating'] ? 'Seeker phone call successfully updated.' : 'Seeker phone call successfully added.';
        $form = $this->createForm(SeekerCallRecordType::class, $callRecord);

        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $callRecord = $form->getData();
                $callRecord->setAdminId($adminId);
                $em->persist($callRecord);
                try {
                    $em->flush();
                    $this->addFlash('success', $message);
                    $response['success'] = true;
                    $response['error'] = false;
                } catch (\Exception $e) {
                    $response['success'] = false;
                    $data['errorMessage'] = "Something went wrong. Please check values and submit.";
                }
            } else {
                $response['success'] = false;
            }

            return new JsonResponse($response);
        }

        $data['form'] = $form->createView();

        $response['template'] = $this->renderView('@YarshaAdmin/employee/add_call_record.html.twig', $data);

        return new JsonResponse($response);

    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/jobseeker/{id}/information", name="yarsha_admin_jobseeker_other_information")
     */
    public function otherInformationAction(Request $request)
    {
        $id = $request->get('id');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $seeker = $seekerService->getSeekerById($id);

        if (!$seeker) {
            throw new NotFoundHttpException;
        }

        $this->get('apy_breadcrumb_trail')->add($seeker->getFullName());

        $data['seeker'] = $seeker;

        return $this->render('YarshaAdminBundle:employee:otherInformation.html.twig', $data);
    }


}
