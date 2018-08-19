<?php

namespace Yarsha\TagsBundle\Listener;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\TagsBundle\Event\TagEvent;
use Yarsha\TagsBundle\Event\PostEvent;
use Yarsha\TagsBundle\Service\TagService;

/**
 * Class TagEventListener
 * @package Yarsha\TagsBundle\Listener
 *
 * @DI\Service("yarsha.listener.tag")
 * @DI\Tag(name="kernel.event_listener", attributes={"event"="yarsha.event.add_edit_post", "method"="onPostUpdate"})
 * @DI\Tag(name="kernel.event_listener", attributes={"event"="yarsha.event.update_tag", "method"="onTagUpdate"})
 * @DI\Tag(name="kernel.event_listener", attributes={"event"="yarsha.event.delete_tag", "method"="onTagDelete"})
 */
class TagEventListener
{

    /**
     * @var TagService
     */
    private $tagService;

    /**
     * TagEventListener constructor.
     *
     * @DI\InjectParams({
     *      "tagService" = @DI\Inject("yarsha.service.tags")
     * })
     *
     * @param TagService $tagService
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function onPostUpdate(PostEvent $event)
    {
        $form = $event->getForm();
        $tags = isset($form['tags']) ? $form['tags']->getData() : '';

        $this->tagService->updateTags($event->getEntity(), $tags);
    }

    public function onTagUpdate(PostEvent $event)
    {
        $form = $event->getForm();

        $tag = $event->getEntity();

        $changeTo = isset($form['changeTo']) ? $form['changeTo']->getData() : null;

        if (!$changeTo) {
            $this->tagService->flush($tag);
        } else {
            $tagDescriptions = $this->tagService->getTagDescriptionsByTagId($tag->getId());

            if (count($tagDescriptions)) {
                foreach ($tagDescriptions as $td) {
                    $td->setTag($changeTo);
                    $this->tagService->flush($td);
                }
            }

            $this->tagService->removeEntity($tag);
        }
    }

    public function onTagDelete(TagEvent $event)
    {
        $tag = $event->getTag();

        $tagDescriptions = $this->tagService->getTagDescriptionsByTagId($tag->getId());

        if (count($tagDescriptions)) {
            foreach ($tagDescriptions as $td) {
                $this->tagService->removeEntity($td);
            }
        }

        $this->tagService->removeEntity($tag);

    }

}
