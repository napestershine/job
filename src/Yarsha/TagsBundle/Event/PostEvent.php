<?php

namespace Yarsha\TagsBundle\Event;

use Symfony\Component\Form\Form;
use Symfony\Component\EventDispatcher\Event;


class PostEvent extends Event
{
    private $entity;

    private $form;

    public function __construct($entity, Form $form)
    {
        $this->entity = $entity;
        $this->form = $form;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getForm()
    {
        return $this->form;
    }

}
