<?php

namespace Yarsha\ArticleBundle\Entity;


interface ArticleInterface
{
    public function setUserId($id);

    public function getUserId();

    public function setUserType($type);

    public function getUserType();

}