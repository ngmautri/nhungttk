<?php
namespace Application\Form\Collection;

use Application\Form\Contracts\GenericForm;
use Application\Form\Render\AbstractFormRender;
use Inventory\Form\ItemSerial\ItemSerialFilterForm;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultCollectionFilterFormRender extends AbstractFormRender
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Form\Render\AbstractFormRender::doRendering()
     */
    public function doRendering(GenericForm $form, PhpRenderer $viewRender)
    {
        if (! $form instanceof ItemSerialFilterForm) {
            throw new \InvalidArgumentException(\sprintf("ItemSerialFilterForm not given", ""));
        }

        $labelHelper = new FormLabel();

        $e = $form->getItemId();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', false);

        // $e = $form->getDocMonth();
        // $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $e = $form->getResultPerPage();
        $this->drawAndAppendElement($e, $labelHelper, $viewRender, 'col-sm-2', true);

        $url = '/inventory/item-serial/list1';
        $resultDiv = 'item_serial_div';
        $label = 'Filter';

        $otherBtn = $this->submitButtonWithCustomResultDiv($form, $viewRender, $url, $resultDiv, $label);
        $this->append($otherBtn);

        return $this->getOutput();
    }
}
