<?php
namespace HR\Domain\ValueObject\Employee;

use Application\Domain\Shared\ValueObject;
use Application\Domain\Shared\Date\DateRange;
use Webmozart\Assert\Assert;

/**
 * Contract Duration Value Object
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class ContractDuration extends ValueObject
{

    private $dateRange;

    private $startDate;

    private $endDate;

    public function __construct(DateRange $dateRange)
    {
        Assert::notNull($dateRange, 'Date range for contract should not be null');

        $this->dateRange = $dateRange;
        $this->startDate = $dateRange->getStartDate();
        $this->endDate = $dateRange->getEndDate();
    }

    public function isRunning()
    {
        $now = new \DateTime();
        return ($this->startDate <= $now && $now <= $this->endDate);
    }

    public function isExpired()
    {
        $now = new \DateTime();
        return ($this->endDate < $now);
    }

    public function notStarted()
    {
        $now = new \DateTime();
        return ($this->startDate > $now);
    }

    public function getElapsedText()
    {
        if (! $this->isRunning()) {
            return \sprintf("%s", "not running");
        }
        $now = new \DateTime();
        $interval = $this->getStartDate()->diff($now);
        return \sprintf("Elapsed: %s years, %s months, %s days", $interval->y, $interval->m, $interval->d);
    }

    public function getElapsedDays()
    {
        if (! $this->isRunning()) {
            return \sprintf("%s", "not running");
        }

        $now = new \DateTime();
        $interval = $this->getStartDate()->diff($now);
        return \sprintf("Elapsed: %s days", $interval->y * 365 + $interval->m * 30 + $interval->d);
    }

    public function getRemainingDays()
    {
        if (! $this->isRunning()) {
            return \sprintf("%s", "not running");
        }

        $now = new \DateTime();
        $interval = $now->diff($this->getEndDate());
        return \sprintf("Remaining: %s days", $interval->y * 365 + $interval->m * 30 + $interval->d);
    }

    public function getRemainingText()
    {
        if (! $this->isRunning()) {
            return \sprintf("%s", "not running");
        }

        $now = new \DateTime();
        $interval = $now->diff($this->getEndDate());
        return \sprintf("Remaining: %s years, %s months, %s days", $interval->y, $interval->m, $interval->d);
    }

    public function makeSnapshot()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\ValueObject::getAttributesToCompare()
     */
    public function getAttributesToCompare()
    {
        return [
            $this->getStartDate()->format('Y-m-d'),
            $this->getEndDate()->format('Y-m-d')
        ];
    }

    /**
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     *
     * @return \Application\Domain\Shared\Date\DateRange
     */
    public function getDateRange()
    {
        return $this->dateRange;
    }
}
