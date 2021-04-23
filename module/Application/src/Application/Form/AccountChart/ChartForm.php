<?php
namespace Application\Form\AccountChart;

use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;
use Zend\Form\Element\Checkbox;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartForm extends GenericForm
{

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->id = $id;
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAction('/application/account-chart/create');
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
        // Form Element for {coaCode}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'coaCode',
            'attributes' => [
                'id' => 'coaCode',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Chart Code'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {coaName}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'coaName',
            'attributes' => [
                'id' => 'coaName',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Chart Name'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {description}
        // ======================================
        $this->add([
            'type' => 'textarea',
            'name' => 'description',
            'attributes' => [
                'id' => 'description',
                'class' => "form-control input-sm",
                'required' => FALSE,
                'rows' => 4
            ],
            'options' => [
                'label' => Translator::translate('description'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {validFrom}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'validFrom',
            'attributes' => [
                'id' => 'validFrom',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Valid from'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {validTo}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'validTo',
            'attributes' => [
                'id' => 'validTo',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Valid To'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // MANUAL ELEMENT
        // +++++++++++++++++++++++++++++++

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
    }

    // ======================================
    // Function to get Form Elements
    // ======================================
    public function getCoaCode()
    {
        return $this->get("coaCode");
    }

    public function getCoaName()
    {
        return $this->get("coaName");
    }

    public function getDescription()
    {
        return $this->get("description");
    }

    public function getIsActive()
    {
        return $this->get("isActive");
    }

    public function getValidFrom()
    {
        return $this->get("validFrom");
    }

    public function getValidTo()
    {
        return $this->get("validTo");
    }
}
