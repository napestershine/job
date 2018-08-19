<?php

namespace Yarsha\EmployerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Yarsha\MainBundle\Service\ImageCropService;
use Yarsha\OrganizationBundle\Entity\OrganizationBannerImages;
use Yarsha\EmployerBundle\Form\EmployerRegisterOrganizationType;
use Yarsha\OrganizationBundle\Form\OrganizationContactPersonType;
use Yarsha\EmployerBundle\Event\ProfileUpdateEvent;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;
use Yarsha\EmployerBundle\Service\ImageService;


/**
 * Class AjaxController
 * @package Yarsha\EmployerBundle\Controller
 * @Route("/employer")
 */
class AjaxController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/upload/profile-picture", name="yarsha_employer_ajax_upload_profile_pic")
     */
    public function cropImageAction(Request $request)
    {
        $data = [
            'message' => '',
            'result' => '',
            'state' => 500
        ];

        $user = $this->getUser();
        $organization = $user->getOrganization() ? $user->getOrganization() : '';

        if ($organization) {
            if ($request->isXmlHttpRequest()) {
                $file = $request->files->get('avatar_file');
                $source = $request->get('avatar_src');
                $data = $request->get('avatar_data');
                $files['error'] = $file->getError();
                $files['tmp_name'] = $file->getRealPath();
                $response = new ImageCropService(
                    $source ? $source : null,
                    $data ? $data : null,
                    $files ? $files : null
                );
                $data = [
                    'message' => $response->getMsg(),
                    'result' => $response->getResult(),
                    'state' => 200
                ];
                if ($response->success) {
                    $organization->setPath($response->getFilename() . $response->getExtension());
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($organization);
                try {
                    $em->flush();
                } catch (\Exception $e) {
                    $data['message'] = $e->getMessage();
                }
            }
        }

        return new JsonResponse($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/ajax/upload/cover-pic", name="yarsha_employer_ajax_upload_cover_pic")
     */
    public function uploadPictureAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->get('Doctrine.orm.entity_manager');

        if (!$user or !$user->hasRole('ROLE_EMPLOYER')) {
            return new JsonResponse(['message' => 'Unauthorized Request'], 400);
        }

        try {

            $others_banner = $em->getRepository(OrganizationBannerImages::class)->findBy(
                [
                    'status' => 1,
                    'isFeatured' => true,
                    'employer' => $user->getOrganization()
                ]

            );
            if ($others_banner) {
                foreach ($others_banner as $b) {
                    $b->deactivateFeatured();
                }
            }

            $cover = new OrganizationBannerImages();
            $picture = $request->files->get('coverPic');
            $cover->setEmployer($user->getOrganization());
            $cover->setFile($picture);
            $cover->setIsFeatured(true);
            $cover->setStatus(true);
            $cover->upload();
            $resize = new ImageService();
            if ($resize->resizeImage($cover->getAbsolutePath(), $cover->getUploadResizeRootDir(), 1268, 350)) {

                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($cover);
                $em->flush();

                $response = [
                    'status' => 'success',
                    'image' => $cover->getPath()
                ];

            } else {
                $response = [
                    'status' => 'Please Upload Image size minimum 1268X350',
                    'image' => ''
                ];
            }

            return new JsonResponse($response, 200);

        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }


    /**
     * @Route("/ajax/edit/info", name="yarsha_employer_ajax_update_profile")
     */
    public function changeEmployerInfo(Request $request)
    {
        $employer = $this->getUser()->getOrganization();
        $form = $this->createForm(EmployerRegisterOrganizationType::class, $employer);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $infoData = $form->getData();
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($infoData);
            try {
                $em->flush();
                $event = new ProfileUpdateEvent($this->getUser());
                $eventDispatcher = $this->get('event_dispatcher');
                $eventDispatcher->dispatch(MainBundleEvents::EVENT_EMPLOYER_PROFILE_UPDATE, $event);
                $this->addFlash('success', 'Profile Updated successfully.');

                return $this->redirectToRoute('yarsha_employer_profile_view');
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        }

        $data['form'] = $form->createView();
        $data['id'] = $employer->getId();
        $response['template'] = $this->renderView('YarshaEmployerBundle:Employer:infoUpdate.html.twig', $data);

        return new JsonResponse($response);

    }


    /**
     * @Route("/ajax/edit/{contactId}/contact-person", name="yarsha_employer_ajax_profile_change_contact_person")
     */
    public function changeContactPerson(Request $request)
    {
        $id = $request->get('contactId');
        $contactPerson = $this->getDoctrine()->getRepository(OrganizationContactPerson::class)->find($id);

        $type = $contactPerson->getContactType();
        $form = $this->createForm(OrganizationContactPersonType::class, $contactPerson,
            ['contact_type' => $type]);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $infoData = $form->getData();
            $infoData->setContactType($infoData->getContactType());
            $this->getDoctrine()->getEntityManager()->persist($infoData);
            try {
                $this->getDoctrine()->getEntityManager()->flush();
                $event = new ProfileUpdateEvent($this->getUser());
                $eventDispatcher = $this->get('event_dispatcher');
                $eventDispatcher->dispatch(MainBundleEvents::EVENT_EMPLOYER_PROFILE_UPDATE, $event);

                return $this->redirectToRoute('yarsha_employer_profile_view');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }


        $data['form'] = $form->createView();
        $data['id'] = $id;
        $response['template'] = $this->renderView('YarshaEmployerBundle:Employer:contactPersonUpdate.html.twig', $data);

        return new JsonResponse($response);

    }


}
