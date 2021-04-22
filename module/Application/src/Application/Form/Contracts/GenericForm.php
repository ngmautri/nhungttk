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
            $v1 = $helper1->openTag($e);
            $v1 = $v1 . $e->getAttribute("name");
            $v1 = $v1 . $helper1->closeTag();
            $v2 = FormHelperFactory::render($e);

            $f = $f . \sprintf($html, $v1, $v2);
        }

        $f = $f . $this->addSubmitButton();
        $f = $f . $helper->closeTag($this);
        return $f;
    }

    public function addSubmitButton()
    {
        return '
        <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default"><i class="fa fa-floppy-o" aria-hidden="true"></i>  Save </button>
        </div>
        </div>';
    }
}
