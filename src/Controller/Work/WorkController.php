<?php

namespace App\Controller\Work;

use App\Entity\Work;
use App\Enum\WorkType;
use App\Repository\WorkRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkController extends AbstractController
{
    // src/Controller/WorkController.php

    #[Route('/services', name: 'app_work_index')]
    public function index(WorkRepository $repo): Response
    {
        return $this->render('work/index.html.twig');
    }

    #[Route('/services/{type}', name: 'app_work_type')]
    public function byType(string $type, WorkRepository $repo): Response
    {
        $workType = WorkType::tryFrom($type);

        if (!$workType) {
            throw $this->createNotFoundException();
        }

        $works = $repo->findActive($workType);

        return $this->render('work/type/index.html.twig', [
            'works' => $works,
            'type'  => $workType,
        ]);
    }

    #[Route('/services/{type}/{slug}', name: 'app_work_show')]
    public function show(string $type, string $slug, WorkRepository $repo): Response
    {
        $work = $repo->findOneBy(['slug' => $slug, 'active' => true]);

        if (!$work) {
            throw $this->createNotFoundException();
        }

        if ($work->getType() && $work->getType()->value !== $type) {
            return $this->redirectToRoute('app_work_show', [
                'type' => $work->getType()->value,
                'slug' => $work->getSlug(),
            ], Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->render('work/show.html.twig', [
            'work' => $work,
        ]);
    }

}
