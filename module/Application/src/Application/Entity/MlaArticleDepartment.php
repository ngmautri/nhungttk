<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaArticleDepartment
 *
 * @ORM\Table(name="mla_article_department", indexes={@ORM\Index(name="department_id_idx", columns={"department_id"}), @ORM\Index(name="mla_article_departments_FK3_idx", columns={"updated_by"}), @ORM\Index(name="mla_article_departmentt_FK2_idx", columns={"article_id"})})
 * @ORM\Entity
 */
class MlaArticleDepartment
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
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
     */
    private $updatedOn = 'CURRENT_TIMESTAMP';

    /**
     * @var \Application\Entity\MlaDepartments
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaDepartments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * })
     */
    private $department;

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
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return MlaArticleDepartment
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return \DateTime
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * Set department
     *
     * @param \Application\Entity\MlaDepartments $department
     *
     * @return MlaArticleDepartment
     */
    public function setDepartment(\Application\Entity\MlaDepartments $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Application\Entity\MlaDepartments
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set article
     *
     * @param \Application\Entity\MlaArticles $article
     *
     * @return MlaArticleDepartment
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
     * Set updatedBy
     *
     * @param \Application\Entity\MlaUsers $updatedBy
     *
     * @return MlaArticleDepartment
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
