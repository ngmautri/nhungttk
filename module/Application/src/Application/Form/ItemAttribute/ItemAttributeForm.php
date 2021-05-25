<?php
namespace Application\Form\ItemAttribute;

use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;
use Zend\Form\Element\Hidden;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeForm extends GenericForm
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
        // Form Element for {attributeCode}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'attributeCode',
            'attributes' => [
                'id' => 'attributeCode',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Attribute Value'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {attributeName}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'attributeName',
            'attributes' => [
                'id' => 'attributeName',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Attribute Value'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {attributeName1}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'attributeName1',
            'attributes' => [
                'id' => 'attributeName1',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Attribute Values 1'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {attributeName2}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'attributeName2',
            'attributes' => [
                'id' => 'attributeName2',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Attribute Values 2'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {combinedName}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'combinedName',
            'attributes' => [
                'id' => 'combinedName',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Combined Name'),
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
        // Form Element for {group}
        // ======================================
        /*
         * $this->add([
         * 'type' => 'text',
         * 'name' => 'group',
         * 'attributes' => [
         * 'id' => 'group',
         * 'class' => "form-control input-sm",
         * 'required' => FALSE
         * ],
         * 'options' => [
         * 'label' => Translator::translate('group'),
         * 'label_attributes' => [
         * 'class' => "control-label col-sm-2"
         * ]
         * ]
         * ]);
         */
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
        $this->add([
            'type' => Hidden::class,
            'name' => 'group'
        ]);

        $this->add([
            'type' => Hidden::class,
            'name' => 'id'
        ]);
    }

    /*
     * |=============================
     * | Function to get Form Elements
     * | Autogenerated
     * |
     * |=============================
     */
    public function getRootId()
    {
        return $this->get("group");
    }

    public function getMemberId()
    {
        return $this->get("id");
    }

    public function getAttributeCode()
    {
        return $this->get("attributeCode");
    }

    public function getAttributeName()
    {
        return $this->get("attributeName");
    }

    public function getAttributeName1()
    {
        return $this->get("attributeName1");
    }

    public function getAttributeName2()
    {
        return $this->get("attributeName2");
    }

    public function getCombinedName()
    {
        return $this->get("combinedName");
    }

    public function getRemarks()
    {
        return $this->get("remarks");
    }

    public function getGroup()
    {
        return $this->get("group");
    }
}
