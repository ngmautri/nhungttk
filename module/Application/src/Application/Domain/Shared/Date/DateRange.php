<?php
namespace Application\Domain\Shared\Date;

use Application\Domain\Shared\ValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class DateRange extends ValueObject
{

    private $startDate;

    private $endDate;

    public function __construct($startDate, $endDate = "9999-12-31")
    {
        $this->assertDateRangeIsInValidFormat($startDate, $endDate);
        $this->startDate = new \DateTime($startDate);
        $this->endDate = new \DateTime($endDate);
    }

    /**
     *
     * @param string $startDate
     * @param string $endDate
     * @throws \InvalidArgumentException
     */
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
