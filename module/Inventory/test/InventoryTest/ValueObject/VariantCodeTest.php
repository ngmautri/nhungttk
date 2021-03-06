<?php
namespace InventoryTest\ValueObject;

use Inventory\Domain\Item\ValueObject\VariantCode;
use PHPUnit_Framework_TestCase;

class VariantCodeTest extends PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $itemId = 1;
        $attributes = [
            1,
            2,
            3,
            4
        ];
        $vo = new VariantCode($itemId, $attributes);
        echo $vo->getValue();
        $itemId = 1;
        $attributes = [
            3,
            2,
            1
        ];
        $v1 = new VariantCode($itemId, $attributes);
        // echo $v1->getValue();
        // \var_dump($vo->equals($v1));

        \var_dump(VariantCode::createFrom('1_I_A_4_1_5_3'));
    }
}