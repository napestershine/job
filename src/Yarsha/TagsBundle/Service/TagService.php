<?php

namespace Yarsha\TagsBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\MainBundle\Service\AbstractService;
use Yarsha\TagsBundle\Entity\Tag;
use Yarsha\TagsBundle\Entity\TagDescription;
use Yarsha\TagsBundle\Model\TaggableInterface;

/**
 * Class TagService
 * @package Yarsha\TagsBundle\Service
 *
 * @DI\Service("yarsha.service.tags", parent="yarsha.service.abstract")
 * @DI\Tag(name="yarsha.abstract_service")
 */
class TagService extends AbstractService
{


    public function getPopularTags($limit = 5)
    {
        return $this->getEntityManager()->getRepository(Tag::class)->getPopularTags($limit);
    }

    public function getTagById($id)
    {
        return $this->getEntityManager()->getRepository(Tag::class)->find($id);
    }

    /**
     * @param TaggableInterface $entity
     * @param string $tags
     */
    public function updateTags(TaggableInterface $entity, $tags = '')
    {

        $tagsDescriptions = $this->getEntityManager()->getRepository(TagDescription::class)->findBy([
            'entityClass' => get_class($entity),
            'entityId' => $entity->getId()
        ]);

        if (count($tagsDescriptions)) {
            foreach ($tagsDescriptions as $td) {
                $this->getEntityManager()->remove($td);
                $this->getEntityManager()->flush();
            }
        }
        if ($tags != '') {
            $tagsRepo = $this->getEntityManager()->getRepository(Tag::class);
            if (strpos($tags, ',') == true) {
                $inputTags = explode(',', $tags);
            } else {
                $inputTags[] = $tags;
            }
            foreach ($inputTags as $t) {
                $t = trim($t);

                $tag = $tagsRepo->findOneBy(['name' => $t]);

                if (!$tag) {
                    $tag = new Tag();
                    $tag->setName($t);
                }

                $tagDesc = new TagDescription();
                $tagDesc->setEntityId($entity->getId())
                    ->setTag($tag)
                    ->setEntityClass(get_class($entity));

                $tag->addDetail($tagDesc);
                $this->getEntityManager()->persist($tag);
                $this->getEntityManager()->persist($tagDesc);
                $this->getEntityManager()->flush();
            }
        }
    }

    public function getPaginatedTagsList($filters = [])
    {
        $result = $this->getEntityManager()->getRepository(Tag::class)->getAllTags($filters);

        return $this->getPaginationService()->getArrayPagerFanta($result);
    }

    /**
     * @param TaggableInterface $entity
     * @param $object boolean
     *
     * @return array
     */
    public function getTags(TaggableInterface $entity, $object = true)
    {
        $tagDescriptions = $this->getEntityManager()->getRepository(TagDescription::class)->findBy([
            'entityClass' => get_class($entity),
            'entityId' => $entity->getId()
        ]);

        $tags = [];


        if (count($tagDescriptions)) {
            foreach ($tagDescriptions as $tagDescription) {
                if ($tag = $tagDescription->getTag()) {
                    $tags[] = ($object) ? $tag : $tag->getName();
                }
            }
        }

        return $tags;
    }

    public function getTagsList($query = '')
    {
        return $this->getEntityManager()->getRepository(Tag::class)->listTags($query);
    }

    public function getPaginatedTagDescriptions($slug)
    {
        $qb = $this->getEntityManager()->getRepository(TagDescription::class)->listTagDescriptionsByTagQuery($slug);

        return $this->getPaginationService()->getPagerFanta($qb);
    }

    public function getTagBySlug($slug)
    {
        return $this->getEntityManager()->getRepository(Tag::class)->findOneBy(['slug' => $slug]);
    }

    public function getEntityByTagDescription(TagDescription $tagDescription)
    {
        return $this->getEntityManager()->getRepository($tagDescription->getEntityClass())->find($tagDescription->getEntityId());
    }

    public function getEntityDetailFromTagDescription(TagDescription $tagDescription)
    {
        $detail = [];

        $entity = $this->getEntityManager()->getRepository($tagDescription->getEntityClass())->find($tagDescription->getEntityId());

        if (!$entity) {
            return $detail;
        }

        $detail['title'] = $entity->getTitle();
        $detail['slug'] = $entity->getSlug();
        $detail['postedDate'] = $entity->getPostedDate();

        $detail['featuredImage'] = $entity->getListingImage();
        $detail['description'] = $entity->getDetail();
        $detail['status'] = $entity->getStatus();


        return $detail;
    }

    public function flush($entity = null)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function getTagDescriptionsByTagId($tagId)
    {
        return $this->getEntityManager()->getRepository(TagDescription::class)->findBy(['tag' => $tagId]);
    }

    public function removeEntity($entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

}
