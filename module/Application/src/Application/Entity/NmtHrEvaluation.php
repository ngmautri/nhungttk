<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrEvaluation
 *
 * @ORM\Table(name="nmt_hr_evaluation", indexes={@ORM\Index(name="nmt_hr_employee_eval_FK1_idx", columns={"employee_id"})})
 * @ORM\Entity
 */
class NmtHrEvaluation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="eval_by", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $evalBy;

    /**
     * @var string
     *
     * @ORM\Column(name="eval_period", type="string", length=45, nullable=true)
     */
    private $evalPeriod;

    /**
     * @var integer
     *
     * @ORM\Column(name="eval_score", type="integer", nullable=true)
     */
    private $evalScore;

    /**
     * @var string
     *
     * @ORM\Column(name="eval_result", type="string", length=45, nullable=true)
     */
    private $evalResult;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="eval_on", type="datetime", nullable=true)
     */
    private $evalOn;

    /**
     * @var string
     *
     * @ORM\Column(name="old_contract_type", type="string", length=45, nullable=true)
     */
    private $oldContractType;

    /**
     * @var string
     *
     * @ORM\Column(name="new_contract_type", type="string", length=45, nullable=true)
     */
    private $newContractType;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var \Application\Entity\NmtHrEmployee
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrEmployee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     * })
     */
    private $employee;



    /**
     * Set id
     *
     * @param integer $id
     *
     * @return NmtHrEvaluation
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * Set evalBy
     *
     * @param integer $evalBy
     *
     * @return NmtHrEvaluation
     */
    public function setEvalBy($evalBy)
    {
        $this->evalBy = $evalBy;

        return $this;
    }

    /**
     * Get evalBy
     *
     * @return integer
     */
    public function getEvalBy()
    {
        return $this->evalBy;
    }

    /**
     * Set evalPeriod
     *
     * @param string $evalPeriod
     *
     * @return NmtHrEvaluation
     */
    public function setEvalPeriod($evalPeriod)
    {
        $this->evalPeriod = $evalPeriod;

        return $this;
    }

    /**
     * Get evalPeriod
     *
     * @return string
     */
    public function getEvalPeriod()
    {
        return $this->evalPeriod;
    }

    /**
     * Set evalScore
     *
     * @param integer $evalScore
     *
     * @return NmtHrEvaluation
     */
    public function setEvalScore($evalScore)
    {
        $this->evalScore = $evalScore;

        return $this;
    }

    /**
     * Get evalScore
     *
     * @return integer
     */
    public function getEvalScore()
    {
        return $this->evalScore;
    }

    /**
     * Set evalResult
     *
     * @param string $evalResult
     *
     * @return NmtHrEvaluation
     */
    public function setEvalResult($evalResult)
    {
        $this->evalResult = $evalResult;

        return $this;
    }

    /**
     * Get evalResult
     *
     * @return string
     */
    public function getEvalResult()
    {
        return $this->evalResult;
    }

    /**
     * Set evalOn
     *
     * @param \DateTime $evalOn
     *
     * @return NmtHrEvaluation
     */
    public function setEvalOn($evalOn)
    {
        $this->evalOn = $evalOn;

        return $this;
    }

    /**
     * Get evalOn
     *
     * @return \DateTime
     */
    public function getEvalOn()
    {
        return $this->evalOn;
    }

    /**
     * Set oldContractType
     *
     * @param string $oldContractType
     *
     * @return NmtHrEvaluation
     */
    public function setOldContractType($oldContractType)
    {
        $this->oldContractType = $oldContractType;

        return $this;
    }

    /**
     * Get oldContractType
     *
     * @return string
     */
    public function getOldContractType()
    {
        return $this->oldContractType;
    }

    /**
     * Set newContractType
     *
     * @param string $newContractType
     *
     * @return NmtHrEvaluation
     */
    public function setNewContractType($newContractType)
    {
        $this->newContractType = $newContractType;

        return $this;
    }

    /**
     * Get newContractType
     *
     * @return string
     */
    public function getNewContractType()
    {
        return $this->newContractType;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtHrEvaluation
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
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrEvaluation
     */
    public function setEmployee(\Application\Entity\NmtHrEmployee $employee = null)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \Application\Entity\NmtHrEmployee
     */
    public function getEmployee()
    {
        return $this->employee;
    }
}
