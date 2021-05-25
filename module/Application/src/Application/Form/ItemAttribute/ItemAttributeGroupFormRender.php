<?php
namespace Application\Form\ItemAttribute;

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
class ItemAttributeGroupFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof ItemAttributeGroupForm) {
            throw new \InvalidArgumentException(\sprintf("ItemAttributeGroupForm not given", ""));
        }

        $labelHelper = new FormLabel();

        $e = $form->getGroupCode();
        $bindObject = $form->getObject();

        $otherBtn = null;

        $fs_title = \sprintf("<strong>Attribute Group: %s</strong>", \strtoupper($e->getValue()));
        $this->append($this->openFieldSetTag("chart_header", $fs_title)); //

        $htmlPart = '<div style="border: 1px; padding: 2px; font-size: 9pt; background: url(/images/bg1.png); background-repeat: repeat-x; background-color: #FFFFFF;">';
        $this->append($htmlPart);

        $e1 = $form->getIsActive();
        $otherHtml = $this->drawElementOnly($e1, $labelHelper, $viewRender, 'col-sm-1', false, '<span>Is Acvice</span>');
        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-2', true, $otherHtml);
        $this->append($htmlPart);

        $e = $form->getGroupName();
        $htmlPart = $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $e = $form->getGroupName1();
        $htmlPart = $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $e = $form->getRemarks();
        $htmlPart = $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');
        $this->drawSeparator();

        // $this->append($this->addButtons($form, $viewRender));

        if ($form->getFormAction() == FormActions::SHOW) {

            $title = 'Back to warehouse list';
            $href = "/application/item-attribute/list";
            $cssClass = "";
            $style = "color: navy; margin-right:50pt";
            $icon = "fa fa-arrow-left";
            $label = "List";
            $otherBtn = $this->createCustomButton($form, $viewRender, $href, $cssClass, $style, $icon, $label, $title);
            $edit_url = sprintf("/application/item-attribute/update?id=%s", $bindObject->getId());

            $otherBtn = $otherBtn . sprintf(' <a class="btn btn-default btn-sm" style="color: navy;" href="%s">
              <i class="fa fa-edit" aria-hidden="true"></i> &nbsp;%s</a>', $edit_url, $viewRender->translate("Edit"));

            $add_url = sprintf("/application/item-attribute/add-member?rid=%s", $bindObject->getId());

            $otherBtn = $otherBtn . sprintf(' <a class="btn btn-default btn-sm" style="color: navy;" href="%s">
              <i class="fa fa-plus" aria-hidden="true"></i> &nbsp;%s</a>', $add_url, $viewRender->translate("Create Value"));
        }

        $this->append($this->addButtons($form, $viewRender, null, $otherBtn));
        $this->drawSeparator();
        $htmlPart = '</div>';
        $this->append($htmlPart);

        $this->append($this->closeFieldSetTag()); //

        return $this->getOutput();
    }
}
