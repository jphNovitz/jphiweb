<?php

namespace App\EventSubscriber;

use App\Enum\WorkType;
use App\Repository\BlogRepository;
use App\Repository\ProjectRepository;
use App\Repository\TopicRepository;
use App\Repository\WorkRepository;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly BlogRepository $blogRepository,
        private readonly TopicRepository $topicRepository,
        private readonly WorkRepository $workRepository,
        private readonly ProjectRepository $projectRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        $urls = $event->getUrlContainer();
        $router = $event->getUrlGenerator();
        $section = $event->getSection();

        // A null section means the whole sitemap is being generated
        if (in_array($section, [null, 'blog'], true)) {
            $this->registerTopicsUrls($urls, $router);
            $this->registerBlogPostsUrls($urls, $router);
        }

        if (in_array($section, [null, 'services'], true)) {
            $this->registerWorksUrls($urls, $router);
        }

        if (in_array($section, [null, 'realisations'], true)) {
            $this->registerProjectsUrls($urls, $router);
        }
    }

    private function registerTopicsUrls(UrlContainerInterface $urls, UrlGeneratorInterface $router): void
    {
        foreach ($this->topicRepository->findBy(['published' => true]) as $topic) {
            $urls->addUrl(
                new UrlConcrete(
                    $router->generate('app_topic_show', ['slug' => $topic->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    $topic->getUpdatedAt(),
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.8
                ),
                'blog'
            );
        }
    }

    private function registerBlogPostsUrls(UrlContainerInterface $urls, UrlGeneratorInterface $router): void
    {
        foreach ($this->blogRepository->findBy(['published' => true]) as $post) {
            // The show route needs the topic slug, posts without topic are not reachable
            if (null === $post->getTopic()) {
                continue;
            }

            $urls->addUrl(
                new UrlConcrete(
                    $router->generate('app_blog_show', [
                        'topicSlug' => $post->getTopic()->getSlug(),
                        'slug' => $post->getSlug(),
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                    $post->getUpdatedAt(),
                    UrlConcrete::CHANGEFREQ_MONTHLY,
                    0.7
                ),
                'blog'
            );
        }
    }

    private function registerWorksUrls(UrlContainerInterface $urls, UrlGeneratorInterface $router): void
    {
        foreach (WorkType::cases() as $type) {
            $urls->addUrl(
                new UrlConcrete(
                    $router->generate('app_work_type', ['type' => $type->value], UrlGeneratorInterface::ABSOLUTE_URL),
                    null,
                    UrlConcrete::CHANGEFREQ_MONTHLY,
                    0.8
                ),
                'services'
            );
        }

        foreach ($this->workRepository->findBy(['active' => true]) as $work) {
            if (null === $work->getType()) {
                continue;
            }

            $urls->addUrl(
                new UrlConcrete(
                    $router->generate('app_work_show', [
                        'type' => $work->getType()->value,
                        'slug' => $work->getSlug(),
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                    $work->getUpdatedAt(),
                    UrlConcrete::CHANGEFREQ_MONTHLY,
                    0.8
                ),
                'services'
            );
        }
    }

    private function registerProjectsUrls(UrlContainerInterface $urls, UrlGeneratorInterface $router): void
    {
        foreach ($this->projectRepository->findAll() as $project) {
            $urls->addUrl(
                new UrlConcrete(
                    $router->generate('app_project_show', ['slug' => $project->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    $project->getUpdatedAt(),
                    UrlConcrete::CHANGEFREQ_YEARLY,
                    0.6
                ),
                'realisations'
            );
        }
    }
}
