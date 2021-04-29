<?php
namespace Inventory\Form\Warehouse;

use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WHLocationForm extends GenericForm
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
        // Form Element for {remarks}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'remarks',
            'attributes' => [
                'id' => 'remarks',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('remarks'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {locationName}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'locationName',
            'attributes' => [
                'id' => 'locationName',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('locationName'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {locationCode}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'locationCode',
            'attributes' => [
                'id' => 'locationCode',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('locationCode'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {locationType}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'locationType',
            'attributes' => [
                'id' => 'locationType',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('locationType'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {isActive}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'isActive',
            'attributes' => [
                'id' => 'isActive',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('isActive'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {isLocked}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'isLocked',
            'attributes' => [
                'id' => 'isLocked',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('isLocked'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {parentCode}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'parentCode',
            'attributes' => [
                'id' => 'parentCode',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('parentCode'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);
    }

    protected function addManualElements()
    {}

    // ======================================
    // Function to get Form Elements
    // ======================================
    public function getRemarks()
    {
        return $this->get("remarks");
    }

    public function getLocationName()
    {
        return $this->get("locationName");
    }

    public function getLocationCode()
    {
        return $this->get("locationCode");
    }

    public function getLocationType()
    {
        return $this->get("locationType");
    }

    public function getIsActive()
    {
        return $this->get("isActive");
    }

    public function getIsLocked()
    {
        return $this->get("isLocked");
    }

    public function getParentCode()
    {
        return $this->get("parentCode");
    }
}
