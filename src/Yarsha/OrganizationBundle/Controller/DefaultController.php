<?php

namespace Yarsha\OrganizationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {
        return $this->render('YarshaOrganizationBundle:Default:index.html.twig');
    }
}
