<?php
namespace Application\Form\ItemAttribute;

use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;
use Zend\Form\Element\Checkbox;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeGroupForm extends GenericForm
{

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->id = $id;
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAction('/application/item-attribute/create');
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
        // Form Element for {groupCode}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'groupCode',
            'attributes' => [
                'id' => 'groupCode',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Attribute Code'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {groupName}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'groupName',
            'attributes' => [
                'id' => 'groupName',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Attribute Name'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {groupName1}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'groupName1',
            'attributes' => [
                'id' => 'groupName1',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Attribute Name 1'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {remarks}
        // ======================================
        $this->add([
            'type' => 'textarea',
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
        // Form Element for {isActive}
        // ======================================
        /*
         * $this->add([
         * 'type' => 'text',
         * 'name' => 'isActive',
         * 'attributes' => [
         * 'id' => 'isActive',
         * 'class' => "form-control input-sm",
         * 'required' => FALSE
         * ],
         * 'options' => [
         * 'label' => Translator::translate('isActive'),
         * 'label_attributes' => [
         * 'class' => "control-label col-sm-2"
         * ]
         * ]
         * ]);
         */
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

        // ======================================
        // Form Element for {canHaveLeaf}
        // ======================================
        /*
         * $this->add([
         * 'type' => 'text',
         * 'name' => 'canHaveLeaf',
         * 'attributes' => [
         * 'id' => 'canHaveLeaf',
         * 'class' => "form-control input-sm",
         * 'required' => FALSE
         * ],
         * 'options' => [
         * 'label' => Translator::translate('canHaveLeaf'),
         * 'label_attributes' => [
         * 'class' => "control-label col-sm-2"
         * ]
         * ]
         * ]);
         */

        $this->add([
            'type' => Checkbox::class,
            'name' => 'canHaveLeaf',
            'attributes' => [
                'id' => 'canHaveLeaf',
                'class' => "form-control input-sm"
            ],
            'options' => [
                'label' => Translator::translate('Can Have Leaf'),
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

    /*
     * |=============================
     * | Function to get Form Elements
     * | Autogenerated
     * |
     * |=============================
     */
    public function getGroupCode()
    {
        return $this->get("groupCode");
    }

    public function getGroupName()
    {
        return $this->get("groupName");
    }

    public function getGroupName1()
    {
        return $this->get("groupName1");
    }

    public function getRemarks()
    {
        return $this->get("remarks");
    }

    public function getParentCode()
    {
        return $this->get("parentCode");
    }

    public function getCanHaveLeaf()
    {
        return $this->get("canHaveLeaf");
    }

    public function getIsActive()
    {
        return $this->get("isActive");
    }
}
