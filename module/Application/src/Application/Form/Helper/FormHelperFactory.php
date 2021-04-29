<?php
namespace Application\Form\Helper;

use Zend\Form\Element;
use Zend\Form\Element\Select;
use Zend\Form\View\Helper\FormButton;
use Zend\Form\View\Helper\FormCheckbox;
use Zend\Form\View\Helper\FormDate;
use Zend\Form\View\Helper\FormHidden;
use Zend\Form\View\Helper\FormInput;
use Zend\Form\View\Helper\FormRadio;
use Zend\Form\View\Helper\FormSelect;
use Zend\Form\View\Helper\FormSubmit;
use Zend\Form\View\Helper\FormTextarea;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class FormHelperFactory
{

    public static function getHelper($type)
    {
        switch ($type) {
            case "text":
                return new FormInput();
            case "select":
                return new FormSelect();

            case "hidden":
                return new FormHidden();
            case "textarea":
                return new FormTextarea();
        }

        throw new \InvalidArgumentException("Can not create Form helper");
    }

    /**
     *
     * @param Element $element
     * @throws \InvalidArgumentException
     * @return NULL|string
     */
    public static function render(Element $element)
    {
        if (! $element instanceof Element) {
            return null;
        }

        $type = $element->getAttribute('type');
        switch ($type) {

            case "text":
                $helper = new FormInput();
                return $helper->render($element);

            case "select":
                $helper = new FormSelect();
                $newElement = self::_updateValueOptions($element);
                return $helper->render($newElement);

            case "hidden":
                $helper = new FormHidden();
                return $helper->render($element);

            case "submit":
                $helper = new FormSubmit();
                return $helper->render($element);

            case "textarea":
                $helper = new FormTextarea();
                return $helper->render($element);

            case "button":
                $helper = new FormButton();
                return $helper->render($element);

            case "radio":
                $helper = new FormRadio();
                return $helper->render($element);

            case "checkbox":
                $helper = new FormCheckbox();
                return $helper->render($element);

            case "date":
                $helper = new FormDate();
                return $helper->render($element);
        }

        throw new \InvalidArgumentException("Can not create Form helper for rendering");
    }

    /**
     *
     * @param Select $element
     * @return \Zend\Form\Element\Select
     */
    private static function _updateValueOptions(Select $element)
    {
        /**
         *
         * @var Select $element ;
         */
        $options = $element->getValueOptions();
        $options1 = [];
        $value = $element->getValue();

        if (count($options) > 0) {
            foreach ($options as $o) {

                if ($o['value'] == $value) {
                    $o['select'] = true;
                    $options1[] = $o;
                } else {
                    $options1[] = $o;
                }
            }
        }

        $element->setValueOptions($options1);
        return $element;
    }
}
