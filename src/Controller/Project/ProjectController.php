<?php

namespace App\Controller\Project;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectController extends AbstractController
{
    #[Route('/realisations', name: 'app_project_list')]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findBy(['published' => true]);
//        dd($projects);
        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/realisations/{slug}', name: 'app_project_show')]
    public function show(#[MapEntity(mapping: ['slug' => 'slug'])] Project $project): Response
    {
        if (!$project->isPublished()) {
            throw new GoneHttpException();
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }
}
