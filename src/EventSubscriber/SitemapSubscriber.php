<?php

namespace App\EventSubscriber;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use App\Repository\ProjectRepository;
use App\Repository\TopicRepository;
use App\Repository\WorkRepository;
use App\Service\PageLastModified;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Google ignores changefreq and priority, so entries only carry an accurate lastmod,
 * computed like the HTTP cache validators of the pages.
 */
class SitemapSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly BlogRepository $blogRepository,
        private readonly TopicRepository $topicRepository,
        private readonly WorkRepository $workRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly PageLastModified $pageLastModified,
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
        if (in_array($section, [null, 'pages'], true)) {
            $this->registerStaticPagesUrls($urls, $router);
        }

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

    private function registerStaticPagesUrls(UrlContainerInterface $urls, UrlGeneratorInterface $router): void
    {
        $pinnedBlogs = $this->blogRepository->findBy(['pin' => true, 'published' => true]);

        $pages = [
            'app_home' => $this->pageLastModified->home($pinnedBlogs),
            'app_work_index' => $this->pageLastModified->compute(
                ['work/index.html.twig'],
                $this->workRepository->findBy(['active' => true])
            ),
            'app_project_list' => $this->pageLastModified->compute(
                ['project/index.html.twig'],
                $this->projectRepository->findAll()
            ),
            'app_contact' => $this->pageLastModified->compute(['contact/*.twig']),
            'app_mentions_legales' => $this->pageLastModified->compute(['home/mentions-legales.html.twig']),
        ];

        foreach ($pages as $route => $lastModified) {
            $urls->addUrl(
                new UrlConcrete($router->generate($route, [], UrlGeneratorInterface::ABSOLUTE_URL), $lastModified),
                'pages'
            );
        }
    }

    private function registerTopicsUrls(UrlContainerInterface $urls, UrlGeneratorInterface $router): void
    {
        foreach ($this->topicRepository->findBy(['published' => true]) as $topic) {
            // The topic page lists its posts, so a post update also updates the topic
            $articles = array_filter(
                $topic->getArticles()->toArray(),
                fn (Blog $post) => $post->isPublished()
            );

            $urls->addUrl(
                new UrlConcrete(
                    $router->generate('app_topic_show', ['slug' => $topic->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    $this->latestUpdate([$topic, ...$articles])
                ),
                'blog'
            );
        }
    }

    private function registerBlogPostsUrls(UrlContainerInterface $urls, UrlGeneratorInterface $router): void
    {
        foreach ($this->blogRepository->findPublishedWithTopic() as $post) {
            $urls->addUrl(
                new UrlConcrete(
                    $router->generate('app_blog_show', [
                        'topicSlug' => $post->getTopic()->getSlug(),
                        'slug' => $post->getSlug(),
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                    $post->getUpdatedAt()
                ),
                'blog'
            );
        }
    }

    private function registerWorksUrls(UrlContainerInterface $urls, UrlGeneratorInterface $router): void
    {
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
                    $work->getUpdatedAt()
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
                    $project->getUpdatedAt()
                ),
                'realisations'
            );
        }
    }

    private function latestUpdate(array $entities): ?\DateTimeInterface
    {
        $latest = null;
        foreach ($entities as $entity) {
            if ($entity->getUpdatedAt() > $latest) {
                $latest = $entity->getUpdatedAt();
            }
        }

        return $latest;
    }
}
