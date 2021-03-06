<?php

namespace App\Controller\Admin;

use App\Entity\Anime;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\Library;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // redirect to some CRUD controller
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Anime List');
    }

    public function configureMenuItems(): iterable //sous menu de la dashboard
    {
        yield MenuItem::linkToUrl('Back to home', 'fa fa-home', '/pwd/');
        yield MenuItem::linkToCrud('Anime', 'fas fa-list', Anime::class);
        yield MenuItem::linkToCrud('Comment', 'fas fa-list', Comment::class);
        yield MenuItem::linkToCrud('Contact', 'fas fa-list', Contact::class);
        yield MenuItem::linkToCrud('Library', 'fas fa-list', Library::class);
        yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
    }
}
