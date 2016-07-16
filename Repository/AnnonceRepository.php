<?php

namespace MonApiBundle\Repository;

/**
 * annonceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnnonceRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAnnonce()
    {
        $time = new \DateTime();
        $time->modify('-10 day');
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.date > :time')
            ->setParameter('time', $time)
            ->orderBy('a.date', 'DESC')
        ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
    public function findCateVille($categorie_search, $ville)
    {
        $time = new \DateTime();
        $time->modify('-10 day');
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.date > :time')
            ->andwhere('a.categories = :categorie')
            ->andwhere('a.villes = :ville')
            ->setParameter('time', $time)
            ->setParameter('ville', $ville)
            ->setParameter('categorie', $categorie_search)
            ->orderBy('a.date', 'DESC')
        ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
    public function findVilles()
    {
        $qb = $this->createQueryBuilder('a');

         $qb->addGroupBy('a.categories')
            ->addGroupBy('a.villes')
        ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
