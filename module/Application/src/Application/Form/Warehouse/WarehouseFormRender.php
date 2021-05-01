<?php
namespace Application\Form\Warehouse;

use Application\Domain\Contracts\FormActions;
use Application\Form\Contracts\GenericForm;
use Application\Form\Render\AbstractFormRender;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof WarehouseForm) {
            throw new \InvalidArgumentException(\sprintf("WarehouseForm not given", ""));
        }

        $htmlPart = '<div style="border: 1px; padding: 2px; font-size: 9pt; background: url(/images/bg1.png); background-repeat: repeat-x; background-color: #FFFFFF;">';
        $this->append($htmlPart);

        $labelHelper = new FormLabel();

        $bindObject = $form->getObject();

        if (! $bindObject instanceof WarehouseSnapshot) {
            throw new \InvalidArgumentException(\sprintf("Expected WarehouseSnapshot!", ""));
        }

        $e = $form->getWhCode();

        $fs_title = \sprintf("<strong>Warehouse: %s</strong>", \strtoupper($e->getValue()));
        $this->append($this->openFieldSetTag("chart_header", $fs_title)); //

        $e = $form->getRootId();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', false);

        $e = $form->getWhCode();

        $otherHtml = "";
        if ($bindObject->getIsLocked()) {
            $otherHtml = '<span class="label label-danger" style="font-size:8pt">Warehouse is locked!</span>';
        }

        $otherHtml1 = "";
        if ($bindObject->getIsDefault()) {
            $otherHtml1 = '<span class="label label-primary" style="font-size:8pt">Default Warehouse!</span>';
        }

        $htmlPart = $this->drawElement($e, $labelHelper, $viewRender, 'col-sm-2', true, $otherHtml . $otherHtml1);

        $this->append($htmlPart);

        $e = $form->getWhName();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $e = $form->getWhAddress();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-6');

        $e = $form->getWhCountry();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $e = $form->getWhController();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');

        $e = $form->getRemarks();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-3');
        $this->drawSeparator();

        $otherBtn = null;

        if ($form->getFormAction() == FormActions::SHOW) {

            $title = 'Back to warehouse list';
            $href = "/application/warehouse/list";
            $cssClass = "";
            $style = "color: navy; margin-right:50pt";
            $icon = "fa fa-arrow-left";
            $label = "List";
            $otherBtn = $this->createCustomButton($form, $viewRender, $href, $cssClass, $style, $icon, $label, $title);
            $edit_url = sprintf("/application/warehouse/update?id=%s", $bindObject->getId());

            $otherBtn = $otherBtn . sprintf(' <a class="btn btn-default btn-sm" style="color: navy;" href="%s">
              <i class="fa fa-edit" aria-hidden="true"></i> &nbsp;%s</a>', $edit_url, $viewRender->translate("Edit"));
        }

        if (! $bindObject->getIsLocked() && $form->getFormAction() == FormActions::EDIT && ! $bindObject->getIsDefault()) {
            // $lock_url = sprintf($this->baseUrl . "/application/warehouse/lock?ver=%s&rid=%s", $bindObject->getRevisionNo(), $bindObject->getId());
            $otherBtn = $otherBtn . sprintf(' <a class="btn btn-default btn-sm" style="color: red; margin-left:50pt" onclick="confirmModal(\'confirm_wh_lock\');" href="javascript:;">
                <i class="fa fa-lock" aria-hidden="true"></i> &nbsp;%s</a>', $viewRender->translate("Lock"));
        }

        if ($bindObject->getIsLocked() && $form->getFormAction() == FormActions::EDIT) {
            // $lock_url = sprintf($this->baseUrl . "/application/warehouse/lock?ver=%s&rid=%s", $bindObject->getRevisionNo(), $bindObject->getId());
            $otherBtn = $otherBtn . sprintf('<a class="btn btn-default btn-sm" style="color: nany;margin-left:50pt" onclick="confirmModal(\'confirm_wh_unlock\');" href="javascript:;">
                <i class="fa fa-unlock" aria-hidden="true"></i> &nbsp;%s</a>', $viewRender->translate("Unlock"));
        }

        $this->append($this->addButtons($form, $viewRender, null, $otherBtn));

        $this->drawSeparator();

        $this->append($this->closeFieldSetTag()); //

        $htmlPart = '</div>';
        $this->append($htmlPart);

        return $this->getOutput();
    }
}
