<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Blog\BlogCrudController;
use App\Controller\Admin\Blog\TopicCrudController;
use App\Controller\Admin\Message\MessageCrudController;
use App\Controller\Admin\Project\CategoryCrudController;
use App\Controller\Admin\Project\ProjectCrudController;
use App\Controller\Admin\Project\RoleCrudController;
use App\Controller\Admin\Project\TagCrudController;
use App\Controller\Admin\Project\TechnologyCrudController;
use App\Controller\Admin\Work\WorkCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
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
        yield MenuItem::linkTo(MessageCrudController::class, 'Messages', 'fas fa-envelope');
        yield MenuItem::linkTo(WorkCrudController::class, 'Services', 'fas fa-envelope');
        yield MenuItem::submenu('Blog', 'fas fa-blog')
        ->setSubItems([
            MenuItem::linkTo(BlogCrudController::class, 'Blog', 'fas fa-blog'),
            MenuItem::linkTo(TopicCrudController::class, 'Topic', 'fas fa-blog'),
        ]);
        yield MenuItem::linkTo(ProjectCrudController::class, 'Projects', 'fas fa-tasks');
        yield MenuItem::linkTo(TechnologyCrudController::class, 'Technologies', 'fas fa-microchip');
        yield MenuItem::linkTo(TagCrudController::class, 'Tags', 'fas fa-tags');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Categories', 'fas fa-layer-group');
        yield MenuItem::linkTo(RoleCrudController::class, 'Roles', 'fas fa-user-tie');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
