<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaArticlesMeasurements
 *
 * @ORM\Table(name="mla_articles_measurements")
 * @ORM\Entity
 */
class MlaArticlesMeasurements
{
    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="string", length=45, nullable=true)
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="length", type="string", length=45, nullable=true)
     */
    private $length;

    /**
     * @var \Application\Entity\MlaArticles
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Application\Entity\MlaArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     * })
     */
    private $article;



    /**
     * Set weight
     *
     * @param string $weight
     *
     * @return MlaArticlesMeasurements
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set length
     *
     * @param string $length
     *
     * @return MlaArticlesMeasurements
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return string
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set article
     *
     * @param \Application\Entity\MlaArticles $article
     *
     * @return MlaArticlesMeasurements
     */
    public function setArticle(\Application\Entity\MlaArticles $article)
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
}
