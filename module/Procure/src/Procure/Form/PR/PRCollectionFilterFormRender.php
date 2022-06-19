<?php
namespace Procure\Form\PR;

use Application\Form\Contracts\GenericForm;
use Application\Form\Render\AbstractFormRender;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRCollectionFilterFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof PRCollectionFilterForm) {
            throw new \InvalidArgumentException(\sprintf("PRCollectionFilterForm not given", ""));
        }

        $labelHelper = new FormLabel();

        $e = $form->getDocYear();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getDocMonth();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getDocStatus();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getBalance();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        // $e = $form->getDocMonth();
        // $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getResultPerPage();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $url = '/procure/pr-report/header-status-result';
        $resultDiv = 'result_div';
        $label = 'Filter';

        $otherBtn = $this->submitButtonWithCustomResultDiv($form, $viewRender, $url, $resultDiv, $label);
        $this->append($otherBtn);

        return $this->getOutput();
    }
}
