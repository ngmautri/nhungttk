<?php
namespace Application\Form\Contracts;

use Application\Domain\Util\Translator;
use Application\Form\Helper\FormHelperFactory;
use Zend\Form\Form;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericForm extends Form
{

    protected $id;

    protected $redirectUrl;

    abstract protected function addElements();

    public function refresh()
    {
        $this->addElements();
    }

    /**
     *
     * @param string $action
     * @param ViewModel $view
     * @throws \InvalidArgumentException
     * @return string
     */
    public function renderForView($action = null, PhpRenderer $render)
    {
        if ($render == null) {
            throw new \InvalidArgumentException("PhpRenderer is required!");
        }
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

        $f = $f . $this->drawSeperator();

        $f = $f . \sprintf($html, "<label class=\"control-label col-sm-2\" for=\"inputTag\"></label>", $this->submitButton($action, $render) . $this->cancelButton($action, $render));
        $f = $f . $helper->closeTag($this);
        return $f;
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

        $f = $f . $this->drawSeperator();
        $f = $f . $helper->closeTag($this);
        return $f;
    }

    public function submitButton($action, PhpRenderer $render, $cssClass = null)
    {
        $cssClass = 'btn btn-primary btn-sm';
        return sprintf(' <a class="%s" style="color: white" onclick="submitForm(\'%s\');" href="javascript:;">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $cssClass, $this->getId(), $this->createLabel(Translator::translate("Save"), $render));
    }

    public function cancelButton($action = null, PhpRenderer $render, $cssClass = null)
    {
        $cssClass = 'btn btn-default btn-sm';
        return sprintf(' <a class="%s" style="color: black" href="%s">
        <i class="fa fa-arrow-circle-left" aria-hidden="true"></i> &nbsp;%s</a>', $cssClass, $this->getRedirectUrl(), $this->createLabel(Translator::translate("Cancel"), $render));
    }

    public function updateButton($action = null, PhpRenderer $render, $cssClass = null)
    {
        $cssClass = 'btn btn-primary btn-sm';
        return sprintf(' <a class="%s" style="color: white" onclick="submitForm(\'%s\');" href="javascript:;">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $cssClass, $this->getId(), $this->createLabel(Translator::translate("Save"), $render));
    }

    public function drawSeperator()
    {
        return '<hr style="margin: 5pt 1pt 5pt 1pt;">';
    }

    private function createLabel($label, PhpRenderer $render)
    {
        if ($render == null) {
            return $label;
        }

        return $render->translate($label);
    }

    // =========================
    // =========================

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     *
     * @param mixed $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }
}
