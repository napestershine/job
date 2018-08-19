<?php

namespace Yarsha\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\AdminBundle\OptionsConstants;

/**
 * Class SettingController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Route("/admin")
 */
class SettingController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     *
     * @Route("/settings", name="yarsha_admin_settings")
     */
    public function indexAction(Request $request)
    {
        $optionService = $this->get('yarsha.service.options');

        if( 'POST' == $request->getMethod() )
        {
            try{
                $optionService->update($_POST);
                $this->addFlash('success', 'Settings Updated Successfully.');
                return $this->redirectToRoute('yarsha_admin_settings', ['t' => $request->get('t')]);
            }catch(\Exception $e)
            {
                $data['errorMessage'] = $e->getMessage();
            }

        }

        $data['options'] = $optionService->getOptions();

        return $this->render("@YarshaAdmin/Settings/index.html.twig", $data);
    }


}
