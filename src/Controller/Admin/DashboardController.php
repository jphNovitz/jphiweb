<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Message\MessageCrudController;
use App\Controller\Admin\Blog\BlogCrudController;
use App\Entity\Blog;
use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Project;
use App\Entity\Role;
use App\Entity\Tag;
use App\Entity\Technology;
use App\Entity\Topic;
use App\Entity\Work;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(MessageCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Pixelservices');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
//        yield MenuItem::linkToMessage('Messages', 'fa fa-envelope');
        yield MenuItem::linkToCrud('Messages', 'fas fa-envelope', Message::class);
        yield MenuItem::linkToCrud('Services', 'fas fa-envelope', Work::class);
        yield MenuItem::submenu('Blog', 'fas fa-blog')
        ->setSubItems([
            MenuItem::linkToCrud('Blog', 'fas fa-blog', Blog::class),
            MenuItem::linkToCrud('Topic', 'fas fa-blog', Topic::class),
        ]);
        yield MenuItem::linkToCrud('Projects', 'fas fa-tasks', Project::class);
        yield MenuItem::linkToCrud('Technologies', 'fas fa-microchip', Technology::class);
        yield MenuItem::linkToCrud('Tags', 'fas fa-tags', Tag::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-layer-group', Category::class);
        yield MenuItem::linkToCrud('Roles', 'fas fa-user-tie', Role::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
