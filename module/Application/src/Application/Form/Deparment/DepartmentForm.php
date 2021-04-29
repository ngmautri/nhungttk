<?php
namespace Application\Form\Deparment;

use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;
use Application\Form\Helper\OptionsHelperFactory;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;

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
        // ======================================
        // Form Element for {remarks}
        // ======================================
        $this->add([
            'type' => 'textarea',
            'name' => 'remarks',
            'attributes' => [
                'id' => 'remarks',
                'class' => "form-control input-sm",
                'required' => FALSE,
                'rows' => 4
            ],
            'options' => [
                'label' => Translator::translate('remarks'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {departmentName}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'departmentName',
            'attributes' => [
                'id' => 'departmentName',
                'class' => "form-control input-sm",
                'required' => true
            ],
            'options' => [
                'label' => Translator::translate('departmentName'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {departmentCode}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'departmentCode',
            'attributes' => [
                'id' => 'departmentCode',
                'class' => "form-control input-sm",
                'required' => true
            ],
            'options' => [
                'label' => Translator::translate('departmentCode'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);
    }

    protected function addManualElements()
    {
        $this->add([
            'type' => Checkbox::class,
            'name' => 'isActive',
            'attributes' => [
                'id' => 'isActive',
                'class' => "form-control input-sm"
            ],
            'options' => [
                'label' => 'Is Active',
                'label_attributes' => [
                    'class' => "control-label col-sm-1"
                ],
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ],
            'attributes' => [
                'value' => '1'
            ]
        ]);

        // select
        $select = new Select();
        $select->setName("parentName");
        $select->setAttributes([
            'id' => 'parentName',
            'class' => "form-control input-sm chosen-select",
            'required' => true
        ]);

        $select->setOptions([
            'label' => 'Parent Name',
            'label_attributes' => [
                'class' => "control-label col-sm-2"
            ]
        ]);

        // $select->setEmptyOption('Select parent department');
        $o = OptionsHelperFactory::createDepartmentOptions1($this->getDepartmentOptions());

        $select->setValueOptions($o);
        // $select->setDisableInArrayValidator(false);
        $this->add($select);
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

    // ========================================================
    // Auto Generated
    // ========================================================
    public function getRemarks()
    {
        return $this->get("remarks");
    }

    public function getDepartmentName()
    {
        return $this->get("departmentName");
    }

    public function getDepartmentCode()
    {
        return $this->get("departmentCode");
    }

    public function getIsActive()
    {
        return $this->get("isActive");
    }

    public function getParentName()
    {
        return $this->get("parentName");
    }
}
