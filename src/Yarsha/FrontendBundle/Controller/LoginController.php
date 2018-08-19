<?php

namespace Yarsha\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\JobsBundle\Entity\Job;


/**
 * Class LoginController
 * @package Yarsha\FrontendBundle\Controller
 *
 * @Route("/")
 */
class LoginController extends Controller
{

//    /**
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     *
//     * @Route("/login/seeker", name="yarsha_frontend_login_as_seeker")
//     */
//    public function loginSeekerAction(Request $request)
//    {
//
//
//        return $this->render('YarshaFrontendBundle:Login:login_seeker.html.twig');
//    }
//
//
//    /**
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     *
//     * @Route("/login/employer", name="yarsha_frontend_login_as_employer")
//     */
//    public function loginEmployerAction(Request $request)
//    {
//
//
//        return $this->render('YarshaFrontendBundle:Login:login_employer.html.twig');
//    }
}
