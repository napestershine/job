<?php

namespace Yarsha\ArticleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Yarsha\AdminBundle\Entity\User;
use Yarsha\TagsBundle\Model\TaggableInterface;

/**
 * Article
 *
 * @ORM\Table(name="ys_articles")
 * @ORM\Entity(repositoryClass="Yarsha\ArticleBundle\Repository\ArticleRepository")
 */
class Article implements TaggableInterface
{

    const ARTICLE_STATUS_PUBLISHED = 1;

    const ARTICLE_STATUS_DRAFT = 2;

    const ARTICLE_STATUS_DELETED = 3;

    const ARTICLE_TYPE_ARTICLE = 1;

    const ARTICLE_TYPE_NEWS = 2;

    const ARTICLE_TYPE_BLOG = 3;

    const ARTICLE_CATEGORY_ALL = 11;

    const ARTICLE_CATEGORY_CMS_PAGE = 1;

    const ARTICLE_CATEGORY_NEWS = 10;

    const ARTICLE_CATEGORY_EMPLOYER_SERVICES = 2;

    const ARTICLE_CATEGORY_EMPLOYEE_SERVICES = 3;

    const ARTICLE_CATEGORY_HR_ISSUES = 4;

    const ARTICLE_CATEGORY_CAREER_RESOURCES = 5;

    const ARTICLE_CATEGORY_TRAINING = 6;

    const ARTICLE_CATEGORY_CORPORATE_SERVICES = 7;

    const ARTICLE_CATEGORY_PAGE_BLOCK = 8;

    const ARTICLE_CATEGORY_LOKSEWA = 9;

    public static $articleCategories = [
        self::ARTICLE_CATEGORY_ALL => 'All',
        self::ARTICLE_CATEGORY_NEWS => 'News',
        self::ARTICLE_CATEGORY_CMS_PAGE => 'CMS Page',
        self::ARTICLE_CATEGORY_EMPLOYER_SERVICES => 'Employer Services',
        self::ARTICLE_CATEGORY_EMPLOYEE_SERVICES => 'Employee Services',
        self::ARTICLE_CATEGORY_HR_ISSUES => 'HR Issues',
        self::ARTICLE_CATEGORY_CAREER_RESOURCES => 'Career Resources',
        self::ARTICLE_CATEGORY_TRAINING => 'Training',
        self::ARTICLE_CATEGORY_CORPORATE_SERVICES => 'Corporate Services',
        self::ARTICLE_CATEGORY_PAGE_BLOCK => 'Page Block',
        self::ARTICLE_CATEGORY_LOKSEWA => 'Resources Center',
    ];

    public static $articleStatusOptions = [
        self::ARTICLE_STATUS_PUBLISHED => 'Published',
        self::ARTICLE_STATUS_DRAFT => 'Draft'
    ];

    public static $articlesCategoryDescForUrl = [
        self::ARTICLE_CATEGORY_ALL => 'articles',
        self::ARTICLE_CATEGORY_EMPLOYER_SERVICES => 'employer-services',
        self::ARTICLE_CATEGORY_EMPLOYEE_SERVICES => 'jobseeker-services',
        self::ARTICLE_CATEGORY_HR_ISSUES => 'hr-issues',
        self::ARTICLE_CATEGORY_CAREER_RESOURCES => 'career-resources',
        self::ARTICLE_CATEGORY_TRAINING => 'trainings',
        self::ARTICLE_CATEGORY_CORPORATE_SERVICES => 'corporate-services',
        self::ARTICLE_CATEGORY_LOKSEWA => 'resources-center',
        self::ARTICLE_CATEGORY_NEWS => 'news',
        self::ARTICLE_CATEGORY_CMS_PAGE => 'cms-page',
        self::ARTICLE_CATEGORY_PAGE_BLOCK => 'page-block',
    ];


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="category", type="integer")
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="link_text", type="string", length=255, nullable=true)
     */
    private $linkText;

    /**
     * @var string
     *
     * @ORM\Column(name="link_url", type="string", length=255, nullable=true)
     */
    private $linkUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = self::ARTICLE_STATUS_DRAFT;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = false;

    /**
     * @var int
     *
     * @ORM\Column(name="hits", type="integer", nullable=true)
     */
    private $hits;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\AdminBundle\Entity\User")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keywords", type="string", length=255, nullable=true)
     */
    private $metaKeywords;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_descriptions", type="string", length=255, nullable=true)
     */
    private $metaDescriptions;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_tags", type="string", length=255, nullable=true)
     */
    private $metaTags;


    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", nullable=true)
     */
    private $tags;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Article
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set category
     *
     * @param integer $category
     *
     * @return Article
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Article
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set linkText
     *
     * @param string $linkText
     *
     * @return Article
     */
    public function setLinkText($linkText)
    {
        $this->linkText = $linkText;

        return $this;
    }

    /**
     * Get linkText
     *
     * @return string
     */
    public function getLinkText()
    {
        return $this->linkText;
    }

    /**
     * Set linkUrl
     *
     * @param string $linkUrl
     *
     * @return Article
     */
    public function setLinkUrl($linkUrl)
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    /**
     * Get linkUrl
     *
     * @return string
     */
    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Article
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Article
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set hits
     *
     * @param integer $hits
     *
     * @return Article
     */
    public function setHits($hits)
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * Get hits
     *
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Article
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     *
     * @return Article
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Article
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }


    /**
     * Set metaKeywords
     *
     * @param string $metaKeywords
     *
     * @return Article
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set metaDescriptions
     *
     * @param string $metaDescriptions
     *
     * @return Article
     */
    public function setMetaDescriptions($metaDescriptions)
    {
        $this->metaDescriptions = $metaDescriptions;

        return $this;
    }

    /**
     * Get metaDescriptions
     *
     * @return string
     */
    public function getMetaDescriptions()
    {
        return $this->metaDescriptions;
    }

    /**
     * Set metaTags
     *
     * @param string $metaTags
     *
     * @return Article
     */
    public function setMetaTags($metaTags)
    {
        $this->metaTags = $metaTags;

        return $this;
    }

    /**
     * Get metaTags
     *
     * @return string
     */
    public function getMetaTags()
    {
        return $this->metaTags;
    }

    public function __toString()
    {

        return $this->title;
    }

    /**
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return $this
     */
    public function increaseHits()
    {
        $this->hits = $this->hits + 1;

        return $this;
    }


}

