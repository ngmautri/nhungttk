<?php
namespace Application\Domain\Shared\Date;

use Application\Domain\Shared\ValueObject;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class DateRange extends ValueObject
{

    private $start;

    private $end;

    public function __construct(\DateTime $start, \DateTime $end)
    {
        if ($start > $end) {
            throw new \RuntimeException(\sprintf("%s-%s", $start, $start));
        }
        $this->start = $start;
        $this->end = $end;
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
            $this->getStart(),
            $this->getEnd()
        ];
    }

    /**
     *
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     *
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }
}
