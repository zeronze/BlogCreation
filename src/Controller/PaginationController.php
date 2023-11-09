<?php
// src/Controller/PaginationController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaginationController extends AbstractController
{
    /**
     * @Route("/pagination", name="pagination")
     */
    public function paginate(Request $request): Response
    {
        // Exemple de récupération des articles paginés à partir de votre source de données (par exemple, un tableau).
        $page = $request->query->get('page', 1);
        $articlesPerPage = 6; // Nombre d'articles par page.
        $articles = $this->getPaginatedArticles($page, $articlesPerPage);
    
        return $this->render('pagination/_pagination.html.twig', [
            'articles' => $articles,
            'pagination_template' => '@KnpPaginator/Pagination/bootstrap_v5_pagination.html.twig',

        ]);
    }

    // Exemple de fonction pour obtenir les articles paginés depuis un tableau de données (à adapter à votre source de données réelle).
    private function getPaginatedArticles($page, $perPage)
    {
        // Simulez un tableau de données d'articles.
        $allArticles = [
            // ... Votre tableau d'articles.
        ];

        $offset = ($page - 1) * $perPage;
        $articles = array_slice($allArticles, $offset, $perPage);

        return $articles;
    }
}
