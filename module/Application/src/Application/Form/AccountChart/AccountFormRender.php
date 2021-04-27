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
class AccountFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof AccountForm) {
            throw new \InvalidArgumentException(\sprintf("AccountForm not given", ""));
        }

        $labelHelper = new FormLabel();

        $e = $form->getRootId();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-2', false);
        $this->append($htmlPart);

        $e = $form->getAccountNumber();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-2', true);
        $this->append($htmlPart);

        $e = $form->getAccountName();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-2', true);
        $this->append($htmlPart);

        $e = $form->getParentAccountNumber();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3');
        $this->append($htmlPart);
        $this->append($this->drawSeparator());

        $e = $form->getDescription();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3');
        $this->append($htmlPart);

        $this->append($this->drawSeparator());

        $this->append($this->addButtons($form, $viewRender));

        return $this->getOutput();
    }
}
