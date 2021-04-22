<?php
namespace Application\Form\Contracts;

use Application\Form\Helper\FormHelperFactory;
use Zend\Form\Form;
use Zend\Form\View\Helper\FormLabel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericForm extends Form
{

    abstract protected function addElements();

    public function refresh()
    {
        $this->addElements();
    }

    public function render($action = null)
    {
        $this->prepare();

        $helper = new \Zend\Form\View\Helper\Form();

        $f = "\n" . $helper->openTag($this) . "\n";

        $elements = $this->getElements();

        if ($elements == null) {
            throw new \InvalidArgumentException("Can not render form!");
        }

        foreach ($elements as $e) {

            $html = "<div class=\"form-group margin-bottom\">\n
                    %s\n
                    <div class=\"col-sm-3\">%s</div>\n
              </div>\n";

            $helper1 = new FormLabel();
            $type = $e->getAttribute("type");
            $helper2 = FormHelperFactory::getHelper($type);

            $v1 = $helper1->openTag($e);
            $v1 = $v1 . $e->getAttribute("name");
            $v1 = $v1 . $helper1->closeTag();
            echo $e->getValue();
            $v2 = $helper2->render($e);

            $f = $f . \sprintf($html, $v1, $v2);
        }

        $f = $f . $helper->closeTag($this);
        return $f;
    }
}
