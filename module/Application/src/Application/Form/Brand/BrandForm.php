<?php
namespace Application\Form\Brand;

use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;
use Zend\Form\Element\Hidden;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandForm extends GenericForm
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
        // Form Element for {brandName}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'brandName',
            'attributes' => [
                'id' => 'brandName',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Brand Name'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {brandName1}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'brandName1',
            'attributes' => [
                'id' => 'brandName1',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('Brand Name1'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {description}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'description',
            'attributes' => [
                'id' => 'description',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('description'),
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
        // Form Element for {logo}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'logo',
            'attributes' => [
                'id' => 'logo',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('logo'),
                'label_attributes' => [
                    'class' => "control-label col-sm-2"
                ]
            ]
        ]);

        // ======================================
        // Form Element for {brandName2}
        // ======================================
        $this->add([
            'type' => 'text',
            'name' => 'brandName2',
            'attributes' => [
                'id' => 'brandName2',
                'class' => "form-control input-sm",
                'required' => FALSE
            ],
            'options' => [
                'label' => Translator::translate('brandName2'),
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

    // this one ID
    public function getRootId()
    {
        return $this->get("id");
    }

    public function getBrandName()
    {
        return $this->get("brandName");
    }

    public function getBrandName1()
    {
        return $this->get("brandName1");
    }

    public function getDescription()
    {
        return $this->get("description");
    }

    public function getRemarks()
    {
        return $this->get("remarks");
    }

    public function getIsActive()
    {
        return $this->get("isActive");
    }

    public function getLogo()
    {
        return $this->get("logo");
    }

    public function getBrandName2()
    {
        return $this->get("brandName2");
    }
}
