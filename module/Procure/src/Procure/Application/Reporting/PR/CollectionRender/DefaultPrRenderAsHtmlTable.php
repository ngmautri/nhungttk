<?php
namespace Procure\Application\Reporting\PR\CollectionRender;

use Application\Application\Helper\Form\FormHelper;
use Application\Domain\Util\Collection\Render\AbstractRenderAsHtmlTable;
use Procure\Application\DTO\Pr\PrHeaderDetailDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultPrRenderAsHtmlTable extends AbstractRenderAsHtmlTable
{

    protected function createHeaderCell()
    {
        return "
        <td><b>Pr Number</b></td>
        <td><b>No.</b></td>
        <td><b>No2</b></td>
        <td><b>No3</b></td>  
        <td><b>No2</b></td>
        <td><b>No3</b></td> 
 <td><b>No3</b></td>         
 <td><b>No3</b></td>   
        <td><b>Action</b></td>";
    }

    protected function createRowCell($ele)
    {
        if (! $ele instanceof PrHeaderDetailDTO) {
            return null;
        }

        /**
         *
         * @var PrHeaderDetailDTO $element
         */
        $element = $ele;

        $cells = '';
        $showUrl = '';
        $sysNumber = '';

        $format = "/procure/pr/view1?entity_token=%s&entity_id=%s";
        $href = sprintf($format, $element->getToken(), $element->getId());
        $format = '<a target="_blank" href="%s">Show</a>';
        $showUrl = sprintf($format, $href);

        if ($element->getTotalRows() > 0) {
            $completion = $element->getGrCompletedRows() / $element->getTotalRows();
        } else {
            $completion = 0;
        }
        $progress_div = FormHelper::createProgressDiv($completion, null);

        $cells = $cells . sprintf("<td style='width:80pt;'>%s</td>\n", $progress_div);
        $cells = $cells . sprintf("<td>%s</td>\n", \ucwords($element->getPrAutoNumber()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getPrName()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getTotalRows()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getGrCompletedRows()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getApCompletedRows()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getCreatedOn()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getCreatedByName()));
        $cells = $cells . sprintf("<td>%s</td>\n", $showUrl);
        return $cells;
    }
}

