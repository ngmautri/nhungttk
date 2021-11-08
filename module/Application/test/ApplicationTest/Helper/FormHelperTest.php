<?php
namespace ApplicationTest\Helper;

use Application\Application\Helper\Form\FormHelper;
use PHPUnit_Framework_TestCase;

class FormHelperTest extends PHPUnit_Framework_TestCase
{

    public function testFormHelper()
    {
        $list = [
            FormHelper::createLink('pdf', 'pdf', '', ''),
            FormHelper::createLink('excel', 'pdf', '', ''),
            FormHelper::createLink('openoffice', 'pdf', '', '')
        ];

        $toolbar = FormHelper::createDropDownBtn($list);
        echo $toolbar;
    }
}