<?php

namespace Yarsha\AdminBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Yarsha\AdminBundle\Entity\Options;

class OptionsRepository extends EntityRepository
{

    public function getOptions()
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select(['o.id', 'o.name', 'o.value', 'o.status'])
            ->from(Options::class, 'o')
            ;

        $result = $qb->getQuery()->getArrayResult();

        $response = [];

        if( count($result) ){
            foreach($result as $res)
            {
                $response[$res['name']] = $res;
            }
        }

        return $response;
    }

}