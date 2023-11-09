<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Importez la classe Request depuis le bon namespace
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class AllArticleController extends AbstractController
{
    #[Route('/all/article', name: 'app_all_article')]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request)
    {
        $query = $articleRepository->findAll(); // Remplacez cela par votre requête de recherche d'articles
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Récupère le numéro de page depuis la requête
            6 // Nombre d'articles par page
        );
    
        return $this->render('all_article/index.html.twig', [
            'articles' => $pagination,
            'pagination_template' => '@KnpPaginator/Pagination/bootstrap_v5_pagination.html.twig',
        ]);
    }
}
