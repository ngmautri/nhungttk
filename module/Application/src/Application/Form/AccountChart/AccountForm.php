<?php
namespace Application\Form\AccountChart;

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
class AccountForm extends GenericForm
{

    private $accountOptions;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->id = $id;
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->addRootElement();
        $this->addMemberElement();
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
            'type' => Hidden::class,
            'name' => 'coa'
        ]);

        $this->add([
            'type' => Hidden::class,
            'name' => 'id'
        ]);

        // ======================================
        // Form Element for {accountNumber}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'accountNumber',
            'attributes' => [
                'id' => 'accountNumber',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Account Number'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {accountName}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'accountName',
            'attributes' => [
                'id' => 'accountName',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Account Name'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {accountType}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'accountType',
            'attributes' => [
                'id' => 'accountType',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Account Type'),
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
        // Form Element for {description}
        // ======================================
        $this->add([
            'type' => 'textarea',
            'name' => 'description',
            'attributes' => [
                'id' => 'description',
                'class' => "form-control input-sm",
                'required' => FALSE,
                'rows' => 3
            ],
            'options' => [
                'label' => Translator::translate('Description'),
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
        // Form Element for {allowReconciliation}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'allowReconciliation',
            'attributes' => [
                'id' => 'allowReconciliation',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('allowReconciliation'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {hasCostCenter}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'hasCostCenter',
            'attributes' => [
                'id' => 'hasCostCenter',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('hasCostCenter'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {isClearingAccount}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'isClearingAccount',
            'attributes' => [
                'id' => 'isClearingAccount',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('isClearingAccount'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {isControlAccount}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'isControlAccount',
            'attributes' => [
                'id' => 'isControlAccount',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('isControlAccount'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {manualPostingBlocked}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'manualPostingBlocked',
            'attributes' => [
                'id' => 'manualPostingBlocked',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('manualPostingBlocked'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {allowPosting}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'allowPosting',
            'attributes' => [
                'id' => 'allowPosting',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('allowPosting'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {controlFor}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'controlFor',
            'attributes' => [
                'id' => 'controlFor',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('controlFor'),
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

        // ======================================
        // Form Element for {parentAccountNumber}
        // ======================================

        /*
         * $this->add([
         * 'type' => 'text',
         * 'name' => 'parentAccountNumber',
         * 'attributes' => [
         * 'id' => 'parentAccountNumber',
         * 'class' => "form-control input-sm",
         * 'required' => FALSE
         * ],
         * 'options' => [
         * 'label' => Translator::translate('Parent Account Number'),
         * 'label_attributes' => [
         * 'class' => "control-label col-sm-2"
         * ]
         * ]
         * ]);
         */

        // select
        $select = new Select();
        $select->setName("parentAccountNumber");
        $select->setAttributes([
            'id' => 'parentAccountNumber',
            'class' => "form-control input-sm chosen-select",
            'required' => true
        ]);

        $select->setOptions([
            'label' => Translator::translate('Parent Account Number'),
            'label_attributes' => [
                'class' => "control-label col-sm-2"
            ]
        ]);

        // $select->setEmptyOption(Translator::translate('Parent Account Number'));
        $o = OptionsHelperFactory::createAccountOptions($this->getAccountOptions());

        $select->setValueOptions($o);
        // $select->setDisableInArrayValidator(false);
        $this->add($select);
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

    public function getAccountNumber()
    {
        return $this->get("accountNumber");
    }

    public function getAccountName()
    {
        return $this->get("accountName");
    }

    public function getAccountType()
    {
        return $this->get("accountType");
    }

    public function getParentAccountNumber()
    {
        return $this->get("parentAccountNumber");
    }

    public function getIsActive()
    {
        return $this->get("isActive");
    }

    public function getDescription()
    {
        return $this->get("description");
    }

    public function getRemarks()
    {
        return $this->get("remarks");
    }

    public function getAllowReconciliation()
    {
        return $this->get("allowReconciliation");
    }

    public function getHasCostCenter()
    {
        return $this->get("hasCostCenter");
    }

    public function getIsClearingAccount()
    {
        return $this->get("isClearingAccount");
    }

    public function getIsControlAccount()
    {
        return $this->get("isControlAccount");
    }

    public function getManualPostingBlocked()
    {
        return $this->get("manualPostingBlocked");
    }

    public function getAllowPosting()
    {
        return $this->get("allowPosting");
    }

    public function getControlFor()
    {
        return $this->get("controlFor");
    }

    /**
     *
     * @return mixed
     */
    public function getAccountOptions()
    {
        return $this->accountOptions;
    }

    /**
     *
     * @param mixed $accountOptions
     */
    public function setAccountOptions($accountOptions)
    {
        $this->accountOptions = $accountOptions;
    }
}
