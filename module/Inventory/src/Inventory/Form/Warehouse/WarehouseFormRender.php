<?php
namespace Inventory\Form\Warehouse;

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

        $labelHelper = new FormLabel();

        $e = $form->getCoaCode();

        $fs_title = \sprintf("<strong>CHART OF ACCOUNT: %s</strong>", \strtoupper($e->getValue()));
        $this->append($this->openFieldSetTag("chart_header", $fs_title)); //

        $htmlPart = '<div style="border: 1px; padding: 2px; font-size: 9pt; background: url(/images/bg1.png); background-repeat: repeat-x; background-color: #FFFFFF;">';
        $this->append($htmlPart);

        $e1 = $form->getIsActive();
        $otherHtml = $this->drawElementOnly($e1, $labelHelper, $viewRender, 'col-sm-1', false, '<span>Is Acvice</span>');
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-2', true, $otherHtml);
        $this->append($htmlPart);

        $e = $form->getCoaName();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $this->append($htmlPart);

        $e = $form->getValidFrom();

        $e1 = $form->getValidTo();
        $htmlPart1 = $this->drawElement($e1, $labelHelper, $viewRender, 'col-sm-3');
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3', true, $htmlPart1);

        $this->append($htmlPart);

        $e = $form->getDescription();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3');
        $this->append($htmlPart);

        $this->append($this->addButtons($form, $viewRender));
        $this->drawSeparator();

        $htmlPart = '</div>';
        $this->append($htmlPart);

        $this->append($this->closeFieldSetTag()); //

        return $this->getOutput();
    }
}
