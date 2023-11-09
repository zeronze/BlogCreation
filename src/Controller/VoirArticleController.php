<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoirArticleController extends AbstractController
{
    #[Route('/voir/article/{id}', name: 'app_voir_article')]
    public function index(ArticleRepository $articleRepository, $id): Response
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('L\'article n\'existe pas.');
        }
        
        return $this->render('voir_article/index.html.twig', [
            'article' => $article,
            'controller_name' => 'VoirArticleController',
        ]);
    }
}
