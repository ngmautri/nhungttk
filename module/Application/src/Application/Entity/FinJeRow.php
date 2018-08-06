<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinJeRow
 *
 * @ORM\Table(name="fin_je_row", indexes={@ORM\Index(name="fin_je_row_FK1_idx", columns={"je_id"}), @ORM\Index(name="fin_je_row_FK2_idx", columns={"GL_account_id"}), @ORM\Index(name="fin_je_row_FK3_idx", columns={"created_by"}), @ORM\Index(name="fin_je_row_FK4_idx", columns={"sub_account_id"})})
 * @ORM\Entity
 */
class FinJeRow
{
    /**
     * @var integer
     *
     * @ORM\Column(name="int", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $int;

    /**
     * @var string
     *
     * @ORM\Column(name="posting_key", type="string", length=5, nullable=false)
     */
    private $postingKey;

    /**
     * @var string
     *
     * @ORM\Column(name="posting_code", type="string", length=5, nullable=false)
     */
    private $postingCode;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_amount", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $docAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="local_amount", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $localAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_center", type="string", length=45, nullable=true)
     */
    private $costCenter;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_id", type="integer", nullable=true)
     */
    private $sourceId;

    /**
     * @var string
     *
     * @ORM\Column(name="source_class", type="string", length=255, nullable=true)
     */
    private $sourceClass;

    /**
     * @var string
     *
     * @ORM\Column(name="source_token", type="string", length=45, nullable=true)
     */
    private $sourceToken;

    /**
     * @var \Application\Entity\FinJe
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinJe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="je_id", referencedColumnName="id")
     * })
     */
    private $je;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="GL_account_id", referencedColumnName="id")
     * })
     */
    private $glAccount;

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
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sub_account_id", referencedColumnName="id")
     * })
     */
    private $subAccount;



    /**
     * Get int
     *
     * @return integer
     */
    public function getInt()
    {
        return $this->int;
    }

    /**
     * Set postingKey
     *
     * @param string $postingKey
     *
     * @return FinJeRow
     */
    public function setPostingKey($postingKey)
    {
        $this->postingKey = $postingKey;

        return $this;
    }

    /**
     * Get postingKey
     *
     * @return string
     */
    public function getPostingKey()
    {
        return $this->postingKey;
    }

    /**
     * Set postingCode
     *
     * @param string $postingCode
     *
     * @return FinJeRow
     */
    public function setPostingCode($postingCode)
    {
        $this->postingCode = $postingCode;

        return $this;
    }

    /**
     * Get postingCode
     *
     * @return string
     */
    public function getPostingCode()
    {
        return $this->postingCode;
    }

    /**
     * Set docAmount
     *
     * @param string $docAmount
     *
     * @return FinJeRow
     */
    public function setDocAmount($docAmount)
    {
        $this->docAmount = $docAmount;

        return $this;
    }

    /**
     * Get docAmount
     *
     * @return string
     */
    public function getDocAmount()
    {
        return $this->docAmount;
    }

    /**
     * Set localAmount
     *
     * @param string $localAmount
     *
     * @return FinJeRow
     */
    public function setLocalAmount($localAmount)
    {
        $this->localAmount = $localAmount;

        return $this;
    }

    /**
     * Get localAmount
     *
     * @return string
     */
    public function getLocalAmount()
    {
        return $this->localAmount;
    }

    /**
     * Set costCenter
     *
     * @param string $costCenter
     *
     * @return FinJeRow
     */
    public function setCostCenter($costCenter)
    {
        $this->costCenter = $costCenter;

        return $this;
    }

    /**
     * Get costCenter
     *
     * @return string
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return FinJeRow
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
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return FinJeRow
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    /**
     * Get sourceId
     *
     * @return integer
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * Set sourceClass
     *
     * @param string $sourceClass
     *
     * @return FinJeRow
     */
    public function setSourceClass($sourceClass)
    {
        $this->sourceClass = $sourceClass;

        return $this;
    }

    /**
     * Get sourceClass
     *
     * @return string
     */
    public function getSourceClass()
    {
        return $this->sourceClass;
    }

    /**
     * Set sourceToken
     *
     * @param string $sourceToken
     *
     * @return FinJeRow
     */
    public function setSourceToken($sourceToken)
    {
        $this->sourceToken = $sourceToken;

        return $this;
    }

    /**
     * Get sourceToken
     *
     * @return string
     */
    public function getSourceToken()
    {
        return $this->sourceToken;
    }

    /**
     * Set je
     *
     * @param \Application\Entity\FinJe $je
     *
     * @return FinJeRow
     */
    public function setJe(\Application\Entity\FinJe $je = null)
    {
        $this->je = $je;

        return $this;
    }

    /**
     * Get je
     *
     * @return \Application\Entity\FinJe
     */
    public function getJe()
    {
        return $this->je;
    }

    /**
     * Set glAccount
     *
     * @param \Application\Entity\FinAccount $glAccount
     *
     * @return FinJeRow
     */
    public function setGlAccount(\Application\Entity\FinAccount $glAccount = null)
    {
        $this->glAccount = $glAccount;

        return $this;
    }

    /**
     * Get glAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return FinJeRow
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

    /**
     * Set subAccount
     *
     * @param \Application\Entity\FinAccount $subAccount
     *
     * @return FinJeRow
     */
    public function setSubAccount(\Application\Entity\FinAccount $subAccount = null)
    {
        $this->subAccount = $subAccount;

        return $this;
    }

    /**
     * Get subAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getSubAccount()
    {
        return $this->subAccount;
    }
}
