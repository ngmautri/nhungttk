<?php
namespace Inventory\Domain\Item\ValueObject;

use Application\Domain\Shared\ValueObject;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VariantCode extends ValueObject
{

    private $code;

    private $itemId;

    private $itemIdVO;

    private $attributes;

    const SEPERATOR = '_I_A_';

    const ATTR_SEPERATOR = '_';

    /**
     *
     * @param string $variantCode
     */
    public static function createFrom($variantCode)

    {
        $result = \explode(self::SEPERATOR, $variantCode, 2);

        if (count($result) != 2) {
            throw new \InvalidArgumentException('Variant Code not valid!');
        }

        return new self($result[0], \explode('_', $result[1]));
    }

    public function __construct($itemId, $attributes, $context = null)
    {
        $this->itemIdVO = new ItemId($itemId, 'Item ID');
        Assert::notEmpty($attributes, sprintf('Item attribute [%s]is empty!', ''));

        $this->itemId = $itemId;
        sort($attributes);
        $this->attributes = $attributes;
        $this->code = \sprintf("%s%s%s", $itemId, self::SEPERATOR, \implode(self::ATTR_SEPERATOR, $attributes));
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
            $this->code
        ];
    }

    /**
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->code;
    }
}
