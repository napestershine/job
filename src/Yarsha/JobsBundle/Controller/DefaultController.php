<?php

namespace Yarsha\JobsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yarsha\JobsBundle\Entity\Job;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('YarshaJobsBundle:Default:index.html.twig');
    }

}
