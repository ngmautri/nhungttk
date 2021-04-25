<?php
namespace Application\Form\AccountChart;

use Application\Form\Contracts\GenericForm;
use Application\Form\Render\AbstractFormRender;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof ChartForm) {
            throw new \InvalidArgumentException(\sprintf("ChartForm not given", ""));
        }

        $labelHelper = new FormLabel();

        $e = $form->getCoaCode();

        $fs_title = \sprintf("<strong>CHART OF ACCOUNT: %s</strong>", \strtoupper($e->getValue()));
        $this->append($this->openFieldSetTag("chart_header", $fs_title)); //

        $e1 = $form->getIsActive();
        $otherHtml = $this->drawElementOnly($e1, $labelHelper, $viewRender, 'col-sm-1', false, '<span>Is Acvice</span>');
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-2', true, $otherHtml);
        $this->append($htmlPart);

        $e = $form->getCoaName();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $this->append($htmlPart);
        $this->append($this->drawSeparator());

        $e = $form->getValidFrom();

        $e1 = $form->getValidTo();
        $htmlPart1 = $this->drawElement($e1, $labelHelper, $viewRender, 'col-sm-3');
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3', true, $htmlPart1);

        $this->append($htmlPart);

        $this->append($this->drawSeparator());

        $e = $form->getDescription();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3');
        $this->append($htmlPart);

        $this->append($this->drawSeparator());

        $this->append($this->addButtons($form, $viewRender));

        $this->append($this->closeFieldSetTag()); //

        return $this->getOutput();
    }
}
