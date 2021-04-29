<?php
namespace Application\Form\Warehouse;

use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;
use Zend\Form\Element\Hidden;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseForm extends GenericForm
{

    private $accountOptions;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->id = $id;
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->addRootElement();
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
        // Form Element for {whCode}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'whCode',
            'attributes' => [
                'id' => 'whCode',
                'class' => "form-control input-sm",
                'required' => TRUE
            ],
            'options' => [
                'label' => Translator::translate('Warehouse Code'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {whName}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'whName',
            'attributes' => [
                'id' => 'whName',
                'class' => "form-control input-sm",
                'required' => TRUE
            ],
            'options' => [
                'label' => Translator::translate('Warehouse Name'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {whAddress}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'whAddress',
            'attributes' => [
                'id' => 'whAddress',
                'class' => "form-control input-sm",
                'required' => TRUE
            ],
            'options' => [
                'label' => Translator::translate('Warehouse Address'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {whContactPerson}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'whContactPerson',
            'attributes' => [
                'id' => 'whContactPerson',
                'class' => "form-control input-sm",
                'required' => TRUE
            ],
            'options' => [
                'label' => Translator::translate('Contact Person'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {whTelephone}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'whTelephone',
            'attributes' => [
                'id' => 'whTelephone',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Telephone'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {whEmail}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'whEmail',
            'attributes' => [
                'id' => 'whEmail',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Email'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {whStatus}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'whStatus',
            'attributes' => [
                'id' => 'whStatus',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Status'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

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
        // Form Element for {stockkeeper}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'stockkeeper',
            'attributes' => [
                'id' => 'stockkeeper',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Stock keeper'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {whController}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'whController',
            'attributes' => [
                'id' => 'whController',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Controller'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {location}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'location',
            'attributes' => [
                'id' => 'location',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('location'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Contracts\GenericForm::addManualElements()
     */
    protected function addManualElements()
    {
        // +++++++++++++++++++++++++++++++++++++++++++
        // MANUAL ELEMENT
        // +++++++++++++++++++++++++++++++++++++++++++
        $this->add([
            'type' => Hidden::class,
            'name' => 'id'
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
        // Form Element for {whCountry}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'whCountry',
            'attributes' => [
                'id' => 'whCountry',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('whCountry'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {isDefault}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'isDefault',
            'attributes' => [
                'id' => 'isDefault',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('isDefault'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);
    }

    // ======================================
    // Function to get Form Elements
    // ======================================
    public function getRootId()
    {
        return $this->get("coa");
    }

    public function getMemberId()
    {
        return $this->get("id");
    }

    public function getWhCode()
    {
        return $this->get("whCode");
    }

    public function getWhName()
    {
        return $this->get("whName");
    }

    public function getWhAddress()
    {
        return $this->get("whAddress");
    }

    public function getWhContactPerson()
    {
        return $this->get("whContactPerson");
    }

    public function getWhTelephone()
    {
        return $this->get("whTelephone");
    }

    public function getWhEmail()
    {
        return $this->get("whEmail");
    }

    public function getIsLocked()
    {
        return $this->get("isLocked");
    }

    public function getWhStatus()
    {
        return $this->get("whStatus");
    }

    public function getRemarks()
    {
        return $this->get("remarks");
    }

    public function getIsDefault()
    {
        return $this->get("isDefault");
    }

    public function getWhCountry()
    {
        return $this->get("whCountry");
    }

    public function getStockkeeper()
    {
        return $this->get("stockkeeper");
    }

    public function getWhController()
    {
        return $this->get("whController");
    }

    public function getLocation()
    {
        return $this->get("location");
    }
}
