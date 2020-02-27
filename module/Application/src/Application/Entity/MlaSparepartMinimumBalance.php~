<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaSparepartMinimumBalance
 *
 * @ORM\Table(name="mla_sparepart_minimum_balance", indexes={@ORM\Index(name="mla_sparepart_minimum_balance_FK1_idx", columns={"sparepart_id"}), @ORM\Index(name="mla_sparepart_minimum_balance_FK2_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class MlaSparepartMinimumBalance
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
     * @var integer
     *
     * @ORM\Column(name="minimum_balance", type="integer", nullable=false)
     */
    private $minimumBalance;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var \Application\Entity\MlaSpareparts
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaSpareparts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sparepart_id", referencedColumnName="id")
     * })
     */
    private $sparepart;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;



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
     * Set minimumBalance
     *
     * @param integer $minimumBalance
     *
     * @return MlaSparepartMinimumBalance
     */
    public function setMinimumBalance($minimumBalance)
    {
        $this->minimumBalance = $minimumBalance;

        return $this;
    }

    /**
     * Get minimumBalance
     *
     * @return integer
     */
    public function getMinimumBalance()
    {
        return $this->minimumBalance;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return MlaSparepartMinimumBalance
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return MlaSparepartMinimumBalance
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return MlaSparepartMinimumBalance
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set sparepart
     *
     * @param \Application\Entity\MlaSpareparts $sparepart
     *
     * @return MlaSparepartMinimumBalance
     */
    public function setSparepart(\Application\Entity\MlaSpareparts $sparepart = null)
    {
        $this->sparepart = $sparepart;

        return $this;
    }

    /**
     * Get sparepart
     *
     * @return \Application\Entity\MlaSpareparts
     */
    public function getSparepart()
    {
        return $this->sparepart;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return MlaSparepartMinimumBalance
     */
    public function setCreatedBy(\Application\Entity\MlaUsers $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
