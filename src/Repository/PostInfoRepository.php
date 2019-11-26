<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostInfo;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PostInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostInfo[]    findAll()
 * @method PostInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostInfoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PostInfo::class);
    }

    public function findByUserAndPost(User $user, Post $post): ?PostInfo
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.user = :userId')
            ->andWhere('i.post = :postId')
            ->setParameters([
                ':userId' => $user->getId(),
                ':postId' => $post->getId()
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return PostInfo[] Returns an array of PostInfo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PostInfo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
