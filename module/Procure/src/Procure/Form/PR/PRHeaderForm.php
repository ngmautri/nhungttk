<?php
namespace Procure\Form\PR;

use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;
use Application\Form\Helper\OptionsHelperFactory;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRHeaderForm extends GenericForm
{

    private $whOptions;

    private $departmentOptions;

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
        // Form Element for {prNumber}
        // ======================================
        $this->add([
            'type' => 'text', // to update, if needed
            'name' => 'prNumber',
            'attributes' => [
                'id' => 'prNumber',
                'class' => "form-control input-sm", // to update, if needed
                'required' => FALSE // to update, if needed
            ],
            'options' => [
                'label' => Translator::translate('Purchase Request No.'), // to update, if needed
                'label_attributes' => [
                    'class' => "control-label col-sm-2" // to update, if needed
                ]
            ]
        ]);

        // ======================================
        // Form Element for {keywords}
        // ======================================
        $this->add([
            'type' => 'text', // to update, if needed
            'name' => 'keywords',
            'attributes' => [
                'id' => 'keywords',
                'class' => "form-control input-sm", // to update, if needed
                'required' => FALSE // to update, if needed
            ],
            'options' => [
                'label' => Translator::translate('Keywords'), // to update, if needed
                'label_attributes' => [
                    'class' => "control-label col-sm-2" // to update, if needed
                ]
            ]
        ]);

        // ======================================
        // Form Element for {remarks}
        // ======================================
        $this->add([
            'type' => 'textarea', // to update, if needed
            'name' => 'remarks',
            'attributes' => [
                'id' => 'remarks',
                'class' => "form-control input-sm", // to update, if needed
                'required' => FALSE, // to update, if needed
                'rows' => 2
            ],
            'options' => [
                'label' => Translator::translate('Description'), // to update, if needed
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

        // ======================================
        // Form Element for {docDate}
        // ======================================
        $this->add([
            'type' => 'text', // to update, if needed
            'name' => 'submittedOn',
            'attributes' => [
                'id' => 'docDate',
                'class' => "form-control input-sm", // to update, if needed
                'required' => FALSE // to update, if needed
            ],
            'options' => [
                'label' => Translator::translate('PR Date'), // to update, if needed
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
        // Form Element for {id}
        // ======================================
        $this->add([
            'type' => Hidden::class, // to update, if needed
            'name' => 'id'
        ]);

        // ======================================
        $this->add([
            'type' => Hidden::class, // to update, if needed
            'name' => 'token'
        ]);

        $this->add([
            'type' => Hidden::class, // to update, if needed
            'name' => 'revisionNo'
        ]);

        // ======================================
        // Form Element for {Department}
        // ======================================

        // SELECT
        $select = new Select();
        $select->setName("department");
        $select->setAttributes([
            'id' => 'department',
            'class' => "form-control input-sm chosen-select",
            'required' => true
        ]);

        $select->setOptions([
            'label' => Translator::translate('Department'),
            'label_attributes' => [
                'class' => "control-label col-sm-2"
            ]
        ]);

        $o = OptionsHelperFactory::createValueOptions($this->getDepartmentOptions());
        $select->setEmptyOption(Translator::translate('-'));
        $select->setValueOptions($o);
        $this->add($select);

        // ======================================
        // Form Element for {WH}
        // ======================================

        $select = new Select();
        $select->setName("warehouse");
        $select->setAttributes([
            'id' => 'warehouse',
            'class' => "form-control input-sm chosen-select",
            'required' => true
        ]);

        $select->setOptions([
            'label' => Translator::translate('Warehouse'),
            'label_attributes' => [
                'class' => "control-label col-sm-2"
            ]
        ]);

        $select->setEmptyOption(Translator::translate('-'));
        $o = OptionsHelperFactory::createValueOptions($this->getWhOptions());
        $select->setValueOptions($o);
        $this->add($select);
    }

    /*
     * |=============================
     * | Function to get Form Elements
     * | Autogenerated
     * |
     * |=============================
     */
    public function getPrNumber()
    {
        return $this->get("prNumber");
    }

    public function getKeywords()
    {
        return $this->get("keywords");
    }

    public function getSubmittedOn()
    {
        return $this->get("submittedOn");
    }

    public function getDepartment()
    {
        return $this->get("department");
    }

    public function getRemarks()
    {
        return $this->get("remarks");
    }

    public function getIsActive()
    {
        return $this->get("isActive");
    }

    public function getWarehouse()
    {
        return $this->get("warehouse");
    }

    /*
     * |=============================
     * | Function to get Form Elements
     * | Manual
     * |
     * |=============================
     */
    public function getRevisionNo()
    {
        return $this->get("revisionNo");
    }

    public function getEntityToken()
    {
        return $this->get("token");
    }

    public function getEntityId()
    {
        return $this->get("id");
    }

    /**
     *
     * @return mixed
     */
    public function getWhOptions()
    {
        return $this->whOptions;
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
     * @param mixed $whOptions
     */
    public function setWhOptions($whOptions)
    {
        $this->whOptions = $whOptions;
    }

    /**
     *
     * @param mixed $departmentOptions
     */
    public function setDepartmentOptions($departmentOptions)
    {
        $this->departmentOptions = $departmentOptions;
    }
}
