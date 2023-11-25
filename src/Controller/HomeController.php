<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\SearchArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository, Request $request): Response
    {
        $articles = $articleRepository->findBy([], ['date' => 'DESC'], 3);

        // Créer le formulaire
        $form = $this->createForm(SearchArticleType::class);

        // Traiter le formulaire s'il a été soumis
        $form->handleRequest($request);

        $searchResults = []; // Initialiser la variable pour stocker les résultats de recherche

        if ($form->isSubmitted() && $form->isValid()) {
            // On recherche les annonces correspondant au mot clé
            $searchResults = $articleRepository->search($form->get('mots')->getData());
        }

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'searchResults' => $searchResults, // Ajouter les résultats de recherche à la vue
            'form' => $form->createView(),
        ]);
    }
}
