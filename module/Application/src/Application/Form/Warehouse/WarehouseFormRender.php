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
class WarehouseFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof WarehouseForm) {
            throw new \InvalidArgumentException(\sprintf("WarehouseForm not given", ""));
        }

        $htmlPart = '<div style="border: 1px; padding: 2px; font-size: 9pt; background: url(/images/bg1.png); background-repeat: repeat-x; background-color: #FFFFFF;">';
        $this->append($htmlPart);

        $labelHelper = new FormLabel();

        $e = $form->getWhCode();

        $fs_title = \sprintf("<strong>Warehouse: %s</strong>", \strtoupper($e->getValue()));
        $this->append($this->openFieldSetTag("chart_header", $fs_title)); //

        $e = $form->getWhName();
        $htmlPart = $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $e = $form->getWhAddress();
        $htmlPart = $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-6');

        $e = $form->getWhCountry();
        $htmlPart = $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $e = $form->getWhController();
        $htmlPart = $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $e = $form->getRemarks();
        $htmlPart = $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $this->append($this->addButtons($form, $viewRender));
        $this->drawSeparator();

        $this->append($this->closeFieldSetTag()); //

        $htmlPart = '</div>';
        $this->append($htmlPart);

        return $this->getOutput();
    }
}
