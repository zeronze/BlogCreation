<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        // Récupérez les trois derniers articles triés par date de publication décroissante
        $articles = $articleRepository->findBy([], ['date' => 'DESC'], 3);


        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
