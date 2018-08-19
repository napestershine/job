<?php

namespace Yarsha\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Yarsha\AdminBundle\Entity\User;
use Yarsha\AdminBundle\Form\AccountManagerType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;


/**
 * Class AccountManagerController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Route("/admin")
 * @Breadcrumb("Account Manager", routeName="yarsha_admin_account_managers_list")
 */
class AccountManagerController extends Controller
{


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/account-managers", name="yarsha_admin_account_managers_list")
     * @Breadcrumb("list")
     */
    public function indexAction(Request $request)
    {
        $filters = $request->query->all();

        $data['accountManagers'] = $this->get('yarsha.service.admin_user')->getPaginatedAccountManagerList($filters);

        return $this->render('@YarshaAdmin/AccountManager/index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/account-manager/new", name="yarsha_admin_account_manager_new")
     * @Route("/account-manager/{username}/update", name="yarsha_admin_account_manager_update")
     */
    public function manageAction(Request $request)
    {
        $data['is_updating'] = false;

        $actionDesc = 'Created';

        $userManager = $this->get('yarsha_admin.user_manager');

        if( $username = $request->get('username') )
        {
            $manager = $userManager->findUserByUsername($username);

            if( !$manager or ! $manager->hasRole('ROLE_ACCOUNT_MANAGER') ){
                throw new AccessDeniedException;
            }

            $this->get('apy_breadcrumb_trail')->add($manager->getUsername());

            $data['is_updating'] = true;

            $actionDesc = 'Updated';

        }else{
            $manager = $userManager->createUser();

            $this->get('apy_breadcrumb_trail')->add('new');
        }

        $form = $this->createForm(AccountManagerType::class, $manager, ['is_updating' => $data['is_updating']]);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            try{
                $manager = $form->getData();

                if($data['is_updating'] == false)
                {
                    $manager->setEnabled(true);
                    $manager->addRole('ROLE_ACCOUNT_MANAGER');
                    $manager->setEmail($manager->getContactEmail().'@jobs.com');
                    $manager->setPlainPassword($manager->getPassword());
                }

                $manager->upload();

                $userManager->updateUser($manager);

                $this->get('doctrine.orm.entity_manager')->flush();

                $this->addFlash('success', "Account Manager $actionDesc Successfully.");

                return $this->redirectToRoute('yarsha_admin_account_managers_list');

            }catch(\Exception $e)
            {
                $data['errorMessage'] = $e->getMessage();
            }
        }

        $data['form'] = $form->createView();

        $data['manager'] = $manager;

        return $this->render('@YarshaAdmin/AccountManager/manage.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/ajax/account-manager/delete", name="yarsha_admin_ajax_delete_account_manager")
     */
    public function deleteAction(Request $request)
    {
        if( "POST" != $request->getMethod() )
        {
            return new JsonResponse(['status' => 'error', 'message' => 'Access Denied'], 400);
        }

        $username = $request->get('username');

        $userManager = $this->get('yarsha_admin.user_manager');

        $manager = $userManager->findUserByUsername($username);

        if( !$manager )
        {
            return new JsonResponse(['status' => 'error', 'message' => 'Account manager not found.'], 400);
        }

        try{
            $manager->setDeleted(true);

            $userManager->updateUser($manager);

            $this->get('doctrine.orm.entity_manager')->flush();

//            $this->addFlash('success', 'Account Manager deleted successfully.');

        }catch(\Exception $e)
        {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'success']);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/ajax/account-manager/change-status", name="yarsha_admin_ajax_account_manager_change_status")
     */
    public function changeStatusAction(Request $request)
    {
        if( "POST" != $request->getMethod() )
        {
            return new JsonResponse(['status' => 'error', 'message' => 'Access Denied'], 400);
        }

        $username = $request->get('username');

        $userManager = $this->get('yarsha_admin.user_manager');

        $manager = $userManager->findUserByUsername($username);

        if( !$manager )
        {
            return new JsonResponse(['status' => 'error', 'message' => 'Account manager not found.'], 400);
        }

        try{
            $manager->setEnabled(!$manager->isEnabled());

            $userManager->updateUser($manager);

            $this->get('doctrine.orm.entity_manager')->flush();

//            $this->addFlash('success', 'Status changed successfully.');

        }catch(\Exception $e)
        {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'success', 'enabled' => $manager->isEnabled()]);
    }
}
