<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaArticlesCategoriesMembers
 *
 * @ORM\Table(name="mla_articles_categories_members", indexes={@ORM\Index(name="mla_articles_categories_members_FK1_idx", columns={"article_id"}), @ORM\Index(name="mla_articles_categories_members_FK2_idx", columns={"article_cat_id"}), @ORM\Index(name="mla_articles_categories_members_FK2_idx1", columns={"updated_by"})})
 * @ORM\Entity
 */
class MlaArticlesCategoriesMembers
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
     * @var \Application\Entity\MlaArticlesCategories
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaArticlesCategories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="article_cat_id", referencedColumnName="id")
     * })
     */
    private $articleCat;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * })
     */
    private $updatedBy;



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
     * @return MlaArticlesCategoriesMembers
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
     * Set articleCat
     *
     * @param \Application\Entity\MlaArticlesCategories $articleCat
     *
     * @return MlaArticlesCategoriesMembers
     */
    public function setArticleCat(\Application\Entity\MlaArticlesCategories $articleCat = null)
    {
        $this->articleCat = $articleCat;

        return $this;
    }

    /**
     * Get articleCat
     *
     * @return \Application\Entity\MlaArticlesCategories
     */
    public function getArticleCat()
    {
        return $this->articleCat;
    }

    /**
     * Set updatedBy
     *
     * @param \Application\Entity\MlaUsers $updatedBy
     *
     * @return MlaArticlesCategoriesMembers
     */
    public function setUpdatedBy(\Application\Entity\MlaUsers $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
