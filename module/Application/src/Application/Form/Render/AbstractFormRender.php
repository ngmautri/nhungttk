<?php
namespace Application\Form\Render;

use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\Translator;
use Application\Form\Contracts\GenericForm;
use Application\Form\Helper\FormHelperFactory;
use Zend\Form\Element;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractFormRender implements FormRenderInterface
{

    protected $output = '';

    /*
     * |=============================
     * |Abstract
     * |
     * |=============================
     */
    abstract function doRendering(GenericForm $form, PhpRenderer $viewRender);

    /*
     * |=============================
     * |Render
     * |
     * |=============================
     */

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\FormRenderInterface::render()
     */
    public function render(GenericForm $form, PhpRenderer $viewRender = null)
    {
        $this->ensureRendering($form, $viewRender);
        $form->prepare();

        $helper = new \Zend\Form\View\Helper\Form();
        $output = $this->formOpenTag($helper, $form, $viewRender);
        $output = $output . $this->doRendering($form, $viewRender);
        $output = $output . $this->formCloseTag($helper, $form, $viewRender);

        return $output;
    }

    /*
     * |=============================
     * |Fieldset
     * |
     * |=============================
     */
    protected function openFieldSetTag($id, $title, $collapse = 'in')
    {
        $fs = '<fieldset>
<legend style="font-size: 9.5pt; color: gray;"><small>
<span class="glyphicon glyphicon-triangle-right"></span></small>&nbsp;
<a href="#%s" class="" data-toggle="collapse">%s</a>
</legend>
<div id="%s" class="collapse %s">';

        return \sprintf($fs, $id, $title, $id, $collapse);
    }

    protected function closeFieldSetTag()
    {
        return '</div></fieldset>';
    }

    /*
     * |=============================
     * | Drawing
     * |
     * |=============================
     */
    public function append($htmlPart)
    {
        $output = $this->getOutput();
        $output = $output . $htmlPart;
        $this->output = $output;
    }

    public function drawSeparator()
    {
        $this->append($this->_drawSeparator());
    }

    /**
     * drawElementOnly
     */
    protected function drawElementOnly(Element $e, FormLabel $labelHelper, $viewRender, $cssClass = 'col-sm-3', $showLabel = true, $otherHtml = '')
    {
        $div = '
%s
<div class="%s">
    %s %s
</div>
';
        $labelHtml = '';

        if ($showLabel) {
            $labelHtml = $labelHelper->openTag($e);
            $labelHtml = $labelHtml . \ucwords($this->createLabel($e->getLabel(), $viewRender)) . ":";
            $labelHtml = $labelHtml . $labelHelper->closeTag();
        }

        $elementHtml = FormHelperFactory::render($e);

        return \sprintf($div, $labelHtml, $cssClass, $elementHtml, $otherHtml);
    }

    /**
     * drawElementOnly
     */
    protected function drawElement(Element $e, FormLabel $labelHelper, $viewRender, $cssClass = 'col-sm-3', $showLabel = true, $otherHtml = null)
    {
        $labelHtml = '';

        if ($showLabel) {
            $labelHtml = $labelHelper->openTag($e);
            $labelHtml = $labelHtml . \ucwords($this->createLabel($e->getLabel(), $viewRender)) . ":";
            $labelHtml = $labelHtml . $labelHelper->closeTag();
        }

        $elementHtml = FormHelperFactory::render($e);

        $required = $e->getAttribute('required');
        if ($required) {
            $div = $this->createRequiredDiv($labelHtml, $elementHtml, $cssClass, $otherHtml);
        } else {
            $div = $this->createNormalDiv($labelHtml, $elementHtml, $cssClass, $otherHtml);
        }

        return $div;
    }

    /**
     * drawAndAppendElement
     */
    protected function drawAndAppendElement($e, $labelHelper, $viewRender, $cssClass = 'col-sm-3', $showLabel = true, $otherHtml = null)
    {
        $labelHtml = '';

        if ($showLabel) {
            $labelHtml = $labelHelper->openTag($e);
            $labelHtml = $labelHtml . \ucwords($this->createLabel($e->getLabel(), $viewRender)) . ":";
            $labelHtml = $labelHtml . $labelHelper->closeTag();
        }

        $elementHtml = FormHelperFactory::render($e);
        $required = $e->getAttribute('required');

        if ($required) {
            $div = $this->createRequiredDiv($labelHtml, $elementHtml, $cssClass, $otherHtml);
        } else {
            $div = $this->createNormalDiv($labelHtml, $elementHtml, $cssClass, $otherHtml);
        }

        $this->append($div);
    }

    /**
     * createNormalDiv
     * ==================
     */
    protected function createNormalDiv($labelHtml, $elementHtml, $cssClass, $otherHtml = null)
    {
        $div = '<div class="form-group margin-bottom">%s
                    <div class="%s">
                        %s
                    </div>
                    %s
                </div>';
        return \sprintf($div, $labelHtml, $cssClass, $elementHtml, $otherHtml);
    }

    /**
     * createRequiredDiv
     * ==================
     */
    protected function createRequiredDiv($labelHtml, $elementHtml, $cssClass, $otherHtml = null)

    {
        $div = '<div class="form-group margin-bottom required">
                    s
                    <div class="%s">
                    %s
                    </div>
                    %s
                </div>';

        return \sprintf($div, $labelHtml, $cssClass, $elementHtml, $otherHtml);
    }

    /*
     * |=============================
     * |Helper
     * |
     * |=============================
     */
    protected function formOpenTag(\Zend\Form\View\Helper\Form $helper, GenericForm $form, PhpRenderer $viewRender)
    {
        return "\n" . $helper->openTag($form) . "\n";
    }

    protected function formCloseTag(\Zend\Form\View\Helper\Form $helper, GenericForm $form, PhpRenderer $viewRender)
    {
        return "\n" . $helper->closeTag($form) . "\n";
    }

    /*
     * |=============================
     * |Button
     * |
     * |=============================
     */

    /**
     *
     * @param GenericForm $form
     * @param PhpRenderer $viewRender
     * @return string|NULL
     */
    protected function addButtons(GenericForm $form, PhpRenderer $viewRender, $cssClass = null, $otherButtons = null)
    {
        if ($form == null) {
            return null;
        }

        $html = "<div class=\"form-group margin-bottom\">\n
                    %s\n
                    <div class=\"col-sm-3\">%s</div>\n
              </div>\n";

        switch ($form->getFormAction()) {
            case FormActions::SHOW:
                return \sprintf($html, "<label class=\"control-label col-sm-2\" for=\"inputTag\"></label>", $otherButtons);

            case FormActions::ADD:
                return \sprintf($html, "<label class=\"control-label col-sm-2\" for=\"inputTag\"></label>", $this->submitButton($form, $viewRender, $cssClass) . $this->cancelButton($form, $viewRender, $cssClass) . $otherButtons);

            case FormActions::EDIT:
                return \sprintf($html, "<label class=\"control-label col-sm-2\" for=\"inputTag\"></label>", $this->submitButton($form, $viewRender, $cssClass) . $this->cancelButton($form, $viewRender, $cssClass) . $otherButtons);
        }

        return null;
    }

    /**
     * when submit form with AJAX, button with type submit must be used! otherwise, submit event is not triggered!
     * ==================
     */
    protected function submitButtonWithCustomResultDiv(GenericForm $form, PhpRenderer $viewRender, $url, $resultDiv, $label, $cssClass = null)
    {
        $html = "<div class=\"form-group margin-bottom\">\n
                    %s\n
                    <div class=\"col-sm-3\">%s</div>\n
              </div>\n";

        $cssClass = 'btn btn-default btn-sm';

        $b = sprintf(' <button type="submit" class="%s" style="color: black; font-size:9pt;" onclick="submitAjaxGETForm(\'%s\');" href="javascript:;">
        <i class="fa fa-filter" aria-hidden="true"></i> &nbsp;%s</button>', $cssClass, $form->getId(), $this->createLabel($label, $viewRender));

        return sprintf($html, "<label class=\"control-label col-sm-2\" for=\"inputTag\"></label>", $b);
    }

    /**
     * submitButton
     * ==================
     */
    protected function submitButton(GenericForm $form, PhpRenderer $viewRender, $cssClass = null)
    {
        $cssClass = 'btn btn-primary btn-sm';
        return sprintf(' <a class="%s" style="color: white" onclick="submitForm(\'%s\');" href="javascript:;">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $cssClass, $form->getId(), $this->createLabel(Translator::translate("Save"), $viewRender));
    }

    /**
     * cancelButton
     * ==================
     */
    protected function cancelButton(GenericForm $form, PhpRenderer $viewRender, $cssClass = null)
    {
        $cssClass = 'btn btn-default btn-sm';
        return sprintf(' <a class="%s" style="color: black" href="%s">
        <i class="fa fa-arrow-circle-left" aria-hidden="true"></i> &nbsp;%s</a>', $cssClass, $form->getRedirectUrl(), $this->createLabel(Translator::translate("Cancel"), $viewRender));
    }

    /**
     * updateButton
     * ==================
     */
    protected function updateButton(GenericForm $form, PhpRenderer $viewRender, $cssClass = null)
    {
        $cssClass = 'btn btn-default btn-sm';
        return sprintf(' <a class="%s" style="color: navy" onclick="submitForm(\'%s\');" href="javascript:;">
        <i class="fa fa-edit" aria-hidden="true"></i> &nbsp;%s</a>', $cssClass, $form->getId(), $this->createLabel(Translator::translate("Edit"), $viewRender));
    }

    /**
     * createCustomButton
     * ==================
     */
    protected function createCustomButton(GenericForm $form, PhpRenderer $viewRender, $href, $cssClass, $style, $icon, $label, $title)
    {
        $cssClass = 'btn btn-default btn-sm';

        return sprintf(' <a title="%s" class="%s" style="%s" href="%s">
        <i class="%s" aria-hidden="true"></i> &nbsp;%s</a>', $title, $cssClass, $style, $href, $icon, $label);
    }

    protected function createCustomButtonWithOnclick(GenericForm $form, PhpRenderer $viewRender, $onclick, $cssClass, $style, $icon, $label, $title)
    {
        $cssClass = 'btn btn-default btn-sm';

        return sprintf(' <a title="%s" class="%s" style="%s" href="#" onclick="%s">
        <i class="%s" aria-hidden="true"></i> &nbsp;%s</a>', $title, $cssClass, $style, $onclick, $icon, $label);
    }

    /*
     * |=============================
     * |Helper
     * |
     * |=============================
     */
    protected function ensureRendering(GenericForm $form, PhpRenderer $viewRender = null)
    {
        if ($form == null) {
            throw new \InvalidArgumentException(\sprintf("Form not given", ""));
        }

        $elements = $form->getElements();

        if ($elements == null) {
            throw new \InvalidArgumentException(\sprintf("Form [%s] does not have any elements!", $form->getId()));
        }
    }

    protected function _drawSeparator()
    {
        return '<hr style="margin: 5pt 1pt 5pt 1pt;">';
    }

    protected function createLabel($label, PhpRenderer $viewRender)
    {
        if ($viewRender == null) {
            return $label;
        }

        return $viewRender->translate($label);
    }

    /*
     * |=============================
     * |Setter and Getter
     * |
     * |=============================
     */

    /**
     *
     * @return string
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     *
     * @param string $output
     */
    protected function setOutput($output)
    {
        $this->output = $output;
    }
}
