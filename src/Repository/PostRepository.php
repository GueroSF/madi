<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class PostRepository extends ServiceEntityRepository
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        $this->security = $security;
        parent::__construct($registry, Post::class);
    }

    public function findLatestForUser(User $user, int $page = 1): Paginator
    {
        $qb = $this->queryBuilderFindLatest($page);
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $qb
                ->innerJoin('p.postInfos', 'pip', Join::WITH, 'pip.user = :userId')
//                ->innerJoin('pip.user', 'u', Join::WITH, 'u.id = :userId')
                ->setParameter(':userId', $user->getId());
        }

        return (new Paginator($qb))->paginate($page);
    }

    public function findLatest(int $page = 1): Paginator
    {
        $qb = $this->queryBuilderFindLatest($page);

        return (new Paginator($qb))->paginate($page);
    }

    private function queryBuilderFindLatest(int $page): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->addSelect('a')
            ->innerJoin('p.author', 'a')
            ->where('p.publishedAt <= :now')
            ->orderBy('p.publishedAt', 'DESC')
            ->setParameter('now', new \DateTime());
    }
}
