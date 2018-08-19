<?php

namespace Yarsha\AgencyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('YarshaAgencyBundle:Default:index.html.twig');
    }
}
