<?php

namespace Yarsha\TagsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Yarsha\TagsBundle\Entity\Tag;


class TagRepository extends EntityRepository
{

    public function listTags($query = '')
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('t')->from(Tag::class, 't');

        if( $query != '' )
        {
            $qb->andWhere(
                $qb->expr()->like('t.name', $qb->expr()->literal('%'.$query.'%'))
            );
        }

        return $qb->getQuery()->getArrayResult();
    }

    public function getTagsListQueryBuilder($filters = [])
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('t')
            ->from(Tag::class, 't');

        if(array_key_exists('name', $filters) and trim($filters['name']) != "")
        {
            $qb->andWhere(
                $qb->expr()->like('t.name', $qb->expr()->literal('%'.$filters['name'].'%'))
            );
        }

        return $qb;
    }

    public function getTagsList($filters = [])
    {
        $qb = $this->getTagsListQueryBuilder($filters);

        return $qb->getQuery()->getResult();
    }

    public function getAllTags($filters = [])
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select([
            't.id as id',
            't.name as name',
            't.slug as slug',
            'count(td.id) as contentCount'
        ])
            ->from(Tag::class, 't')
            ->leftJoin('t.details', 'td');

        if(array_key_exists('name', $filters) and trim($filters['name']) != "")
        {
            $qb->andWhere(
                $qb->expr()->like('t.name', $qb->expr()->literal('%'.$filters['name'].'%'))
            );
        }

        if(array_key_exists('sort', $filters))
        {
            $field = $filters['sort'];
            $order = (isset($filters['order']) and $filters['order'] == 'desc') ? 'desc' : 'asc';

            if(property_exists(Tag::class, $field))
            {
                $qb->orderBy('t.'. $field, $order);
            }elseif( strtolower($field) == "count" ){
                $qb->orderBy('contentCount', $order);
            }


        }

        $qb->groupBy('t.id');

        return $qb->getQuery()->getResult();
    }

    public function getPopularTags($limit = 5)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select([
            't.id as id',
            't.name as name',
            't.slug as slug',
            'count(td.id) as contentCount'
        ])
            ->from(Tag::class, 't')
            ->leftJoin('t.details', 'td')
            ->orderBy('contentCount', 'desc')
            ->groupBy('t.id')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }

}
