<?php
namespace Procure\Form\PR;

use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;
use Application\Form\Helper\DefaultOptions;
use Procure\Form\Helper\DefaultProcureOptions;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRCollectionFilterForm extends GenericForm
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
        /*
         * $this->add([
         * 'type' => 'text', // to update, if needed
         * 'name' => 'docYear',
         * 'attributes' => [
         * 'id' => 'docYear',
         * 'class' => "form-control input-sm", // to update, if needed
         * 'required' => FALSE // to update, if needed
         * ],
         * 'options' => [
         * 'label' => Translator::translate('Doc Year'), // to update, if needed
         * 'label_attributes' => [
         * 'class' => "control-label col-sm-2" // to update, if needed
         * ]
         * ]
         * ]);
         */

        // ======================================
        // Form Element for {docMonth}
        // ======================================
        /*
         * $this->add([
         * 'type' => 'text', // to update, if needed
         * 'name' => 'docMonth',
         * 'attributes' => [
         * 'id' => 'docMonth',
         * 'class' => "form-control input-sm", // to update, if needed
         * 'required' => FALSE // to update, if needed
         * ],
         * 'options' => [
         * 'label' => Translator::translate('docMonth'), // to update, if needed
         * 'label_attributes' => [
         * 'class' => "control-label col-sm-2" // to update, if needed
         * ]
         * ]
         * ]);
         */

        // ======================================
        // Form Element for {docStatus}
        // ======================================
        /*
         * $this->add([
         * 'type' => 'text', // to update, if needed
         * 'name' => 'docStatus',
         * 'attributes' => [
         * 'id' => 'docStatus',
         * 'class' => "form-control input-sm", // to update, if needed
         * 'required' => FALSE // to update, if needed
         * ],
         * 'options' => [
         * 'label' => Translator::translate('PR Status'), // to update, if needed
         * 'label_attributes' => [
         * 'class' => "control-label col-sm-2" // to update, if needed
         * ]
         * ]
         * ]);
         */
        // ======================================
        // Form Element for {balance}
        // ======================================
        /*
         * $this->add([
         * 'type' => 'text', // to update, if needed
         * 'name' => 'balance',
         * 'attributes' => [
         * 'id' => 'balance',
         * 'class' => "form-control input-sm", // to update, if needed
         * 'required' => FALSE // to update, if needed
         * ],
         * 'options' => [
         * 'label' => Translator::translate('balance'), // to update, if needed
         * 'label_attributes' => [
         * 'class' => "control-label col-sm-2" // to update, if needed
         * ]
         * ]
         * ]);
         */

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
        // Form Element for {sortBy}
        // ======================================
        $this->add([
            'type' => 'text', // to update, if needed
            'name' => 'sortBy',
            'attributes' => [
                'id' => 'sortBy',
                'class' => "form-control input-sm", // to update, if needed
                'required' => FALSE // to update, if needed
            ],
            'options' => [
                'label' => Translator::translate('sortBy'), // to update, if needed
                'label_attributes' => [
                    'class' => "control-label col-sm-2" // to update, if needed
                ]
            ]
        ]);

        // ======================================
        // Form Element for {sort}
        // ======================================
        $this->add([
            'type' => 'text', // to update, if needed
            'name' => 'sort',
            'attributes' => [
                'id' => 'sort',
                'class' => "form-control input-sm", // to update, if needed
                'required' => FALSE // to update, if needed
            ],
            'options' => [
                'label' => Translator::translate('sort'), // to update, if needed
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
        // Form Element for {renderType}
        // ======================================
        $this->add([
            'type' => Hidden::class, // to update, if needed
            'name' => 'renderType'
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
                'class' => 'control-label col-sm-2'
            ]
        ]);
        // $select->setEmptyOption(Translator::translate('Parent Account Number'));

        $select->setValueOptions(DefaultOptions::createResultPerPageOption());
        // $select->setDisableInArrayValidator(false);
        $this->add($select);

        // ======================================
        // Form Element for {Doc Year}
        // ======================================

        // select
        $select = new Select();
        $select->setName("docYear");
        $select->setAttributes([
            'id' => 'docYear',
            'class' => "form-control input-sm chosen-select",
            'required' => true
        ]);

        $select->setOptions([
            'label' => Translator::translate('Year'),
            'label_attributes' => [
                'class' => 'control-label col-sm-2'
            ]
        ]);
        // $select->setEmptyOption(Translator::translate('Parent Account Number'));

        $select->setValueOptions(DefaultOptions::createYearOption());
        // $select->setDisableInArrayValidator(false);
        $this->add($select);

        // ======================================
        // Form Element for {Doc Month}
        // ======================================
        // select
        $select = new Select();
        $select->setName("docMonth");
        $select->setAttributes([
            'id' => 'docYear',
            'class' => "form-control input-sm chosen-select",
            'required' => true
        ]);

        $select->setOptions([
            'label' => Translator::translate('Month'),
            'label_attributes' => [
                'class' => 'control-label col-sm-2'
            ]
            // 'empty_option' => 'Please choose a month'
        ]);
        // $select->setEmptyOption(Translator::translate('Parent Account Number'));

        $select->setValueOptions(DefaultOptions::createMonthOption());
        // $select->setDisableInArrayValidator(false);
        $this->add($select);

        // ======================================
        // Form Element for {Doc Status}
        // ======================================
        // select
        $select = new Select();
        $select->setName("docStatus");
        $select->setAttributes([
            'id' => 'docStatus',
            'class' => "form-control input-sm chosen-select",
            'required' => true
        ]);

        $select->setOptions([
            'label' => Translator::translate('Status'),
            'label_attributes' => [
                'class' => 'control-label col-sm-2'
            ]
            // 'empty_option' => 'Please choose a month'
        ]);
        // $select->setEmptyOption(Translator::translate('Parent Account Number'));

        $select->setValueOptions(DefaultProcureOptions::createPRDocStatusOption());
        // $select->setDisableInArrayValidator(false);
        $this->add($select);

        // ======================================
        // Form Element for {balance}
        // ======================================
        // select
        $select = new Select();
        $select->setName("balance");
        $select->setAttributes([
            'id' => 'docStatus',
            'class' => "form-control input-sm chosen-select",
            'required' => true
        ]);

        $select->setOptions([
            'label' => Translator::translate('Balance'),
            'label_attributes' => [
                'class' => 'control-label col-sm-2'
            ]
            // 'empty_option' => 'Please choose a month'
        ]);
        // $select->setEmptyOption(Translator::translate('Parent Account Number'));

        $select->setValueOptions(DefaultProcureOptions::createPRBalanceOption());
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
    public function getDocYear()
    {
        return $this->get("docYear");
    }

    public function getDocMonth()
    {
        return $this->get("docMonth");
    }

    public function getCurrentState()
    {
        return $this->get("currentState");
    }

    public function getDocStatus()
    {
        return $this->get("docStatus");
    }

    public function getBalance()
    {
        return $this->get("balance");
    }

    public function getPrId()
    {
        return $this->get("prId");
    }

    public function getIsActive()
    {
        return $this->get("isActive");
    }

    public function getCompanyId()
    {
        return $this->get("companyId");
    }

    public function getSortBy()
    {
        return $this->get("sortBy");
    }

    public function getSort()
    {
        return $this->get("sort");
    }

    public function getLimit()
    {
        return $this->get("limit");
    }

    public function getOffset()
    {
        return $this->get("offset");
    }

    /*
     * |=============================
     * | Function to get Form Elements
     * | Manual
     * |
     * |=============================
     */
    public function getResultPerPage()
    {
        return $this->get("resultPerPage");
    }

    public function getRenderType()
    {
        return $this->get("renderType");
    }
}
