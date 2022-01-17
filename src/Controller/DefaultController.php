<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\AnimeRepository;
use App\Service\PhotoUploader;
use App\Form\ContactType;
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
        $anime = $animeRepository->find(2);
        $animes = $animeRepository->findAll();
        $animes = $paginator->paginate(
            $animes,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('pages/home.html.twig', ['topanime' => $anime, 'animes' => $animes]);
    }

    /**
     * @Route("/anime/{id}", name="anime")
     */
    public function anime(int $id, AnimeRepository $animeRepository): Response
    {

        $animes = $animeRepository->find($id);
        return $this->render('pages/anime.html.twig', ['anime' => $animes]);
    }



    /**
     * @Route("/contact", name="anime")
     */
    public
    function contact(Request $request, AnimeRepository $animeRepository, EntityManagerInterface $entityManager): Response
    {
        $animes = $animeRepository->findAll();
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Message envoyé avec succès !');

            return $this->render("pages/home.html.twig", ['animes' => $animes]);
        }

        return $this->render("pages/contact.html.twig", ['contactForm' => $form->createView()]);
    }

}