<?php
namespace ApplicationTest\Form;

use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Form\Deparment\DepartmentForm;
use Zend\Form\View\Helper\Form;
use Zend\Form\View\Helper\FormElementErrors;
use Zend\Form\View\Helper\FormInput;
use Zend\Form\View\Helper\FormLabel;
use Zend\Form\View\Helper\FormRow;
use Zend\Stdlib\Hydrator\Reflection;
use PHPUnit_Framework_TestCase;

class DepartmentFormTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreate()
    {
        $data = new DepartmentSnapshot();
        $data->departmentName = '112';
        $data->departmentCode = 'ok';
        $data->parentCode = 'parent';

        $form = new DepartmentForm();
        $form->setHydrator(new Reflection());
        $form->bind($data);

        var_dump($form->getObject());

        // \var_dump($form->getElements());
        echo $form->render();

        // \var_dump($form->get('departmentName'));
        $helper = new Form();
        // echo $helper->openTag($form);

        $helper = new FormRow();

        $helper = new FormInput();

        $helper = new FormElementErrors();

        $helper = new FormLabel();

        $helper = new Form();
        // $helper->closeTag($form);
    }
}