<?php
namespace Application\Form\Helper;

use Zend\Form\Element;
use Zend\Form\View\Helper\FormHidden;
use Zend\Form\View\Helper\FormInput;
use Zend\Form\View\Helper\FormSelect;
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

    public static function render($element)
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
                echo $value = $element->getValue();
                return $helper->render($element);

            case "hidden":
                $helper = new FormHidden();
                return $helper->render($element);
            case "textarea":
                $helper = new FormTextarea();
                return $helper->render($element);
        }

        throw new \InvalidArgumentException("Can not create Form helper");
    }
}
