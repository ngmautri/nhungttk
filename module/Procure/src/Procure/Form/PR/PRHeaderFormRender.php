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
class PRHeaderFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof PRHeaderForm) {
            throw new \InvalidArgumentException(\sprintf("PRHeaderForm not given", ""));
        }

        $labelHelper = new FormLabel();

        $e = $form->getPrNumber();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getDocDate();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getKeywords();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getWarehouse();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getDepartment();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getRemarks();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $url = '/procure/pr/create';
        $resultDiv = 'pr_row_div';
        $label = 'Save  ';
        $otherBtn = null;

        $this->append($this->addButtons($form, $viewRender, null, $otherBtn));

        return $this->getOutput();
    }
}
