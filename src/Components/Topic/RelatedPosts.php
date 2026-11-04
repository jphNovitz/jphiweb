<?php

namespace App\Components\Topic;

use App\Entity\Blog;
use App\Entity\Topic;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/Topic/RelatedPosts.html.twig')]
class RelatedPosts
{
    public array $relatedPosts = [];
    public ?Topic $parentTopic = null;
    public array $childrenTopics = [];
    public int $maxPosts = 4;
    public int $maxChildrenTopics = 1;

    public function mount(Topic $topic): void
    {
        // Posts picked in EasyAdmin win: show only them, without automatic parent/child cards
        $pickedPosts = array_values(array_filter(
            $topic->getRelatedBlogs()->toArray(),
            fn (Blog $relatedPost) => $this->isOnline($relatedPost)
        ));
        if ($pickedPosts !== []) {
            $this->relatedPosts = array_slice($pickedPosts, 0, $this->maxPosts);

            return;
        }

        $parent = $topic->getParentTopic();
        $this->parentTopic = $parent !== null
        && $parent->isPublished()
        && !empty($parent->getSlug())
            ? $parent
            : null;

        $candidatesTopic = array_values(array_filter(
            $topic->getChildrenTopics()->toArray(),
            fn (Topic $childTopic) =>
                !empty($childTopic->getSlug())
                && $childTopic->isPublished()
        ));

        if ($candidatesTopic !== []) {
            shuffle($candidatesTopic);
            $this->childrenTopics = array_slice($candidatesTopic, 0, $this->maxChildrenTopics);
        }

        $maxPosts = $this->maxPosts
            - (int) ($this->parentTopic !== null)
            - count($this->childrenTopics);

        $candidates = array_values(array_filter(
            $topic->getArticles()->toArray(),
            fn (Blog $relatedPost) => $this->isOnline($relatedPost)
        ));

        shuffle($candidates);

        $this->relatedPosts = array_slice($candidates, 0, $maxPosts);
    }

    private function isOnline(Blog $post): bool
    {
        return !empty($post->getSlug()) && $post->isPublished();
    }
}
