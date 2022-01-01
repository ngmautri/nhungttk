<?php
namespace Procure\Application\Reporting\PR\CollectionRender;

use Application\Domain\Util\Collection\Render\AbstractRenderAsSpreadsheet;
use Procure\Application\DTO\Pr\PrHeaderDetailDTO;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultPrRenderAsExcel extends AbstractRenderAsSpreadsheet
{

    protected function createHeader()
    {}

    protected function createRowValue($element)
    {
        if (! $element instanceof PrHeaderDetailDTO) {
            return null;
        }

        return [

            'header1' => $element->getPrAutoNumber(),
            'header2' => $element->getPrName()
        ];
    }

    protected function createFooter()
    {}
}
