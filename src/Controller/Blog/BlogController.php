<?php

namespace App\Controller\Blog;

use App\Entity\Blog;
use App\Entity\Topic;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/{slug}', name: 'app_topic_show', requirements: ['slug' => '^(?!admin).*'], priority: -10)]
    public function showTopic(Topic $topic): Response
    {
        if (!$topic->isPublished()) {
            throw new GoneHttpException();
        }
        return $this->render('blog/topic/show.html.twig', [
            'topic' => $topic,
        ]);
    }
    #[Route('/focus', name: 'app_blog_index')]
    public function index(EntityManagerInterface $entityManager,
                          PaginatorInterface $paginator,
                          TopicRepository $topicRepository,
                          Request $request): Response
    {
        $posts = $paginator->paginate(
            $entityManager->getRepository(Blog::class)->findAllQuery(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        $topics = $topicRepository->findAll();



        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
            'topics' => $topics,
        ]);
    }


    #[Route('/{topicSlug}/{slug}', name: 'app_blog_show', requirements: [
        'topicSlug' => 'creation-site-internet-brabant-wallon|creation-site-internet-court-saint-etienne|creation-site-web-liege|web-conseils|symfony-et-ses-outils',
        'slug' => '[a-z0-9\-]+'
    ])]
    public function show(string $topicSlug, Blog $post): Response
    {
        if (!$post->isPublished()) {
            throw new GoneHttpException();
        }

        if (!$post->getTopic()) {
            throw $this->createNotFoundException();
        }

        if ($post->getTopic()->getSlug() !== $topicSlug) {
            return $this->redirectToRoute('app_blog_show', [
                'topicSlug' => $post->getTopic()->getSlug(),
                'slug' => $post->getSlug(),
            ], Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->render('blog/show.html.twig', [
            'post' => $post,
        ]);
    }
    #[Route('/focus-sur/{slug}', name: 'app_blog_redirect_legacy')]
    public function redirectLegacy(Blog $post): Response
    {
        if (!$post->getTopic()) {
            throw $this->createNotFoundException();
        }

        return $this->redirectToRoute('app_blog_show', [
            'topicSlug' => $post->getTopic()->getSlug(),
            'slug' => $post->getSlug(),
        ], 301);
    }


}
