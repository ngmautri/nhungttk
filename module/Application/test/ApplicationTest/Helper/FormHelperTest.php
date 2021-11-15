<?php
namespace ApplicationTest\Helper;

use Application\Application\Helper\Form\FormHelper;
use Application\Application\Helper\Form\Bootstrap3\CollapseHelper;
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
        // echo $toolbar;
    }

    public function testCollapse()
    {
        $showTitle = '<i class="fa fa-chevron-down" aria-hidden="true"></i> Filter';
        $hideTitle = '<i class="fa fa-chevron-up" aria-hidden="true"></i> Filter';
        $collapseId = 'test_id';
        $content = 'test_id';

        echo CollapseHelper::drawBasisCollapse($showTitle, $hideTitle, $collapseId, $content);
    }
}