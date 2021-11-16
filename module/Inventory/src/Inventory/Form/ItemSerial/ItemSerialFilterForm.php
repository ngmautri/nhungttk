<?php
namespace Inventory\Form\ItemSerial;

use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialFilterForm extends GenericForm
{

    private $accountOptions;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->id = $id;
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-horizontal');
    }

    public function setAction($url)
    {
        $this->setAttribute('action', $url);
        return $this;
    }

    /*
     * |=============================
     * |Autogenerate Element
     * |
     * |=============================
     */

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Contracts\GenericForm::addElements()
     */
    protected function addElements()
    {
        // ======================================
        // Form Element for {docYear}
        // ======================================
        $this->add([
            'type' => 'text', // to update, if needed
            'name' => 'docYear',
            'attributes' => [
                'id' => 'docYear',
                'class' => "form-control input-sm", // to update, if needed
                'required' => FALSE // to update, if needed
            ],
            'options' => [
                'label' => Translator::translate('docYear'), // to update, if needed
                'label_attributes' => [
                    'class' => "control-label col-sm-2" // to update, if needed
                ]
            ]
        ]);

        // ======================================
        // Form Element for {docMonth}
        // ======================================
        $this->add([
            'type' => 'text', // to update, if needed
            'name' => 'docMonth',
            'attributes' => [
                'id' => 'docMonth',
                'class' => "form-control input-sm", // to update, if needed
                'required' => FALSE // to update, if needed
            ],
            'options' => [
                'label' => Translator::translate('docMonth'), // to update, if needed
                'label_attributes' => [
                    'class' => "control-label col-sm-2" // to update, if needed
                ]
            ]
        ]);

        // ======================================
        // Form Element for {isActive}
        // ======================================
        $this->add([
            'type' => 'text', // to update, if needed
            'name' => 'isActive',
            'attributes' => [
                'id' => 'isActive',
                'class' => "form-control input-sm", // to update, if needed
                'required' => FALSE // to update, if needed
            ],
            'options' => [
                'label' => Translator::translate('isActive'), // to update, if needed
                'label_attributes' => [
                    'class' => "control-label col-sm-2" // to update, if needed
                ]
            ]
        ]);
    }

    /*
     * |=============================
     * | Manual Element
     * |
     * |=============================
     */

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Contracts\GenericForm::addManualElements()
     */
    protected function addManualElements()
    {

        // ======================================
        // Form Element for {itemId}
        // ======================================
        $this->add([
            'type' => Hidden::class, // to update, if needed
            'name' => 'itemId'
        ]);

        // ======================================
        // Form Element for {vendorId}
        // ======================================
        $this->add([
            'type' => Hidden::class, // to update, if needed
            'name' => 'vendorId'
        ]);

        // ======================================
        // Form Element for {invoiceId}
        // ======================================
        $this->add([
            'type' => Hidden::class, // to update, if needed
            'name' => 'invoiceId'
        ]);

        // ======================================
        // Form Element for {resultPerPage}
        // ======================================

        // select
        $select = new Select();
        $select->setName("resultPerPage");
        $select->setAttributes([
            'id' => 'resultPerPage',
            'class' => "form-control input-sm chosen-select",
            'required' => true
        ]);

        $select->setOptions([
            'label' => Translator::translate('Result Per Page'),
            'label_attributes' => [
                'class' => "control-label col-sm-2"
            ]
        ]);
        // $select->setEmptyOption(Translator::translate('Parent Account Number'));

        $o = [];

        $tmp1 = [
            'value' => 10,
            'label' => 10
        ];

        $o[] = $tmp1;

        $tmp1 = [
            'value' => 20,
            'label' => 20
        ];

        $o[] = $tmp1;

        $tmp1 = [
            'value' => 50,
            'label' => 50
        ];

        $o[] = $tmp1;

        $select->setValueOptions($o);
        // $select->setDisableInArrayValidator(false);
        $this->add($select);
    }

    /*
     * |=============================
     * | Function to get Form Elements
     * | Autogenerated
     * |
     * |=============================
     */
    public function getItemId()
    {
        return $this->get("itemId");
    }

    public function getVendorId()
    {
        return $this->get("vendorId");
    }

    public function getInvoiceId()
    {
        return $this->get("invoiceId");
    }

    public function getDocYear()
    {
        return $this->get("docYear");
    }

    public function getDocMonth()
    {
        return $this->get("docMonth");
    }

    public function getIsActive()
    {
        return $this->get("isActive");
    }

    public function getResultPerPage()
    {
        return $this->get("resultPerPage");
    }
}
