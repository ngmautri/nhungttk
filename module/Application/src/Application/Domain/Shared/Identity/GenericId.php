<?php
namespace Application\Domain\Shared\Identity;

use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericId
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
}
