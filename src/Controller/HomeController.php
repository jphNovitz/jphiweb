<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {

//        $message = new Message();
//        $message->setSeen(false);
//
//        $form = $this->createForm(MessageType::class, $message);
//
//        $form->handleRequest($request);
//
//        // dd($form->isValid());
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em->persist($message);
//            $em->flush();
//
//            $this->addFlash('success', 'Your message have been send');
//            return $this->redirectToRoute('app_home');
//        }

        $pinnedBlogs = $em->getRepository(Blog::class)->findBy(['pin' => true, 'published' => true]);

        // Validators follow the templates (updated by git pull on deploy) and the pinned posts
        $lastModified = $this->getTemplatesLastModified();
        foreach ($pinnedBlogs as $blog) {
            if ($blog->getUpdatedAt() > $lastModified) {
                $lastModified = $blog->getUpdatedAt();
            }
        }
        $etag = md5($request->getPathInfo() . $lastModified->format('U') . implode(',', array_map(
            fn (Blog $blog) => $blog->getId(),
            $pinnedBlogs
        )));

        $response = new Response();
        $response->setPublic();
        $response->setLastModified($lastModified);
        $response->setEtag($etag);
        if ($response->isNotModified($request)) {
            return $response;
        }

        $response = $this->render('home/index.html.twig', [
            'pinned' => $pinnedBlogs,
//            'form' => $form->createView(),
        ]);

        // // cache publicly for 3600 seconds
        // $response->setPublic();
        // $response->setMaxAge(3600);
        // use this method to set several cache settings in one call
        // (this example lists all the available cache settings)
        $response->setCache([
            'public'        => true,
            'max_age'       => 86400,     // 24h
            'must_revalidate' => true,
            'last_modified' => $lastModified,
            'etag'          => $etag,
        ]);
        // (optional) set a custom Cache-Control directive
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;

        // return $this->render('home/index.html.twig', [
        //     'form' => $form->createView(),
        // ]);
    }

    private function getTemplatesLastModified(): \DateTime
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $files = array_merge(
            glob($projectDir . '/templates/base.html.twig'),
            glob($projectDir . '/templates/home/*.twig'),
            glob($projectDir . '/templates/_partials/*.twig'),
            glob($projectDir . '/templates/components/*.twig'),
            glob($projectDir . '/templates/components/*/*.twig'),
            glob($projectDir . '/translations/*.yaml'),
        );

        $timestamp = max(array_map('filemtime', $files) ?: [0]);

        return (new \DateTime())->setTimestamp($timestamp);
    }

    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function legalNotice(): Response
    {
        return $this->render('home/mentions-legales.html.twig');
    }
}
