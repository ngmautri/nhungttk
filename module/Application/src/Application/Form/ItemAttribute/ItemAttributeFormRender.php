<?php
namespace Application\Form\ItemAttribute;

use Application\Form\Contracts\GenericForm;
use Application\Form\Render\AbstractFormRender;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof ItemAttributeForm) {
            throw new \InvalidArgumentException(\sprintf("ItemAttributeForm not given", ""));
        }

        $labelHelper = new FormLabel();

        $e = $form->getRootId();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', false);

        $e = $form->getMemberId();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', false);

        $e = $form->getAttributeCode();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getAttributeName();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getAttributeName1();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $this->drawSeparator();

        $e = $form->getRemarks();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $this->drawSeparator();

        $this->append($this->addButtons($form, $viewRender));

        return $this->getOutput();
    }
}
