<?php
namespace Inventory\Domain\Item\ValueObject;

use Application\Domain\Shared\ValueObject;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemName extends ValueObject
{

    private $itemName;

    public function __construct($itemName, $context = null)
    {
        $itemName = trim($itemName);
        Assert::stringNotEmpty($itemName, sprintf('Item name [%s]is invalid!', $itemName));
        Assert::lengthBetween($itemName, 2, 80, sprintf('[%s] is invalid. Length >%s and <%s', $itemName, 2, 80));
        Assert::notWhitespaceOnly($itemName, sprintf('Item name [%s]is invalid. only white space', $itemName));

        if (preg_match('/[$^]/', $itemName) == 1) {
            throw new \InvalidArgumentException("Item name contains invalid character (e.g. $)");
        }

        $this->itemName = $itemName;
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
            $this->itemName
        ];
    }

    /**
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->itemName;
    }
}
