<?php
namespace Procure\Application\Service\PR\RowCollectionRender;

use Application\Domain\Util\Collection\Render\AbstractRenderAsSpreadsheet;
use Procure\Domain\PurchaseRequest\PRRow;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultPrRowRenderAsExcel extends AbstractRenderAsSpreadsheet
{

    protected function createHeader()
    {}

    protected function createRowValue($element)
    {
        if (! $element instanceof PRRow) {
            return null;
        }

        return [
            'header1' => $element->getRowIdentifer(),
            'header2' => $element->getItemName()
        ];
    }

    protected function createFooter()
    {}
}
