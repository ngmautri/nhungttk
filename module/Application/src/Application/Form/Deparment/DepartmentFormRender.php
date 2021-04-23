<?php
namespace Application\Form\Deparment;

use Application\Form\Contracts\GenericForm;
use Application\Form\Render\AbstractFormRender;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof DepartmentForm) {
            throw new \InvalidArgumentException(\sprintf("DepartmentForm not given", ""));
        }

        $labelHelper = new FormLabel();

        $e = $form->getDepartmentCode();
        $e1 = $form->getIsActive();
        $otherHtml = $this->drawElementOnly($e1, $labelHelper, $viewRender, 'col-sm-1', false, '<span>Is Acvice</span>');
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-1', true, $otherHtml);
        $this->append($htmlPart);

        $e = $form->getDepartmentName();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $this->append($htmlPart);

        $e = $form->getParentName();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3');
        $this->append($htmlPart);

        $this->append($this->drawSeparator());

        $e = $form->getRemarks();
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-3');
        $this->append($htmlPart);

        $this->append($this->drawSeparator());

        $this->append($this->addButtons($form, $viewRender));

        return $this->getOutput();
    }
}
