<?php

namespace Yarsha\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('YarshaArticleBundle:Default:index.html.twig');
    }
}
