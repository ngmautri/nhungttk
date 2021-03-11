<?php
namespace HR\Domain\ValueObject\Employee;

use Application\Domain\Shared\ValueObject;

/**
 * Contract Duration Value Object
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class ContractDuration extends ValueObject
{

    private $startDate;

    private $endDate;

    public function __construct($startDate, $endDate = "9999-12-31")
    {
        $this->assertDateRangeIsInValidFormat($startDate, $endDate);
        $this->startDate = new \DateTime($startDate);
        $this->endDate = new \DateTime($endDate);
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

    private function assertDateRangeIsInValidFormat($startDate, $endDate)
    {
        try {
            $d1 = new \DateTimeImmutable($startDate);
        } catch (\Exception $e) {
            throw \InvalidArgumentException(sprintf('Invalid start date! %s value provided.', var_export($startDate, true)));
        }

        try {
            $d2 = new \DateTimeImmutable($endDate);
        } catch (\Exception $e) {
            throw \InvalidArgumentException(sprintf('Invalid start date! %s value provided.', var_export($endDate, true)));
        }

        if ($d1 >= $d2) {
            throw new \InvalidArgumentException(sprintf('Invalid range! %s-%s provided.', var_export($startDate, true), var_export($endDate, true)));
        }
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
}
