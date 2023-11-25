<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Get commentaire for a specific article.
     *
     * @param int $articleId
     * @return array
     */
    public function getCommentaireForArticle(int $articleId): array
    {
        return $this->createQueryBuilder('a')
            ->select('c.id, c.pseudo, c.email, c.description, c.date')
            ->join('a.commentaires', 'c') // Assuming 'commentaires' is the property name for the commentaire relationship in the Article entity
            ->where('a.id = :articleId')
            ->setParameter('articleId', $articleId)
            ->getQuery()
            ->getResult();
    }

    public function search($mots = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.active = 1');

        if ($mots !== null) {
            $qb->andWhere('MATCH(a.titre, a.description) AGAINST(:mots BOOLEAN) > 0')
                ->setParameter('mots', $mots);
        }

        return $qb->getQuery()->getResult();
    }
}
