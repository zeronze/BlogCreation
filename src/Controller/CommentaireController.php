<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupérer tous les commentaires
        $commentaires = $commentaireRepository->findAll();

        // Paginer les commentaires
        $pagination = $paginator->paginate(
            $commentaires,
            $request->query->getInt('page', 1), // Paramètre page de la requête, 1 par défaut
            10 // Limite d'éléments par page
        );

        return $this->render('commentaire/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/article/{id}/new', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository, EntityManagerInterface $entityManager, $id): Response
    {
        // Récupérer l'article associé depuis la base de données
        $article = $articleRepository->find($id);

        // Créer un nouveau commentaire
        $commentaire = new Commentaire();

        // Associer le commentaire à l'article
        $commentaire->setArticle($article);

        // Obtenez la date actuelle
        $currentDate = new \DateTime();

        // Définir la date du commentaire comme la date actuelle
        $commentaire->setDate($currentDate);

        // Créer le formulaire
        $form = $this->createForm(CommentaireType::class, $commentaire);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persister le commentaire
            $entityManager->persist($commentaire);
            $entityManager->flush();

            // Rediriger vers la page de l'article
            return $this->redirectToRoute('app_voir_article', ['id' => $article->getId()]);
        }

        // Afficher le formulaire
        return $this->render('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
