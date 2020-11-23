<?php
namespace Application\Domain\Shared\Identity;

use Application\Domain\Shared\ValueObject;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericId extends ValueObject
{

    /**
     *
     * @var int
     */
    private $genericId;

    /**
     *
     * @param int $productId
     */
    public function __construct($id, $context = null)
    {
        Assert::greaterThan($id, 0, sprintf('%s ID %s is invalid. ID must be number that is greater than zero', $context, $id));
        $this->genericId = $id;
    }

    /**
     *
     * @return int
     */
    public function getValue()
    {
        return $this->genericId;
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
            $this->getValue()
        ];
    }
}
