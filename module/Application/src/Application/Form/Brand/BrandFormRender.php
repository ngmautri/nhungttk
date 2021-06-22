<?php
namespace Application\Form\Brand;

use Application\Domain\Contracts\FormActions;
use Application\Form\Contracts\GenericForm;
use Application\Form\Render\AbstractFormRender;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof BrandForm) {
            throw new \InvalidArgumentException(\sprintf("BrandForm not given", ""));
        }

        $labelHelper = new FormLabel();
        $bindObject = $form->getObject();

        $e = $form->getRootId();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', false);

        $e = $form->getBrandName();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getBrandName1();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $this->drawSeparator();

        $e = $form->getRemarks();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $this->drawSeparator();

        $otherBtn = null;

        if ($form->getFormAction() == FormActions::SHOW) {

            $title = 'Back to brad list';
            $href = "/application/brand/list";
            $cssClass = "";
            $style = "color: navy; margin-right:50pt";
            $icon = "fa fa-arrow-left";
            $label = "List";
            $otherBtn = $this->createCustomButton($form, $viewRender, $href, $cssClass, $style, $icon, $label, $title);
            $edit_url = sprintf("/application/brand/update?id=%s", $bindObject->getId());

            $otherBtn = $otherBtn . sprintf(' <a class="btn btn-default btn-sm" style="color: navy;" href="%s">
              <i class="fa fa-edit" aria-hidden="true"></i> &nbsp;%s</a>', $edit_url, $viewRender->translate("Edit"));
        }

        $otherBtn = $otherBtn . sprintf('<a class="btn btn-default btn-sm" style="color: red;margin-left:10pt" onclick="confirmModal(\'confirm_remove_modal_sm\');" href="javascript:;">
                <i class="fa fa-remove" aria-hidden="true"></i> &nbsp;%s</a>', $viewRender->translate("Remove"));

        $this->append($this->addButtons($form, $viewRender, null, $otherBtn));

        return $this->getOutput();
    }
}
