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
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', false);

        $e = $form->getMemberId();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', false);

        $e = $form->getAccountNumber();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getAccountName();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getParentAccountNumber();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $this->drawSeparator();

        $e = $form->getDescription();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $this->drawSeparator();

        $this->append($this->addButtons($form, $viewRender));

        return $this->getOutput();
    }
}
