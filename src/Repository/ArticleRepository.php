<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Security\Core\Security;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function countNbArticlesMine(User $user)
    {
        $query = $this->createQueryBuilder('a')
            ->select('count(a)')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ;

        return $query->getQuery()->getSingleScalarResult();
    }

    public function findOnlyPublishedWithPaging(int $currentPage, int $nbPerPage)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.published = true')
            ->orderBy('a.created_at', 'DESC')
            ->leftJoin('a.comments', 'c')
            ->leftJoin('a.categories', 'cat')
            ->addSelect('c')
            ->addSelect('cat')
            ->addOrderBy('c.created_at', 'DESC')
            ->setFirstResult(($currentPage - 1) * $nbPerPage)
            ->setMaxResults($nbPerPage);

        return new Paginator($query);
    }

    public function findOnlyMineWithPaging(int $currentPage, int $nbPerPage, User $user)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->orderBy('a.created_at', 'DESC')
            ->leftJoin('a.comments', 'c')
            ->leftJoin('a.categories', 'cat')
            ->addSelect('c')
            ->addSelect('cat')
            ->addOrderBy('c.created_at', 'DESC')
            ->setParameter('user', $user)
            ->setFirstResult(($currentPage - 1) * $nbPerPage)
            ->setMaxResults($nbPerPage);

        return new Paginator($query);
    }

    public function findOnlyPublishedByCategory(Category $category, int $currentPage, int $nbPerPage)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.published = true')
            ->andWhere('cat = :category')
            ->orderBy('a.created_at', 'DESC')
            ->leftJoin('a.categories', 'cat')
            ->leftJoin('a.categories', 'categories')
            ->leftJoin('a.comments', 'com')
            ->addSelect('categories')
            ->addSelect('com')
            ->setParameter(':category', $category)
            ->setFirstResult(($currentPage - 1) * $nbPerPage)
            ->setMaxResults($nbPerPage);
        ;

        return new Paginator($query);
    }


    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
