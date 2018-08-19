<?php

namespace Yarsha\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobSeekerBundle\Entity\User;
use Yarsha\MainBundle\Service\ImageService;


class DefaultController extends Controller
{

    public function indexAction()
    {
        return $this->render('YarshaMainBundle:Default:index.html.twig');
    }


//    /**
//     * @return \Symfony\Component\HttpFoundation\Response
//     * @Route("test/alert",name="test_alert")
//     */
//    public function testAlert()
//    {
//        $jobService = $this->get('yarsha.service.job');
//        $jobs = $jobService->getRecentJob();
//        $data['jobs'] = $jobs;
//        $data['name'] = 'pradeep karki';
//        $data['email'] = 'pradeep karki';
//        return $this->render('YarshaMainBundle:Emails:job_alert.html.twig',$data);
//    }
    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("image/cropper", name="yarsha_main_image_cropper")
     */
    public function cropImageAction(Request $request)
    {
        $data = [
            'message' => '',
            'result' => '',
            'state' => 500
        ];
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
                'state' => 200,
            ];
        }

        return new JsonResponse($data, 200);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("image/crop", name="yarsha_main_image_croper_show")
     */
    public function showCropAction()
    {
        return $this->render('YarshaMainBundle:Default:image_manipulation.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/test-email-template/mass")
     */
    public function testMassEmail(Request $request)
    {
        $jobService = $this->get('yarsha.service.job');
        $jobs = $jobService->getRecentJob();
        $data['jobs'] = $jobs;
        $data['name'] = 'pradeep karki';

//        $body = $this->render('YarshaMainBundle:Emails:job_alert.html.twig', $data);
//        $this->get('yarsha.service.mailer')->sendEmail('test', $body,'danepliz@gmail.com');

        return $this->render('YarshaMainBundle:Emails:job_alert.html.twig', $data);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/test-email-template")
     */
    public function testSeekerRegisterEmail(Request $request)
    {
//        $res = $this->get('doctrine.orm.default_entity_manager')->getRepository(User::class)->getJobSeekersWithJobAlertEmailActivated();

        //7141

//        $jobs = $this->get('doctrine.orm.default_entity_manager')->getRepository(Job::class)->getRelatedJobsByJobSeeker(7141);

//        dump($jobs); die;
    }
}
