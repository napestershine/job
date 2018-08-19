<?php

namespace Yarsha\JobSeekerBundle\Controller;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Yarsha\ArticleBundle\Entity\Article;
use Yarsha\ArticleBundle\Entity\Notice;
use Yarsha\ArticleBundle\Entity\Testimonial;
use Yarsha\ArticleBundle\Form\TestimonialType;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobSeekerBundle\Entity\CoverLetter;
use Yarsha\JobSeekerBundle\Entity\JobSeekerEducation;
use Yarsha\JobSeekerBundle\Entity\JobSeekerExperience;
use Yarsha\JobSeekerBundle\Entity\JobSeekerLanguage;
use Yarsha\JobSeekerBundle\Entity\JobSeekerReference;
use Yarsha\JobSeekerBundle\Entity\JobSeekerSetting;
use Yarsha\JobSeekerBundle\Entity\JobSeekerTraining;
use Yarsha\JobSeekerBundle\Form\CoverLetterType;
use Yarsha\JobSeekerBundle\Form\JobSeekerEducationType;
use Yarsha\JobSeekerBundle\Form\JobSeekerProfileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Yarsha\JobSeekerBundle\Form\JobSeekerSettingType;
use Symfony\Component\HttpFoundation\Response;
use Yarsha\MainBundle\MainBundleConstants;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\JobSeekerBundle\Event\ProfileUpdateEvent;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;


/**
 * Class HomeController
 * @package Yarsha\JobSeekerBundle\Controller
 * @Route("/seeker")
 */
