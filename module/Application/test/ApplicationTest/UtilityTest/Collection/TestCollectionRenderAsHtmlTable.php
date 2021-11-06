<?php
namespace ApplicationTest\UtilityTest\Collection;

use Application\Domain\Util\Collection\Render\AbstractRenderAsHtmlTable;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TestCollectionRenderAsHtmlTable extends AbstractRenderAsHtmlTable
{

    protected function createHeaderCell()
    {
        return "
        <td><b>Header1</b></td>
        <td><b>Header2</b></td>";
    }

    protected function createRowCell($element)
    {
        $cells = sprintf("<td>%s</td>\n", \ucwords($element->value1));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->value2));
        return $cells;
    }
}

