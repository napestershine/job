<?php

namespace Yarsha\TagsBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use EducationSansar\Bundle\TagsBundle\Entity\Tag;


class TagEvent extends Event
{

    /**
     * @var \Yarsha\TagsBundle\Entity\Tag
     */
    private $tag;

    public function __construct(\Yarsha\TagsBundle\Entity\Tag $tag)
    {
        $this->tag = $tag;
    }

    public function getTag()
    {
        return $this->tag;
    }

}