class HomeController extends Controller
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="yarsha_job_seeker_homepage")
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_JOB_SEEKER');
        $settings = $this->getDoctrine()->getRepository(JobSeekerSetting::class)->findOneBy([
            'jobSeeker' => $this->getUser()
        ]);

        if ($settings) {
            $status = "Updated";
            $this->data['settings'] = $settings;
        } else {
            $this->data['settings'] = new JobSeekerSetting();
            $status = "Added";
        }

        $form = $this->createForm(JobSeekerSettingType::class, $this->data['settings']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userSetting = $form->getData();
            $userSetting->setJobSeeker($this->getUser());
            $this->getDoctrine()->getManager()->persist($userSetting);
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', "Setting {$status}.");
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        }

        $this->data['form'] = $form->createView();
        $this->resumeAction($request);
        $this->data['message'] = $request->get('message');
        $this->data['jobs'] = $this->getDoctrine()->getManager()->getRepository(Job::class)->getJobs(date('Y-m-d'));

        return $this->render('YarshaJobSeekerBundle:Home:index.html.twig', $this->data);
    }

    /**
     * @Route("/update-profile", name="yarsha_job_seeker_update_profile")
     * @Breadcrumb("Update Profile")
     */
    public function updateProfile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(JobSeekerProfileType::class, $user);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $profileData = $form->getData();
            $em->persist($profileData);
            try {
                $em->flush();
                $eventDispatcher = $this->get('event_dispatcher');
                $profileUpdateEvent = new ProfileUpdateEvent($user);
                $eventDispatcher->dispatch(MainBundleEvents::EVENT_JOB_SEEKER_PROFILE_UPDATE, $profileUpdateEvent);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

        }

        $this->resumeAction($request);

        $this->data['form'] = $form->createView();
        $this->data['user'] = $user;

        return $this->render('YarshaJobSeekerBundle:Home:updateProfile.html.twig', $this->data);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/view/{section}", name="yarsha_job_seeker_profile_detail_view", defaults={"section"="personal_information"})
     * @Breadcrumb("Profile")
     */
    public function viewProfileAction(Request $request)
    {

        $settings = $this->getDoctrine()->getRepository(JobSeekerSetting::class)->findOneBy([
            'jobSeeker' => $this->getUser()
        ]);
        if ($settings) {
            $status = "Updated";
            $this->data['settings'] = $settings;
        } else {
            $this->data['settings'] = new JobSeekerSetting();
            $status = "Added";
        }

        $form = $this->createForm(JobSeekerSettingType::class, $this->data['settings']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userSetting = $form->getData();
            $userSetting->setJobSeeker($this->getUser());
            $this->getDoctrine()->getManager()->persist($userSetting);
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', "Setting {$status}.");
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        }

        $this->data['form'] = $form->createView();


        $this->resumeAction($request);
        $this->data['seeker'] = $this->getUser();
        $this->data['profileSection'] = str_replace('-', '_', $request->get('section'));

        return $this->render('YarshaJobSeekerBundle:Details:profile.html.twig', $this->data);
    }

    /**
     * @param $filename
     * @param Request $request
     * @Route("/cv/download", name="yarsha_job_seeker_cv_download")
     */
    public function downloadCvAction()
    {
        $user = $this->getUser();
        $filename = $user->getCurriculumVitaePath();
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
     * @param Request $rquest
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/setting/list", name="yarsha_job_seeker_setting_list")
     * @Breadcrumb("Setting")
     */
    public function settingListAction(Request $request)
    {
        $this->resumeAction($request);

        $settings = $this->getDoctrine()->getRepository(JobSeekerSetting::class)->findOneBy([
            'jobSeeker' => $this->getUser()
        ]);
        if ($settings) {
            $status = "Updated";
            $this->data['settings'] = $settings;
        } else {
            $this->data['settings'] = new JobSeekerSetting();
            $status = "Added";
        }

        $form = $this->createForm(JobSeekerSettingType::class, $settings);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userSetting = $form->getData();
            $userSetting->setJobSeeker($this->getUser());
            $this->getDoctrine()->getManager()->persist($userSetting);
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', "Setting {$status}.");
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        }

        $this->data['form'] = $form->createView();

        return $this->render('YarshaJobSeekerBundle:Details:settingList.html.twig', $this->data);
    }

    private function getDetailForm($formType, $name)
    {
        $form = $this->createFormBuilder()
            ->add($name, CollectionType::class, [
                'prototype' => true,
                'allow_add' => true,
                'entry_type' => $formType,
                'allow_delete' => true
            ])->getForm();

        return $form;
    }

    private function saveDetailForm($formData, $name)
    {
        try {
            $em = $this->get('doctrine.orm.entity_manager');
            foreach ($formData[$name] as $detail) {
                $em->persist($detail);
                $em->flush();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/add/setting", name="yarsha_job_seeker_add_setting")
     */
    public function settingAction(Request $request)
    {
        $this->resumeAction($request);
        $form = $this->getDetailForm(JobSeekerSettingType::class, 'setting');
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $formData = $form->getData();
            $this->saveDetailForm($formData, 'setting');

            return $this->redirectToRoute('yarsha_job_seeker_homepage');

        }

        $this->data['form'] = $form->createView();

        return $this->render('YarshaJobSeekerBundle:Details:setting.html.twig', $this->data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/generate/resume", name="yarsha_job_seeker_generate_resume")
     */
    public function PdfGenerateAction(Request $request)
    {
        $userId = $this->getUser();
        $jobseekerService = $this->get('yarsha.service.job_seeker');

        try {
            return $jobseekerService->downloadGeneratedResume($userId);
        } catch (\Exception $e) {
            $this->addFlash('errors', 'Unable to download resume right now. plz try again.');
        }

    }

    /**
     * @return Response
     * @Route("/generate/resume/view",name="yarsha_job_seeker_generate_view")
     */
    public function generateResumeView()
    {
        $userId = $this->getUser();
        $jobseekerService = $this->get('yarsha.service.job_seeker');
        $jobseeker = $jobseekerService->getSeekerById($userId);
        $educations = $jobseeker->getEducations();
        $data['jobseeker'] = $jobseeker;
        $data['educations'] = $educations;

        return $this->render('@YarshaJobSeeker/pdf/resume_format.html.twig', ['detail' => $this->getUser()]);


    }


    /**
     * @return Response
     * @Route("/notices", name="yarsha_job_seeker_notice_list")
     * @Breadcrumb("Notice",routeName="yarsha_job_seeker_notice_list")
     * @Breadcrumb("List")
     */
    public function viewNoticeListAction(Request $request)
    {
        $this->resumeAction($request);
        $userId = $this->getUser()->getId();
        $noticeService = $this->get('yarsha.service.notice');
        $this->data['notices'] = $noticeService->getPaginatedNoticesForSeeker($userId);

        return $this->render('YarshaJobSeekerBundle:notices:list.html.twig', $this->data);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/notice/{id}/detail", name="yarsha_job_seeker_notice_detail")
     * @Breadcrumb("Notice",routeName="yarsha_job_seeker_notice_list")
     */
    public function viewNoticeAction($id, Request $request)
    {
        $this->resumeAction($request);

        $noticeService = $this->get('yarsha.service.notice');
        $notice = $noticeService->getNoticeById($id);
        $this->data['notice'] = $notice;
        $this->get('apy_breadcrumb_trail')->add($notice->getTitle());

        return $this->render('YarshaJobSeekerBundle:notices:single.html.twig', $this->data);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/coverletter/add", name="yarsha_job_seeker_add_cover_letter")
     * @Route("/coverletter/{id}/edit", name="yarsha_job_seeker_update_cover_letter")
     * @Breadcrumb("Cover Letter",routeName="yarsha_job_seeker_list_cover_letter")
     */
    public function addCoverLetterAction(Request $request)
    {
        $this->data['updating'] = false;
        $this->resumeAction($request);
        $id = $request->get('id');
        if ($id) {
            $this->data['updating'] = true;
            $coverLetter = $this->getDoctrine()->getManager()->getRepository(CoverLetter::class)->find($id);
            if (!$coverLetter) {
                throw new NotFoundHttpException();
            }
            $this->get('apy_breadcrumb_trail')->add($coverLetter->getTitle());
        } else {
            $coverLetter = new CoverLetter();
            $this->get('apy_breadcrumb_trail')->add("New");
        }
        $form = $this->createForm(CoverLetterType::class, $coverLetter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $coverLetter = $form->getData();
            $default = $coverLetter->isDefault();
            if ($default) {
                $coverLetters = $em->getRepository(CoverLetter::class)->findBy([
                    'user' => $this->getUser()
                ]);
                foreach ($coverLetters as $c) {
                    $c->setDefault(false);
                }
                $coverLetter->setDefault(true);
            }
            $coverLetter->setUser($this->getUser());
            $em->persist($coverLetter);
            try {
                $em->flush();
                $status = $this->data['updating'] ? "updated" : "added";
                $this->addFlash('success', "Cover letter {$status}.");

                return $this->redirectToRoute('yarsha_job_seeker_list_cover_letter');
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        }
        $this->data['form'] = $form->createView();

        return $this->render('YarshaJobSeekerBundle:CoverLetter:add.html.twig', $this->data);

    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/coverletter/list", name="yarsha_job_seeker_list_cover_letter")
     * @Breadcrumb("Cover Letter",routeName="yarsha_job_seeker_list_cover_letter")
     * @Breadcrumb("list")
     */
    public function listCoverLetterAction(Request $request)
    {
        $this->resumeAction($request);
        $coverLetters = $this->getDoctrine()->getManager()->getRepository(CoverLetter::class)->findBy([
            'user' => $this->getUser()
        ]);
        $this->data['coverLetters'] = $coverLetters;

        return $this->render('YarshaJobSeekerBundle:CoverLetter:list.html.twig', $this->data);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/coverletter/{id}/delete", name="yarsha_job_seeker_delete_cover_letter")
     */
    public function deleteCoverLetterAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $coverLetter = $em->getRepository(CoverLetter::class)->findOneBy([
            'id' => $id,
            'user' => $this->getUser()
        ]);
        if (!$coverLetter) {
            throw new NotFoundHttpException();
        }
        $em->remove($coverLetter);
        try {
            $em->flush();
            $this->addFlash('success', "One cover letter deleted.");
        } catch (\Exception $e) {
            $this->addFlash('errorMessage', $e->getMessage());
        }

        return $this->redirectToRoute('yarsha_job_seeker_list_cover_letter');
    }

    /**
     * @param Request $request
     * @Route("/suggest/friend", name="yarsha_job_seeker_suggest_job_to_friend")
     */
    public function suggestToFriendAction(Request $request)
    {
        $data = [];
        $name = $request->get('name');
        $email = $request->get('email');
        $sender = $this->getUser()->getEmail() ? $this->getUser()->getEmail() : 'info@yarshastudio.com';
        $message = \Swift_Message::newInstance()
            ->setSubject('Suggestion for job from ' . $name)
            ->setFrom($sender)
            ->setTo($email)
            ->setBody($request->get('message'));
        $mailer = $this->get('mailer');
        try {
            $mailer->send($message);
            $data['success'] = "Mail successfully sent to your friend.";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $this->redirectToRoute('yarsha_job_seeker_homepage');

    }

    /**
     * @param Request $request
     * @Route("/ajax/suggest/friend", name="yarsha_ajax_job_seeker_suggest_job_to_friend")
     */
    public function ajaxSuggestToFriendAction(Request $request)
    {
        $data = [];
        $name = $request->get('name');
        $email = $request->get('email');
        $senderMessage = $request->get('message');
        $sender = $this->getUser()->getContactEmail() ? $this->getUser()->getContactEmail() : 'info@kantipurjob.com';
        $data['message'] = "<p>Your friend {$name} has suggested you a job. 
                            To view the job <a href='{$senderMessage}'> Click Here</a> or click the link below.</p>"
            . $senderMessage;
        $subject = 'Suggestion for job from ' . $name;
        $body = $this->renderView('@YarshaMain/EmailTemplates/common_messages.html.twig', $data);
//        $message = \Swift_Message::newInstance()
//            ->setSubject('Suggestion for job from ' . $name)
//            ->setFrom($sender)
//            ->setTo($email)
//            ->setBody($body, 'text/html');
//        $mailer = $this->get('mailer');
        $mailer = $this->get('yarsha.service.mailer');
        try {
            $mailer->sendEmail($subject, $body, $email);
            $data['success'] = true;
            $data['message'] = "Job Suggestion mail successfully sent to your friend.";
        } catch (\Exception $e) {
            $data['success'] = false;
//            $data['message'] = $e->getMessage();
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/story/add", name="yarsha_job_seeker_add_story")
     * @Route("/story/{id}/update", name="yarsha_job_seeker_update_story")
     */
    public function postStoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $this->data['isUpdating'] = false;
        if ($id) {
            $this->data['isUpdating'] = true;
            $story = $em->getRepository(Testimonial::class)->find($id);
            if (!$story) {
                throw new NotFoundHttpException('Testimonial does not exist');
            }
        } else {
            $story = new Testimonial();
            $story->setUserId($this->getUser()->getId());
            $story->setUserType(MainBundleConstants::USER_TYPE_EMPLOYEE);
        }
        $form = $this->createForm(TestimonialType::class, $story, ['seeker' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userStory = $form->getData();
            $userStory->upload();
            $em->persist($userStory);
            try {
                $em->flush();
            } catch (Exception $e) {
                $this->data['errorMessage'] = $e->getMessage();
            }

            return $this->redirectToRoute('yarsha_job_seeker_list_story');
        }
        $this->resumeAction($request);
        $this->data['form'] = $form->createView();

        return $this->render('@YarshaJobSeeker/story/addstory.html.twig', $this->data);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/stories/list", name="yarsha_job_seeker_list_story")
     */
    public function listSeekerStoryAction(Request $request)
    {
        $this->resumeAction($request);
        $em = $this->getDoctrine()->getManager();
        $this->data['stories'] = $em->getRepository(Testimonial::class)->findBy([
            'isDeleted' => false,
            'userId' => $this->getUser()->getId(),
            'userType' => MainBundleConstants::USER_TYPE_EMPLOYEE
        ]);

        return $this->render('@YarshaJobSeeker/story/liststories.html.twig', $this->data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/story/{id}/delete", name="yarsha_job_seeker_delete_story")
     */
    public function deleteStoryAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        if (!$id) {
            return $this->redirectToRoute('yarsha_job_seeker_list_story');
        } else {
            $story = $em->getRepository(Testimonial::class)->find($id);
            if (!$story) {
                throw new NotFoundHttpException('Story does not exist.');
            } else {
                $story->setIsDeleted(true);
                $em->persist($story);
                try {
                    $em->flush();
                    $data['success'] = "One Story deleted.";
                } catch (Exception $e) {
                    $data['errorMessage'] = $e->getMessage();
                }
            }
        }

        return $this->redirectToRoute('yarsha_job_seeker_list_story', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/email/resume", name="yarsha_job_seeker_email_resume")
     */
    public function emailResumeAction(Request $request)
    {
        $form = $this->getEmailResumeForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
//            $resumeFile = $user->getUploadRootDir().'/'.$user->getCurriculumVitaePath();
//            if(file_exists($resumeFile) and is_file($resumeFile)){
//                $resumeFile = $resumeFile;
//            }   else    {
//                $resumeFile = $this->generateTemporaryResume();
//            }
            $formData = $form->getData();
            try {
                $mailer = $this->get('swiftmailer.mailer');
                $message = \Swift_Message::newInstance();
                $message->setSubject("Job Seeker Resume");
                $message->setSender($user->getContactEmail());
                $message->setFrom($user->getContactEmail());
                $message->setTo($formData['email']);
                $resumeFile = $this->generateTemporaryResume();
                $message->attach(
                    \Swift_Attachment::fromPath($resumeFile)->setFilename($user->getCurriculumVitaePath())
                );
                $message->setBody($formData['message']);
                $message->setContentType('text/html');
                $mailer->send($message);
                $this->addFlash('success', "Resume mailed.");
                $form = $this->getEmailResumeForm();
            } catch (Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        } else {
            $this->resumeAction($request);
        }
        $this->data['form'] = $form->createView();

        return $this->render('@YarshaJobSeeker/Resume/emailresume.html.twig', $this->data);
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

    public function getEmailResumeForm()
    {
        $builder = $this->createFormBuilder();
        $form = $builder->add('email', TextType::class, [
            'label' => 'Email Address',
            'attr' => [
                'class' => 'form form-control'
            ]
        ])
            ->add('message', CKEditorType::class, [
                'label' => 'Message',
                'attr' => [
                    'class' => 'form form-control'
                ],
                'config_name' => 'simple_editor'
            ])->getForm();

        return $form;
    }

    /**
     * @Route("/testemail", name="yarsha_job_seeker_test_email")
     */
    public function sendTestEmail()
    {
        $data['seeker'] = $this->getUser();
//        $data['profileStatus'] = $this->get('yarsha.service.jobseeker_profile')->getJobSeekerProfileStatus($data['seeker']);
//        $data['jobs'] = $this->get('yarsha.service.job')->listJobs();
//
//        $message = \Swift_Message::newInstance()
//            ->setSubject('Profile Update Alert')
//            ->setFrom('jobalert@kantipurjob.com')
//            ->setTo("danepliz@gmail.com")
//            ->setBody(
//                $this->renderView('YarshaMainBundle:EmailTemplates:birthday_wish.html.twig', $data),
//                'text/html'
//            );
//        $this->get('mailer')->send($message);

        return $this->render('@YarshaMain/EmailTemplates/common_messages.html.twig', $data);
    }

    public function getFollowedCompanyBySeeker()
    {

    }


}
