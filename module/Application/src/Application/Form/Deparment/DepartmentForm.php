<?php
namespace Application\Form\Deparment;

use Application\Form\Contracts\GenericForm;
use Application\Form\Helper\OptionsHelperFactory;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\Form\Element\Textarea;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentForm extends GenericForm
{

    private $departmentOptions;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->id = $id;
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAction('/application/department/create');
    }

    public function setAction($url)
    {
        $this->setAttribute('action', $url);
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Contracts\GenericForm::addElements()
     */
    protected function addElements()
    {
        $this->add([
            'type' => 'text',
            'name' => 'departmentName',
            'attributes' => [
                'id' => 'departmentName',
                'class' => "form-control input-sm"
            ],
            'options' => [
                'label' => 'departmentName',
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'departmentCode',
            'attributes' => [
                'id' => 'departmentCode',
                'class' => "form-control input-sm"
            ],
            'options' => [
                'label' => 'departmentCode',
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        $this->add([
            'type' => Checkbox::class,
            'name' => 'Is active',
            'attributes' => [
                'id' => 'isActive',
                'class' => "form-control input-sm"
            ],
            'options' => [
                'label' => '1',
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ],
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ],
            'attributes' => [
                'value' => '1'
            ]
        ]);
        $this->add([
            'type' => 'text',
            'name' => 'departmentNameLocal',
            'attributes' => [
                'id' => 'departmentNameLocal',
                'class' => "form-control input-sm"
            ],
            'options' => [

                'label' => 'departmentNameLocal',
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // select
        $select = new Select();
        $select->setName("parentName");
        $select->setAttributes([
            'id' => 'parentName',
            'class' => "form-control input-sm chosen-select"
        ]);

        $select->setLabelAttributes([
            'class' => "control-label col-sm-2"
        ]);

        // $select->setEmptyOption('Select parent department');
        $o = OptionsHelperFactory::createDepartmentOptions1($this->getDepartmentOptions());

        $select->setValueOptions($o);
        // $select->setDisableInArrayValidator(false);
        $this->add($select);

        $this->add([
            'type' => Textarea::class,
            'name' => 'remarks',
            'attributes' => [
                'id' => 'remarks',
                'class' => "form-control input-sm"
            ],
            'options' => [
                'label' => 'remarks',
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);
    }

    /**
     *
     * @return mixed
     */
    public function getDepartmentOptions()
    {
        return $this->departmentOptions;
    }

    /**
     *
     * @param mixed $departmentOptions
     */
    public function setDepartmentOptions($departmentOptions)
    {
        // \var_dump($departmentOptions);
        $this->departmentOptions = $departmentOptions;
    }
}
