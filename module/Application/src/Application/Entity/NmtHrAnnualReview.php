<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrAnnualReview
 *
 * @ORM\Table(name="nmt_hr_annual_review")
 * @ORM\Entity
 */
class NmtHrAnnualReview
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
     * @ORM\Column(name="employee", type="integer", nullable=false)
     */
    private $employee;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="review_period_from", type="datetime", nullable=true)
     */
    private $reviewPeriodFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="review_period_to", type="datetime", nullable=true)
     */
    private $reviewPeriodTo;



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
     * Set employee
     *
     * @param integer $employee
     *
     * @return NmtHrAnnualReview
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return integer
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set reviewPeriodFrom
     *
     * @param \DateTime $reviewPeriodFrom
     *
     * @return NmtHrAnnualReview
     */
    public function setReviewPeriodFrom($reviewPeriodFrom)
    {
        $this->reviewPeriodFrom = $reviewPeriodFrom;

        return $this;
    }

    /**
     * Get reviewPeriodFrom
     *
     * @return \DateTime
     */
    public function getReviewPeriodFrom()
    {
        return $this->reviewPeriodFrom;
    }

    /**
     * Set reviewPeriodTo
     *
     * @param \DateTime $reviewPeriodTo
     *
     * @return NmtHrAnnualReview
     */
    public function setReviewPeriodTo($reviewPeriodTo)
    {
        $this->reviewPeriodTo = $reviewPeriodTo;

        return $this;
    }

    /**
     * Get reviewPeriodTo
     *
     * @return \DateTime
     */
    public function getReviewPeriodTo()
    {
        return $this->reviewPeriodTo;
    }
}
