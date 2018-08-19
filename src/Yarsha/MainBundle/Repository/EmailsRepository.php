<?php
namespace Yarsha\MainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Yarsha\MainBundle\Entity\Emails;

class EmailsRepository extends EntityRepository
{

    public function getAllEmails($filters = [], $limit = 40)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('e')
            ->from(Emails::class, 'e')
            ->where("e.status = 1");

        if (array_key_exists('name', $filters) and $filters['name'] != "") {
            $qb->andWhere(
                $qb->expr()->like('e.name', $qb->expr()->literal('%' . $filters['name'] . '%'))
            );
        }

        if (array_key_exists('email', $filters) and $filters['email'] != "") {
            $qb->andWhere(
                $qb->expr()->like('e.email', $qb->expr()->literal('%' . $filters['email'] . '%'))
            );
        }

        return $qb;
    }

    public function getAllActiveEmails()
    {

        $qb = $this->_em->createQueryBuilder();

        $qb->select('e')
            ->from(Emails::class, 'e')
            ->where("e.status = 1");

        return $results = $qb->getQuery()->getArrayResult();
    }


}
