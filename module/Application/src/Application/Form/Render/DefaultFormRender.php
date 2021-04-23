<?php
namespace Application\Form\Render;

use Application\Form\Contracts\GenericForm;
use Application\Form\Helper\FormHelperFactory;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DefaultFormRender extends AbstractFormRender
{

    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        $elements = $form->getElements();

        $html = "<div class=\"form-group margin-bottom\">\n
                    %s\n
                    <div class=\"col-sm-3\">%s</div>\n
              </div>\n";
        $f = '';
        $helper1 = new FormLabel();
        foreach ($elements as $e) {

            $l = $helper1->openTag($e);
            $l = $l . \ucwords($this->createLabel($e->getLabel(), $viewRender)) . ':';
            $l = $l . $helper1->closeTag();
            $v = FormHelperFactory::render($e);

            $f = $f . \sprintf($html, $l, $v);
        }

        $f = $f . $this->drawSeparator();
        $f = $f . $this->addButtons($form, $viewRender);
        return $f;
    }
}
