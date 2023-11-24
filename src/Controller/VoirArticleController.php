<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Import Symfony's Request class
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoirArticleController extends AbstractController
{
    #[Route('/voir/article/{id}', name: 'app_voir_article')]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request, $id): Response
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('L\'article n\'existe pas.');
        }

        // Get commentaires for the article
        $commentaires = $articleRepository->getcommentaireForArticle($id);

        // Paginate commentaires
        $pagination = $paginator->paginate(
            $commentaires,
            $request->query->getInt('page', 1),
        5 // Nombre d'éléments par page
        );

        return $this->render('voir_article/index.html.twig', [
            'article' => $article,
            'pagination' => $pagination, // Passer la variable pagination au template
            'controller_name' => 'VoirArticleController',
        ]);
    }
}
