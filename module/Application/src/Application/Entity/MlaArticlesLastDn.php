<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaArticlesLastDn
 *
 * @ORM\Table(name="mla_articles_last_dn", uniqueConstraints={@ORM\UniqueConstraint(name="article_id_UNIQUE", columns={"article_id"})}, indexes={@ORM\Index(name="mla_articles_last_dn_FK1_idx", columns={"article_id"}), @ORM\Index(name="mla_articles_last_dn_FK2_idx", columns={"last_workflow_id"})})
 * @ORM\Entity
 */
class MlaArticlesLastDn
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Application\Entity\MlaArticles
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     * })
     */
    private $article;

    /**
     * @var \Application\Entity\MlaDeliveryItemsWorkflows
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaDeliveryItemsWorkflows")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_workflow_id", referencedColumnName="id")
     * })
     */
    private $lastWorkflow;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set article
     *
     * @param \Application\Entity\MlaArticles $article
     *
     * @return MlaArticlesLastDn
     */
    public function setArticle(\Application\Entity\MlaArticles $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \Application\Entity\MlaArticles
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set lastWorkflow
     *
     * @param \Application\Entity\MlaDeliveryItemsWorkflows $lastWorkflow
     *
     * @return MlaArticlesLastDn
     */
    public function setLastWorkflow(\Application\Entity\MlaDeliveryItemsWorkflows $lastWorkflow = null)
    {
        $this->lastWorkflow = $lastWorkflow;

        return $this;
    }

    /**
     * Get lastWorkflow
     *
     * @return \Application\Entity\MlaDeliveryItemsWorkflows
     */
    public function getLastWorkflow()
    {
        return $this->lastWorkflow;
    }
}
