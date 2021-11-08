<?php
namespace Inventory\Application\Reporting\ItemSerial\CollectionRender;

use Application\Domain\Util\Collection\Render\AbstractRenderAsSpreadsheet;
use Inventory\Domain\Item\Serial\GenericSerial;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialRenderAsExcel extends AbstractRenderAsSpreadsheet
{

    protected function createHeader()
    {}

    protected function createRowValue($element)
    {
        if (! $element instanceof GenericSerial) {
            return null;
        }

        return [
            'header1' => $element->getInvoiceSysNumber(),
            'header2' => $element->getItem()
        ];
    }

    protected function createFooter()
    {}
}
