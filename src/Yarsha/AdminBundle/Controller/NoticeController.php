<?php

namespace Yarsha\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\ArticleBundle\Entity\Notice;
use Yarsha\ArticleBundle\Form\NoticeType;
use Yarsha\MainBundle\MainBundleConstants;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("/admin")
 * @Breadcrumb("Notice",routeName="yarsha_admin_notice_list")
 */
class NoticeController extends Controller
{

    /**
     * @Route("/notice/add", name="yarsha_admin_add_notice")
     * @Route("/notice/{id}/update", name="yarsha_admin_update_notice")
     */
    public function addNotice(Request $request)
    {
        $id = $request->get('id');
        $noticeService = $this->get('yarsha.service.notice');
        if ($id) {
            $notice = $noticeService->getNoticeById($id);
            if (!$notice) {
                throw new NotFoundHttpException;
            }
            $isUpdating = true;
        } else {
            $notice = new Notice();
            $isUpdating = false;
        }
        $form = $this->createForm(NoticeType::class, $notice);
        $form->handleRequest($request);
        $this->get('apy_breadcrumb_trail')->add($isUpdating ? $notice->getTitle() : 'New');
        if ($form->isSubmitted() && $form->isValid()) {
            $notice = $form->getData();
            try {
                $notice->setUserId($this->getUser()->getId());
                $notice->setUserType(MainBundleConstants::USER_TYPE_ADMIN);
                $noticeService->saveNotice($notice);
                $this->addFlash('success', 'Notice Saved Successfully,');
                $data['notices'] = $noticeService->getPaginatedNotices();

                return $this->redirectToRoute('yarsha_admin_notice_list', $data);
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        }
        $data['form'] = $form->createView();
        $data['updating'] = $isUpdating;

        return $this->render('YarshaAdminBundle:Notice:add.html.twig', $data);
    }

    /**
     * @Route("/notice/list", name="yarsha_admin_notice_list")
     */
    public function listNotices()
    {
        $noticeService = $this->get('yarsha.service.notice');
        $userType = MainBundleConstants::USER_TYPE_ADMIN;
        $data['notices'] = $noticeService->getPaginatedNotices($filters = [], $userType);

        return $this->render('YarshaAdminBundle:Notice:list.html.twig', $data);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/notice/{id}/delete", name="yarsha_admin_delete_notice")
     */
    public function deleteNotice($id)
    {
        $noticeService = $this->get('yarsha.service.notice');
        $notice = $noticeService->getNoticeById($id);
        if ($notice) {
            try {
                $notice->setIsDeleted(true);
                $noticeService->saveNotice($notice);
                $this->addFlash('success', 'One notice deleted.');
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        } else {
            throw new NotFoundHttpException();
        }
        $userType = MainBundleConstants::USER_TYPE_ADMIN;
        $data['notices'] = $noticeService->getPaginatedNotices($filters = [], $userType);

        return $this->redirectToRoute('yarsha_admin_notice_list', $data);
    }
}
