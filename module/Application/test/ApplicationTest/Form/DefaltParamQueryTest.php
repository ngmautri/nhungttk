<?php
namespace ApplicationTest\Form;

use Application\Application\Helper\DefaultParamQueryHelper;
use PHPUnit_Framework_TestCase;

class DefaltParamQueryTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreate()
    {
        $data = new DefaultParamQueryHelper();
        $data->setChangeEventTemplate('/procure/ap/inline-update-row');
        echo $data->getOutPut();

        // $helper->closeTag($form);
    }
}