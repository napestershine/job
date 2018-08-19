<?php

namespace Yarsha\EmployerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\ArticleBundle\Entity\Notice;
use Yarsha\ArticleBundle\Form\NoticeType;
use Yarsha\MainBundle\MainBundleConstants;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("/employer")
 * @Breadcrumb("Notice",routeName="yarsha_employer_notice_list")
 */
class NoticeController extends Controller
{

    /**
     * @Route("/notice/list", name="yarsha_employer_notice_list")
     */
    public function listNotices()
    {
        $noticeService = $this->get('yarsha.service.notice');
        $userType = MainBundleConstants::USER_TYPE_EMPLOYER;
        $data['notices'] = $noticeService->getPaginatedNotices($filters = [], $userType);

        return $this->render('YarshaEmployerBundle:Notice:list.html.twig', $data);
    }

    /**
     * @Route("/notice/add", name="yarsha_employer_notice_create")
     * @Route("/notice/{id}/edit", name="yarsha_employer_notice_edit")
     */
    public function updateNotice(Request $request)
    {
        $id = $request->get('id');
        $noticeService = $this->get('yarsha.service.notice');
        if ($id) {
            $notice = $noticeService->getNoticeById($id);
            if (!$notice) {
                throw new NotFoundHttpException;
            }
            $this->get('apy_breadcrumb_trail')->add($notice->getTitle());

        } else {
            $notice = new Notice();
            $this->get('apy_breadcrumb_trail')->add("New");
        }
        $form = $this->createForm(NoticeType::class, $notice);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $notice = $form->getData();
            try {
                $notice->setUserId($this->getUser()->getId());
                $notice->setUserType(MainBundleConstants::USER_TYPE_EMPLOYER);
                $noticeService->saveNotice($notice);
                $this->addFlash('success', 'Notice Saved Successfully,');
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }

            return $this->redirectToRoute('yarsha_employer_notice_list');
        }
        $data['form'] = $form->createView();

        return $this->render('YarshaEmployerBundle:Notice:add.html.twig', $data);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/notice/{id}/delete", name="yarsha_employer_notice_delete")
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
        $data['notices'] = $noticeService->getPaginatedNotices();

        return $this->redirectToRoute('yarsha_employer_notice_list', $data);
    }
}
