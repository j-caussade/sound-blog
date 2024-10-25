<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findByOrder($orderBy = 'date', $order = 'DESC')
    {
        $qb = $this->createQueryBuilder('a');
        if ($orderBy === 'date') {
            $qb->orderBy('a.date', $order);
        } elseif ($orderBy === 'like') {
            $qb->orderBy('a.liked', $order);
        }
        return $qb->getQuery()->getResult();
    }

    public function findByCategoryName($categoryName, $orderBy = 'date', $order = 'ASC')
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.category', 'c')
            ->andWhere('c.name = :categoryName')
            ->setParameter('categoryName', $categoryName);
        if ($orderBy === 'date') {
            $qb->orderBy('a.date', $order);
        } elseif ($orderBy === 'like') {
            $qb->orderBy('a.likes', $order);
        }
        return $qb->getQuery()->getResult();
    }
}
