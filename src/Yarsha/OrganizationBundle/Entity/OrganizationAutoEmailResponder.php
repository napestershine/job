<?php

namespace Yarsha\OrganizationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yarsha\AdminBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * OrganizationAutoEmailResponder
 *
 * @ORM\Table(name="ys_organization_email_responder")
 * @ORM\Entity(repositoryClass="Yarsha\OrganizationBundle\Repository\OrganizationAutoEmailResponderRepository")
 */
class OrganizationAutoEmailResponder
{

    const ENABLE = true;

    const DISABLE = false;


    public static $emailResponseStatus = [
        self::ENABLE => 'Enable',
        self::DISABLE => 'Disable',
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
     * @ORM\Column(name="auto_response_text", type="text", nullable=true)
     */
    private $autoresponsetext;

    /**
     * @var int
     *
     * @ORM\Column(name="auto_response", type="integer", length=1, nullable=true)
     */
    private $autoresponse = false;

    /**
     * @var int
     *
     * @ORM\Column(name="organization_id", type="integer", nullable=false)
     */
    private $organizationId;


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
     * Set autoresponsetext
     *
     * @param string $autoresponsetext
     *
     * @return OrganizationAutoEmailResponder
     */
    public function setAutoResponseText($autoresponsetext)
    {
        $this->autoresponsetext = $autoresponsetext;

        return $this;
    }

    /**
     * Get autoresponsetext
     *
     * @return string
     */
    public function getAutoResponseText()
    {
        return $this->autoresponsetext;
    }


    /**
     * Set autoresponse
     *
     * @param int $autoresponse
     *
     * @return OrganizationType
     */
    public function setAutoResponse($autoresponse)
    {
        $this->autoresponse = $autoresponse;

        return $this;
    }

    /**
     * Get autoresponse
     *
     * @return int
     */
    public function getAutoResponse()
    {
        return $this->autoresponse;
    }

    /**
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }


}

