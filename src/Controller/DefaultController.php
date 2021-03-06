<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\Library;
use App\Form\CommentType;
use App\Repository\AnimeRepository;
use App\Repository\CommentRepository;
use App\Repository\LibraryRepository;
use App\Search\Search;
use App\Search\SearchType;
use App\Form\ContactType;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(Request $request, PaginatorInterface $paginator, AnimeRepository $animeRepository): Response
    {
        $anime = $animeRepository->find(2); //permet de choisir l'anime en tête de page
        $animes = $animeRepository->findAll();
        //pagination
        $animes = $paginator->paginate(
            $animes,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('pages/home.html.twig', ['topanime' => $anime, 'animes' => $animes]); //redirection vers page home
    }

    /**
     * @Route("/anime/{id}", name="anime")
     */
    public function anime(int $id, AnimeRepository $animeRepository): Response
    {

        $animes = $animeRepository->find($id); //recherche d'un anime par l'id
        return $this->render('pages/anime.html.twig', ['anime' => $animes]);
    }

    /**
     * @Route("/comment/{id}", name="comment")
     */

    public function comment(int $id, Request $request, AnimeRepository $animeRepository, EntityManagerInterface $entityManager): Response
    {
        $animes = $animeRepository->find($id);
        $user = $this->getUser();

        $comment = new Comment(); //creation formulaire commentaire
        $comment->setAnime($animes);
        $comment->setUser($user);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if (!$animes->isCommented($user)) { //verification de si un commentaire à déjà été créé
            $this->addFlash('error', 'Vous avez déjà commenté ce jeu !');

            return $this->render("pages/anime.html.twig", ['user' => $user, 'anime' => $animes, 'id' => $id]);
        }


        if ($form->isSubmitted() && $form->isValid()) { //validation du formulaire
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire mis en ligne !'); //message pour dire que le commentaire à été validé

            return $this->redirectToRoute("anime", ['animes' => $animes, 'id' => $id]); //redirection vers la page pour voir le commentaire
        }

        return $this->render("pages/comment.html.twig", ['anime' => $animes, 'commentForm' => $form->createView()]);
    }

    /**
     * @Route("/editComment/{id}", name="editComment")
     */
    public function editComment(int $id, Request $request, AnimeRepository $animeRepository, CommentRepository $commentRepository, EntityManagerInterface $entityManager): Response
    {
        $animes = $animeRepository->find($id);
        $userId = $this->getUser()->getId();
        $user = $this->getUser();

        $comment = $commentRepository->getCommentId( $userId, $id); //recherche de l'id du commentaire via un querybuilder

        if(!isset($comment)){ //verification qu'il existe un commentaire à modifier
            $this->addFlash('error', 'Veuillez d\'abord créer un commentaire !'); //erreur si il n'y a pas encore de commentaire

            return $this->redirectToRoute("anime", ['user' => $user, 'anime' => $animes, 'id' => $id]);
        }

        $comment->setAnime($animes);
        $comment->setUser($user);

        $form = $this->createForm(CommentType::class, $comment); //créer le formulaire pour modifier le commentaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { //validation du commentaire
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire mise à jour avec succès !'); //message de mise à jour du commentaire

            return $this->redirectToRoute("anime", ['user' => $user, 'anime' => $animes, 'id' => $id]);
        }

        return $this->render("pages/editComment.html.twig", ['editCommentForm' => $form->createView()]);
    }


    /**
     * @Route("/add/{id}", name="add")
     */

    public function add(int $id, AnimeRepository $animeRepository, LibraryRepository $libraryRepository, EntityManagerInterface $entityManager)
    {
        $anime = $animeRepository->find($id);
        $user = $this->getUser();
        $animebis = $libraryRepository->getAnimeLibraryBy($user, $anime);

        if (!isset($animebis)) {
            $library = new Library(); //ajout d'un nouvel animé à la collection via un formulaire
            $library->setAnime($anime);
            $library->setUtilisateur($user);

            $entityManager->persist($library);
            $entityManager->flush();

            $this->addFlash('success', 'Animé ajouté avec succès !');

            return $this->redirectToRoute("library", ['user' => $user]);
        }
        $this->addFlash('error', 'Animé déja ajouté !');
        return $this->redirectToRoute("library", ['user' => $user]);
    }


    /**
     * @Route("/delete/{id}", name="delete")
     */

    public function delete(int $id, LibraryRepository $libraryRepository, EntityManagerInterface $entityManager)
    {
        $library = $libraryRepository->find($id); //fonction pour supprimer un anime de la collection
        $user = $this->getUser();
        if(!$user->isAddByThisUser($user, $id)){ // sécurité pour refuser l'accès à un autre utilisateur qui celui de la collection
            throw $this->createAccessDeniedException('You cannot access this page!');
        }

        $entityManager->remove($library);
        $entityManager->flush();

        $this->addFlash('success', 'Animé supprimé avec succès !');
        return $this->redirectToRoute("library", ['user' => $user]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, PaginatorInterface $paginator, AnimeRepository $animeRepository, EntityManagerInterface $entityManager): Response
    {

        $anime = $animeRepository->find(2);
        $animes = $animeRepository->findAll();
        $animes = $paginator->paginate(
            $animes,
            $request->query->getInt('page', 1),
            6
        );

        $contact = new Contact(); //creation formulaire de contact

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { //validation du formulaire de contact
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Message envoyé avec succès !');

            return $this->redirectToRoute("home", ['topanime' => $anime, 'animes' => $animes]);
        }

        return $this->render("pages/contact.html.twig", ['contactForm' => $form->createView()]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, AnimeRepository $animeRepository): Response
    {
        $search = new search(); //formulaire de recherche
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        $result = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $animeRepository->findBySearch($search);
        }
        return $this->render('pages/search.html.twig', ['animes' => $result, 'searchFullForm' => $form->createView()]);
    }

}