<?php

namespace HR\Payroll\Input;
use HR\Payroll\Input\AbstractPayrollInput;

/**
 * Consolidated Payroll Input
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Class ConsolidatedPayrollInput extends AbstractPayrollInput implements PayrollInputInterface
{
    private $totalWorkingDays;
    private $publicHolidays;
    private $coporateLeaves;    
    private $actualWorkedDays;
    private $outOfOfficeDays;
    private $annualLeaves;
    private $paidSickleaves;
    private $personalPaidLeaves;
    private $approvedLeaves;
    private $unapprovedLeaves;
    private $maternityLeaves;
    private $sickLeavesPaidBySSO;
    private $overTime;
    
    private $numberOfWarningsLetter;    
    private $numberOfLoadedContainer;
    
    private $otherLeaves;    
    
    /**
     * @return mixed
     */
    public function getTotalWorkingDays()
    {
        return $this->totalWorkingDays;
    }

    /**
     * @return mixed
     */
    public function getPublicHolidays()
    {
        return $this->publicHolidays;
    }

    /**
     * @return mixed
     */
    public function getCoporateLeaves()
    {
        return $this->coporateLeaves;
    }

    /**
     * @return mixed
     */
    public function getActualWorkedDays()
    {
        return $this->actualWorkedDays;
    }

    /**
     * @return mixed
     */
    public function getOutOfOfficeDays()
    {
        return $this->outOfOfficeDays;
    }

    /**
     * @return mixed
     */
    public function getAnnualLeaves()
    {
        return $this->annualLeaves;
    }

    /**
     * @return mixed
     */
    public function getPaidSickleaves()
    {
        return $this->paidSickleaves;
    }

    /**
     * @return mixed
     */
    public function getPersonalPaidLeaves()
    {
        return $this->personalPaidLeaves;
    }

    /**
     * @return mixed
     */
    public function getApprovedLeaves()
    {
        return $this->approvedLeaves;
    }

    /**
     * @return mixed
     */
    public function getUnapprovedLeaves()
    {
        return $this->unapprovedLeaves;
    }

    /**
     * @return mixed
     */
    public function getMaternityLeaves()
    {
        return $this->maternityLeaves;
    }

    /**
     * @return mixed
     */
    public function getOtherLeaves()
    {
        return $this->otherLeaves;
    }

    /**
     * @return mixed
     */
    public function getSickLeavesPaidBySSO()
    {
        return $this->sickLeavesPaidBySSO;
    }

    /**
     * @param mixed $totalWorkingDays
     */
    public function setTotalWorkingDays($totalWorkingDays)
    {
        $this->totalWorkingDays = $totalWorkingDays;
    }

    /**
     * @param mixed $publicHolidays
     */
    public function setPublicHolidays($publicHolidays)
    {
        $this->publicHolidays = $publicHolidays;
    }

    /**
     * @param mixed $coporateLeaves
     */
    public function setCoporateLeaves($coporateLeaves)
    {
        $this->coporateLeaves = $coporateLeaves;
    }

    /**
     * @param mixed $actualWorkedDays
     */
    public function setActualWorkedDays($actualWorkedDays)
    {
        $this->actualWorkedDays = $actualWorkedDays;
    }

    /**
     * @param mixed $outOfOfficeDays
     */
    public function setOutOfOfficeDays($outOfOfficeDays)
    {
        $this->outOfOfficeDays = $outOfOfficeDays;
    }

    /**
     * @param mixed $annualLeaves
     */
    public function setAnnualLeaves($annualLeaves)
    {
        $this->annualLeaves = $annualLeaves;
    }

    /**
     * @param mixed $paidSickleaves
     */
    public function setPaidSickleaves($paidSickleaves)
    {
        $this->paidSickleaves = $paidSickleaves;
    }

    /**
     * @param mixed $personalPaidLeaves
     */
    public function setPersonalPaidLeaves($personalPaidLeaves)
    {
        $this->personalPaidLeaves = $personalPaidLeaves;
    }

    /**
     * @param mixed $approvedLeaves
     */
    public function setApprovedLeaves($approvedLeaves)
    {
        $this->approvedLeaves = $approvedLeaves;
    }

    /**
     * @param mixed $unapprovedLeaves
     */
    public function setUnapprovedLeaves($unapprovedLeaves)
    {
        $this->unapprovedLeaves = $unapprovedLeaves;
    }

    /**
     * @param mixed $maternityLeaves
     */
    public function setMaternityLeaves($maternityLeaves)
    {
        $this->maternityLeaves = $maternityLeaves;
    }

    /**
     * @param mixed $otherLeaves
     */
    public function setOtherLeaves($otherLeaves)
    {
        $this->otherLeaves = $otherLeaves;
    }

    /**
     * @param mixed $sickLeavesPaidBySSO
     */
    public function setSickLeavesPaidBySSO($sickLeavesPaidBySSO)
    {
        $this->sickLeavesPaidBySSO = $sickLeavesPaidBySSO;
    }
    /**
     * @return mixed
     */
    public function getOverTime()
    {
        return $this->overTime;
    }

    /**
     * @param mixed $overTime
     */
    public function setOverTime($overTime)
    {
        $this->overTime = $overTime;
    }
    /**
     * @return mixed
     */
    public function getNumberOfWarningsLetter()
    {
        return $this->numberOfWarningsLetter;
    }

    /**
     * @param mixed $numberOfWarningsLetter
     */
    public function setNumberOfWarningsLetter($numberOfWarningsLetter)
    {
        $this->numberOfWarningsLetter = $numberOfWarningsLetter;
    }
    /**
     * @return mixed
     */
    public function getNumberOfLoadedContainer()
    {
        return $this->numberOfLoadedContainer;
    }

    /**
     * @param mixed $numberOfLoadedContainer
     */
    public function setNumberOfLoadedContainer($numberOfLoadedContainer)
    {
        $this->numberOfLoadedContainer = $numberOfLoadedContainer;
    }




      
  
}