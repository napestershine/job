<?php

namespace Yarsha\JobSeekerBundle\Controller;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;
use Yarsha\JobSeekerBundle\Entity\EmployeeJobBasket;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Yarsha\JobSeekerBundle\Event\ProfileUpdateEvent;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yarsha\MainBundle\Event\JobSeekerJobsEmailEvent;
use Yarsha\MainBundle\MainBundleConstants;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\JobSeekerBundle\Entity\User as Seeker;
use Symfony\Component\HttpFoundation\File\Uploade;


/**
 * Class JobManagementController
 * @package Yarsha\JobSeekerBundle\Controller
 *
 * @Route("seeker")
 */
class JobManagementController extends Controller
{

    private $data = [];

    public function resumeAction(Request $request)
    {
        $form = $this->getUploadResumeForm();
        $form->handleRequest($request);
        $this->uploadCvAction($form);
    }

    /**
     * @param Request $request
     * @Route("/cv/upload", name="yarsha_job_seeker_upload_cv")
     */
    public function uploadCvAction($form)
    {
        $user = $this->getUser();
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $file = $form->get('curriculumVitaeFile')->getData();
                $user->setCurriculumVitaeFile($file);
                $user->uploadCv();
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                try {
                    $em->flush();
                    $eventDispatcher = $this->get('event_dispatcher');
                    $profileUpdateEvent = new ProfileUpdateEvent($user);
                    $eventDispatcher->dispatch(MainBundleEvents::EVENT_JOB_SEEKER_PROFILE_UPDATE, $profileUpdateEvent);
                    $this->addFlash('success', 'Curriculum vitae uploaded.');
                } catch (\Exception $e) {
                    $this->addFlash('errorMessage',
                        "Please upload a valid file. only (pdf / word document) file are supported.");
                }
            } else {
                $this->addFlash('errorMessage',
                    "Please upload a valid file. only (pdf / word document) file are supported.");
            }
        }
    }

    public function getUploadResumeForm()
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('curriculumVitaeFile', FileType::class, [
                'data_class' => null,
                'required' => true,
                'label' => 'Upload Curriculum Vitae'
            ])
            ->getForm();

        return $form;
    }

    /**
     * @Route("/appliedjob/list", name="yarsha_job_seeker_job_list")
     * @Breadcrumb("applied jobs", routeName="yarsha_job_seeker_job_list")
     * @Breadcrumb("List")
     */
    public function listJobsAction(Request $request)
    {
        $this->resumeAction($request);
        $jobSeekerService = $this->get('yarsha.service.job_seeker');
        $this->data['jobs'] = $jobSeekerService->getJobsAppliedBySeeker($this->getUser());

        return $this->render('YarshaJobSeekerBundle:Job:list.html.twig', $this->data);
    }

    /**
     * @Route("/{slug}/apply/email", name="yarsha_job_seeker_job_apply_email")
     */
    public function applyByEmailAction($slug, Request $request)
    {
        $applyType = EmployeeAppliedJob::JOB_APPLIED_TYPE_EMAIL;
        $status = EmployeeAppliedJob::JOB_APPLIED_STATUS_PENDING;
        try {
            $this->applyForJobAction($slug, $applyType, $status, $request);
        } catch (\Exception $e) {
            $this->addFlash('errorMessage', $e->getMessage());
        }

        return $this->redirectToRoute('yarsha_job_seeker_job_list');
    }

    /**
     * @Route("/{slug}/apply/online", name="yarsha_job_seeker_job_apply_online")
     */
    public function applyOnlineAction($slug, Request $request)
    {
        $applyType = EmployeeAppliedJob::JOB_APPLIED_TYPE_ONLINE;
        $status = EmployeeAppliedJob::JOB_APPLIED_STATUS_PENDING;
        try {
            $jobService = $this->get('yarsha.service.job');
            $job = $jobService->getJobBySlug($slug);
            if ($job) {
                $response['external_link'] = $job->getOnlineLink();
            }
            $response['hasApplied'] = $jobService->checkJobApplied($this->getUser(), $job) ? true : false;
            $message = $this->applyForJobAction($slug, $applyType, $status, $request);
            $response['message'] = $message;
        } catch (\Exception $e) {
            die("Something went wrong. <br>Error: " . $e->getMessage());
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/ajax/{slug}/apply/email", name="yarsha_job_seeker_ajax_job_apply_email")
     */
    public function applyEmailAction($slug, Request $request)
    {
        $baseurl = realpath($this->getParameter('kernel.root_dir') . '/../web/uploads/seekers');
        $user = $this->getUser();
        $response['success'] = false;
        $mailer = $this->get('yarsha.service.mailer');
        $applyType = EmployeeAppliedJob::JOB_APPLIED_TYPE_EMAIL;
        $status = EmployeeAppliedJob::JOB_APPLIED_STATUS_PENDING;
        $form = $this->generateApplyEmailForm();
        $form->handleRequest($request);
        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobBySlug($slug);
        $contactPersons = $job->getOrganization()->getContactPersons();
        $contactEmail = $contactPersons[0]->getEmail();
        if ($job) {
            if ($form->isSubmitted() && $form->isValid()) {
                $userdata = $form->getData();
                $imageFile = $form->get('image')->getData();
                $cvFile = $form->get('cv')->getData();

                if ($user) {
                    $userId = $this->getUser()->getId();
                    $seekerService = $this->get('yarsha.service.job_seeker');
                    $seeker = $seekerService->getSeekerById($userId);
                    $profile = $seeker->getPath() ? "/uploads/seekers/" . $seeker->getPath() : "bundles/yarshaadmin/images/user.png";
                } else {

                    $upload_dir = $baseurl;
                    if ($imageFile) {
                        // Generate a unique name for the file before saving it
                        $fileName = md5(uniqid()) . '.' . $imageFile->guessExtension();

                        // Move the file to the directory where brochures are stored
                        $imageFile->move($upload_dir, $fileName);
                        $profile = "/uploads/seekers/" . $fileName;
                    } else {
                        $profile = "";
                    }
                }

                $subject = 'Job Application for ' . $job->getTitle();
                $senderName = $form->get('senderName')->getData();
                $phone = $userdata['phone'];
                $from = $userdata['senderEmail'];
                $messageBody = $userdata['body'];
                $address = $userdata['currentAddress'];
                $body = $this->renderView('@YarshaJobSeeker/Email/applybyemail.html.twig', [
                    'senderName' => $senderName,
                    'senderEmail' => $from,
                    'phone' => $phone,
                    'job' => $job,
                    'message' => $messageBody,
                    'profile' => $profile,
                    'address' => $address
                ]);
                $to = $contactEmail;
                $toAdmin = 'emailapply@kantipurjob.com';

                if ($user) {
                    $resumeFile = $this->generateTemporaryResume();
                    $attachment = [
                        'file' => $resumeFile,
                        'filename' => 'JobSeekerCV.pdf'
                    ];
                } else {

                    if ($cvFile) {
                        // Generate a unique name for the file before saving it
                        $cvName = md5(uniqid()) . '.' . $cvFile->guessExtension();
                        $ext = 'JobSeekerCV.' . $cvFile->guessExtension();

                        // Move the file to the directory where brochures are stored
                        $cvFile->move($upload_dir, $cvName);

                        $attachment = [
                            'file' => $upload_dir . '/' . $cvName,
                            'filename' => $ext
                        ];

                    } else {
                        $attachment = [];
                    }
                }

                try {
                    if ($mailer->sendEmail($subject, $body, $to, $from, $senderName, $attachment)) {
                        $mailer->sendEmail($subject, $body, $toAdmin, $from, $senderName, $attachment);
                    }

                    if ($user) {
                        $response['hasApplied'] = $jobService->checkJobApplied($this->getUser(), $job) ? true : false;
                        $message = $this->applyForJobAction($slug, $applyType, $status, $request);
                    } else {
                        if ($imageFile) {
                            unlink($upload_dir . '/' . $fileName);
                        }
                        if ($cvFile) {
                            unlink($upload_dir . '/' . $cvName);
                        }

                        $message = 'You have successfully applied for this Job.';
                    }
                    $response['message'] = $message;
                    $response['success'] = true;
                } catch (\Exception $e) {
                    $response['errorMessage'] = "Something went wrong. <br>Error: " . $e->getMessage();
                }
            }
        }


        $response['template'] = $this->renderView('@YarshaJobSeeker/Job/jobapplyemailform.html.twig',
            ['form' => $form->createView()]);

        return new JsonResponse($response);
    }


//    public function generateApplyEmailForm(){
//        $user = $this->getUser();
//        $form = $this->createFormBuilder($user)
//            ->add('senderName', TextType::class, [])
//            ->add('senderEmail', TextType::class, [])
//            ->add('body', TextType::class, [])
//            ->getForm();
//        return $form;
//    }

    public function generateApplyEmailForm()
    {
        $user = $this->getUser();

        if ($user) {
            $value = false;
        } else {
            $value = true;
        }

        $form = $this->createFormBuilder()
            ->add('senderName', TextType::class, [
                'data_class' => null,
                'required' => true,
                'label' => "Your Name"
            ])
            ->add('senderEmail', EmailType::class, [
                'required' => true,
                'label' => 'Your Email',
                'attr' => [
                    'id' => 'jobSeekerApplyEmailField'
                ]
            ])
            ->add('phone', TextType::class, [
                'data_class' => null,
                'required' => true,
                'label' => 'Mobile no'
            ])
            ->add('currentAddress', TextType::class, [
                'required' => true,
                'label' => 'Current Address'
            ])
            ->add('image', FileType::class, [
                'label' => 'Profile Picture',
                'required' => false,
            ])
            ->add('cv', FileType::class, [
                'label' => 'Curriculum vitae file (pdf / word document):',
                'required' => $value,

            ])
            ->add('body', CKEditorType::class, [
                'required' => false,
                'config_name' => 'simple_editor',
                'label' => 'Cover Letter',
                'attr' => [
                    'rows' => 15
                ]
            ])
            ->getForm();

        return $form;
    }

    /**
     * @param $slug
     * @return JsonResponse
     * @Route("/{slug}/jobappliedevent/trigger", name="yarsha_job_seeker_trigger_job_applied_event")
     */
    public function triggerJobAppliedEvent($slug)
    {
        $response['success'] = false;
        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobBySlug($slug);

        if (!$job) {
            $response['message'] = "Job does not exist.";
        } else {
            $eventDispatcher = $this->get('event_dispatcher');
            $event = new JobSeekerJobsEmailEvent($this->getUser(), $job);
            try {
                $eventDispatcher->dispatch(MainBundleEvents::EMAIL_EVENT_JOB_SEEKER_JOB_APPLIED, $event);
                $response['success'] = true;
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }

        }

        return new JsonResponse($response);
    }


    public function applyForJobAction($slug, $type, $status, Request $request)
    {
        $jobSeekerService = $this->get('yarsha.service.job_seeker');
        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobBySlug($slug);
        $applied_job_link = $this->generateUrl('yarsha_job_seeker_job_list');

        if (!$job) {
            throw new NotFoundHttpException("Sorry the job you are applying for could not be found.");
        }
        $seeker = $this->getUser();
        $alreadyApplied = $jobSeekerService->checkAlreadyApplied($seeker, $job);
        if ($alreadyApplied) {
            $message = 'You have already applied for this job. <a target="_blank"href="' . $applied_job_link . '">Check Applied Job</a>';
        } else {
            $jobSeekerService->saveJobApplied($seeker, $job, $type, $status);
//            if ($type == EmployeeAppliedJob::JOB_APPLIED_TYPE_EMAIL) {
//                $mailer = $this->get('mailer');
//                $message = \Swift_Message::newInstance()
//                    ->setDescription('Job application of From KantipurJobs by ' . $this->getUser()->getFirstName())
//                    ->setFrom([
//                        $this->getUser()->getEmail() => $this->getUser()->getFirstName()
//                    ])
//                    ->setSubject('Job Application')
//                    ->setTo($this->getParameter('mailer_user'))
//                    ->setBody('The user ' . $this->getUser()->getFirstName() . ' applied for the job.');
//
//                $mailer->send($message);
//            }

            $message = 'You have successfully applied for this Job, Please check your email or view your dashboard "Jobs I have applied" for the confirmation. Thank you <a target="_blank"href="' . $applied_job_link . '">Check Applied Job</a>';
        }

        return $message;

    }

    /**
     * @Route("/{slug}/cancel", name="yarsha_job_seeker_cancel_job_apply")
     */
    public function cancelJobApplyAction($slug, Request $request)
    {
        $jobSeekerService = $this->get('yarsha.service.job_seeker');
        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobBySlug($slug);
        $seeker = $this->getUser();
        $jobSeekerService->cancelJobApplication($job, $seeker);
        $this->addFlash('messages', 'Job application cancelled . ');

        return $this->redirectBack($request);
    }

    /**
     * @Route("/{slug}/basket/add", name="yarsha_job_seeker_add_to_job_basket")
     */
    public function addToJobBasketAction($slug, Request $request)
    {
        $jobSeekerService = $this->get('yarsha.service.job_seeker');
        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobBySlug($slug);
        $seeker = $this->getUser();
        $alreadyAdded = $jobSeekerService->checkAlreadyAddedToBasket($job, $seeker);
        if ($alreadyAdded) {
            $this->addFlash('errors', 'Job already added to basket . ');
        } else {
            $jobSeekerService->addToJobBasket($job, $seeker);
            $this->addFlash('messages', 'Job added to basket . ');
        }

        return $this->redirectToRoute('yarsha_job_seeker_job_basket_list');
    }

    /**
     * @Route("/jobbasket/list", name="yarsha_job_seeker_job_basket_list")
     * @Breadcrumb("Job Basket", routeName="yarsha_job_seeker_job_basket_list")
     * @Breadcrumb("List")
     */
    public function listJobBasketAction(Request $request)
    {
        $this->resumeAction($request);
        $repo = $this->getDoctrine()->getRepository(EmployeeJobBasket::class);
        $this->data['message'] = "";
        $this->data['jobsBucket'] = $repo->findBy(['employee' => $this->getUser()]);

        return $this->render('YarshaJobSeekerBundle:JobBasket:list.html.twig', $this->data);
    }

    /**
     * @Route("/jobbasket/{id}/remove", name="yarsha_job_seeker_remove_job_from_job_bucket")
     */
    public function removeJobFromJobBasketAction($id, Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(EmployeeJobBasket::class);
        $this->data['message'] = "";
        $jobSeekerService = $this->get('yarsha.service.job_seeker');
        $jb = $jobSeekerService->getJobBasketJobById($id);
        try {
            $jobSeekerService->removeJobFromBasket($jb);
            $this->addFlash('messages', 'Job removed from basket.');
        } catch (\Exception $e) {
            $this->addFlash('errors', 'Unable to remove job from basket.');
        }

        return $this->redirectToRoute('yarsha_job_seeker_job_basket_list');
    }

    public function redirectBack(Request $request)
    {
        $referer = $request->headers->get('referer');

        return new RedirectResponse($referer);
    }

    public function generateTemporaryResume()
    {
        $user = $this->getUser();
        $jobseekerService = $this->get('yarsha.service.job_seeker');
        $jobseeker = $jobseekerService->getSeekerById($user);
        $educations = $jobseeker->getEducations();

        $data['detail'] = $jobseeker;
        $data['educations'] = $educations;

        $html = $this->renderView('YarshaJobSeekerBundle:pdf:resume_format.html.twig', $data);
        $filename = $jobseeker->getFirstName() . '-' . date('Ymd');

        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('no-outline', true);
        $snappy->setOption('page-size', 'LETTER');
        $snappy->setOption('encoding', 'UTF-8');
        $html = $snappy->getOutputFromHtml($html);
        putenv('TMPDIR=' . ini_get('open_basedir') . '/tmp');
        $file = getenv('TMPDIR') . '/' . $filename . '.pdf';
        file_put_contents($file, $html);

        return $file;
    }


}
