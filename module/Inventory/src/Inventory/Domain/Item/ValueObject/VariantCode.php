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

    public function __construct($itemId, $attributes, $context = null)
    {
        $this->itemIdVO = new ItemId($itemId,'Item ID');
        Assert::notEmpty($attributes, sprintf('Item attribute [%s]is empty!', ''));

        $this->itemId = $itemId;
        $this->attributes = sort($attributes);
        $this->code=\sprintf("i%s_a_%s", $itemId, \implode("_", $attributes));

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
            $this->code,

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
