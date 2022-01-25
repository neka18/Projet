<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AbstractController
{
    /**
     * @Route("/library", name="library")
     */


    public function library(){
        $user = $this->getUser();
        return $this->render("pages/library.html.twig", ['user' => $user]);
    }
}