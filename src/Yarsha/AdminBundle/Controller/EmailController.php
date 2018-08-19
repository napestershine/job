<?php

namespace Yarsha\AdminBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\EmployerBundle\YarshaEmployerEvents;
use Yarsha\JobsBundle\Entity\Job;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Yarsha\JobsBundle\Form\JobType;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\MainBundle\Event\EmployerJobsEmailEvent;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\OrganizationBundle\OrganizationConstants;
use Yarsha\MainBundle\Entity\Emails;
use Yarsha\AdminBundle\Form\EmailType;

/**
 * Class EmailController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Route("/admin")
 * @Breadcrumb("Emails", routeName="yarsha_admin_email_list")
 */
class EmailController extends Controller
{

    /**
     * @Route("/email/list", name="yarsha_admin_email_list")
     * @Breadcrumb("list")
     */
    public function indexAction(Request $request)
    {
        $filters = $request->query->all();
        $emails = $this->get('yarsha.service.email')->getPaginatedEmails($filters);
        $data['emails'] = $emails;

        return $this->render('YarshaAdminBundle:Emails:list.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/email/create", name="yarsha_admin_email_create")
     * @Route("/email/{id}/update", name="yarsha_admin_email_update")
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $id = $request->get('id');

        $emailService = $this->get('yarsha.service.email');

        if ($id) {
            $email = $emailService->getEmailById($id);

            if (!$email) {
                throw new NotFoundHttpException;
            }


            $isUpdating = true;


        } else {
            $email = new Emails();
            $isUpdating = false;
        }

        $form = $this->createForm(EmailType::class, $email);

        $form->handleRequest($request);
        $this->get('apy_breadcrumb_trail')->add($isUpdating ? $email->getName() : 'New');

        if ($form->isSubmitted() and $form->isValid()) {
            $postData = $form->getData();

            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($postData);
                $em->flush();

                return $this->redirectToRoute('yarsha_admin_email_list');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        $data['updating'] = $isUpdating;

        $data['form'] = $form->createView();

        return $this->render('YarshaAdminBundle:Emails:add.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/email/{id}/delete", name="yarsha_admin_email_delete")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');
        $emailService = $this->get('yarsha.service.email');
        $email = $emailService->getEmailById($id);
        if (!$email) {
            return NotFoundHttpException::class;
        }
        $em = $this->get('doctrine.orm.entity_manager');
        $em->remove($email);
        $em->flush();

        return $this->redirectToRoute('yarsha_admin_email_list');

    }


}
