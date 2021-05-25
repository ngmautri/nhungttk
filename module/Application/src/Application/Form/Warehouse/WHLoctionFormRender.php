<?php
namespace Application\Form\Warehouse;

use Application\Form\Contracts\GenericForm;
use Application\Form\Render\AbstractFormRender;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WHLoctionFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof WHLocationForm) {
            throw new \InvalidArgumentException(\sprintf("WHLocationForm not given", ""));
        }

        $labelHelper = new FormLabel();

        $e = $form->getRootId();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', false);

        $e = $form->getMemberId();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', false);

        $e = $form->getLocationCode();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getLocationName();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getParentCode();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $e = $form->getParentCode1();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');


        $this->drawSeparator();

        $e = $form->getRemarks();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $this->drawSeparator();

        $this->append($this->addButtons($form, $viewRender));

        return $this->getOutput();
    }
}
