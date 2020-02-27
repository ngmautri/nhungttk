<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationDocType
 *
 * @ORM\Table(name="nmt_application_doc_type", indexes={@ORM\Index(name="nmt_application_doc_type_FK1_idx", columns={"doc_range_number_id"}), @ORM\Index(name="nmt_application_doc_type_FK2_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class NmtApplicationDocType
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
     * @var string
     *
     * @ORM\Column(name="doc_type_code", type="string", length=10, nullable=false)
     */
    private $docTypeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_type_name", type="string", length=45, nullable=false)
     */
    private $docTypeName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=45, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_prefix", type="string", length=10, nullable=true)
     */
    private $docPrefix;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_suffix", type="string", length=45, nullable=true)
     */
    private $docSuffix;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

    /**
     * @var \Application\Entity\NmtApplicationDocRangeNumber
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationDocRangeNumber")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doc_range_number_id", referencedColumnName="id")
     * })
     */
    private $docRangeNumber;

    /**
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;



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
     * Set docTypeCode
     *
     * @param string $docTypeCode
     *
     * @return NmtApplicationDocType
     */
    public function setDocTypeCode($docTypeCode)
    {
        $this->docTypeCode = $docTypeCode;

        return $this;
    }

    /**
     * Get docTypeCode
     *
     * @return string
     */
    public function getDocTypeCode()
    {
        return $this->docTypeCode;
    }

    /**
     * Set docTypeName
     *
     * @param string $docTypeName
     *
     * @return NmtApplicationDocType
     */
    public function setDocTypeName($docTypeName)
    {
        $this->docTypeName = $docTypeName;

        return $this;
    }

    /**
     * Get docTypeName
     *
     * @return string
     */
    public function getDocTypeName()
    {
        return $this->docTypeName;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return NmtApplicationDocType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set docPrefix
     *
     * @param string $docPrefix
     *
     * @return NmtApplicationDocType
     */
    public function setDocPrefix($docPrefix)
    {
        $this->docPrefix = $docPrefix;

        return $this;
    }

    /**
     * Get docPrefix
     *
     * @return string
     */
    public function getDocPrefix()
    {
        return $this->docPrefix;
    }

    /**
     * Set docSuffix
     *
     * @param string $docSuffix
     *
     * @return NmtApplicationDocType
     */
    public function setDocSuffix($docSuffix)
    {
        $this->docSuffix = $docSuffix;

        return $this;
    }

    /**
     * Get docSuffix
     *
     * @return string
     */
    public function getDocSuffix()
    {
        return $this->docSuffix;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return NmtApplicationDocType
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtApplicationDocType
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
     * Set docRangeNumber
     *
     * @param \Application\Entity\NmtApplicationDocRangeNumber $docRangeNumber
     *
     * @return NmtApplicationDocType
     */
    public function setDocRangeNumber(\Application\Entity\NmtApplicationDocRangeNumber $docRangeNumber = null)
    {
        $this->docRangeNumber = $docRangeNumber;

        return $this;
    }

    /**
     * Get docRangeNumber
     *
     * @return \Application\Entity\NmtApplicationDocRangeNumber
     */
    public function getDocRangeNumber()
    {
        return $this->docRangeNumber;
    }

    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtApplicationDocType
     */
    public function setCompany(\Application\Entity\NmtApplicationCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\Entity\NmtApplicationCompany
     */
    public function getCompany()
    {
        return $this->company;
    }
}
