<?php
namespace InventoryTest\Form;

use Inventory\Form\ItemSerial\ItemSerialFilterForm;
use PHPUnit_Framework_TestCase;

class ItemSerialFilterFormTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreate()
    {
        $form = new ItemSerialFilterForm();
        $form->refresh();

        var_dump($form);
    }
}