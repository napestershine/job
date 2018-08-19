<?php

namespace Yarsha\EmployerBundle\Controller;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;

class JobSeekerController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/employer/ajax/{seekerId}/request/cv", name="yarsha_ajax_employer_request_cv")
     */
    public function requestCvAction(Request $request)
    {
        $data['success'] = false;
        $seekerId = $request->get('seekerId');
        $em = $this->getDoctrine()->getManager();
        if ($seekerId) {
            $seeker = $this->getDoctrine()->getManager()->getRepository(JobSeeker::class)->find($seekerId);
            if (!$seeker) {
                $data['errorMessage'] = "Seeker Not found.";
            }
            $employer = $this->getUser();

            $organization = $employer->getOrganization();

            $contactPerson = $em->getRepository(OrganizationContactPerson::class)->findOneBy([
                'organization' => $organization,
                'contactType' => 'contact'
            ]);

            if($contactPerson instanceof OrganizationContactPerson){
                $subject = "Request for CV";
                $fromEmail = $contactPerson->getEmail();
                $toEmail = $seeker->getContactEmail();
                $data['message'] = $employer->getOrganization()->getName() . " requested for your CV.";
                $body = $this->renderView('@YarshaMain/EmailTemplates/common_messages.html.twig', $data);
                $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($fromEmail)
                    ->setTo($toEmail)
                    ->setBody($body, 'text/html');
                try {
                    $this->get('mailer')->send($message);
                    $data['success'] = true;
                } catch (\Exception $e) {
                    $data['success'] = false;
                    $data['errorMessage'] = $e->getMessage();
                }
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     * @Route("/employer/ajax/sendmessage/form", name="yarsha_ajax_employer_get_send_message_form")
     */
    public function getSendMessageViewAction()
    {
//        $form = $this->createFormBuilder()
//            ->add('message', TextareaType::class, [
//                'required' => true,
//                'label' => 'Send Message'
//            ])
//            ->add('save', SubmitType::class, [
//                'attr' => [
//                    'class' => 'btn btn-primary'
//                ]
//            ])
//            ->getForm();
//        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('@YarshaEmployer/Jobseeker/sendmessage.html.twig');

//        $response['template'] = $this->renderView('@YarshaEmployer/Jobseeker/sendmessage.html.twig', $data);
        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/employer/ajax/{seekerId}/sendmessage", name="yarsha_ajax_employer_send_message")
     */
    public function sendMessageAction(Request $request)
    {
        $data['success'] = false;
        $seekerId = $request->get('seekerId');
        $em = $this->get('doctrine.orm.entity_manager');
        if ($seekerId) {
            $seeker = $this->getDoctrine()->getManager()->getRepository(JobSeeker::class)->find($seekerId);
            if (!$seeker) {
                $data['errorMessage'] = "Seeker Not found.";
            } else {
                $employer = $this->getUser();

                $organization = $employer->getOrganization();

                $contactPerson = $em->getRepository(OrganizationContactPerson::class)->findOneBy(
                    [
                        'organization' => $organization,
                        'contactType' => 'contact'
                    ]

                );


                $subject = "Message from " . $employer->getOrganization()->getName();
                $fromEmail = $contactPerson->getEmail();
                $toEmail = $seeker->getContactEmail();
                $data['message'] = $request->request->get('message');
                $body = $this->renderView('@YarshaMain/EmailTemplates/common_messages.html.twig', $data);
                $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($fromEmail)
                    ->setTo($toEmail)
                    ->setBody($body, 'text/html');
                try {
                    $this->get('mailer')->send($message);
                    $data['success'] = true;
                } catch (\Exception $e) {
                    $data['success'] = false;
                    $data['errorMessage'] = $e->getMessage();
                }
            }
        }

        return new JsonResponse($data);
    }

}
