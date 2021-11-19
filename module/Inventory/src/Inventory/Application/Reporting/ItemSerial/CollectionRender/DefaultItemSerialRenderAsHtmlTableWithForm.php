<?php
namespace Inventory\Application\Reporting\ItemSerial\CollectionRender;

use Application\Domain\Util\Collection\Render\AbstractRenderAsHtmlTableWithSelection;
use Inventory\Domain\Item\Serial\GenericSerial;
use Inventory\Domain\Item\Serial\SerialSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultItemSerialRenderAsHtmlTableWithForm extends AbstractRenderAsHtmlTableWithSelection
{

    protected function createHeaderCell()
    {
        return "
        <td style=\"width=20px;\"><input type=\"checkbox\" id=\"checkAll\"></td>
        <td><b>Invoice Number</b></td>
        <td><b>No.</b></td>
        <td><b>No2</b></td>
        <td><b>No3</b></td>       
        <td><b>Action</b></td>";
    }

    protected function createRowCell($ele)
    {
        if (! $ele instanceof GenericSerial) {
            return null;
        }

        /**
         *
         * @var SerialSnapshot $element
         */
        $element = $ele->makeSnapshot();

        $cells = '';
        $showUrl = '';
        $sysNumber = '';

        $format = "/inventory/item-serial/edit?token=%s&entity_id=%s";
        $href = sprintf($format, $element->getToken(), $element->getId());
        $format = '<a target="_blank" href="%s">Show</a>';
        $showUrl = sprintf($format, $href);

        $format = '<input class="checkbox1" type="checkbox" name="ids[]" value="%s" class="checkbox" />';
        $s = sprintf($format, $element->getId());

        $cells = $cells . sprintf("<td>%s</td>\n", $s);
        $cells = $cells . sprintf("<td>%s</td>\n", \ucwords($element->getInvoiceSysNumber()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getSerialNumber()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getCreatedOn()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getVendorName()));
        $cells = $cells . sprintf("<td>%s</td>\n", $showUrl);
        return $cells;
    }

    protected function createHeader()
    {}

    protected function createFooter()
    {}

    protected function createFooter2()
    {
        $format = '<script>%s</script>';

        $c = "$(\"#checkAll\").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        })";

        $c1 = "
        $(\".checkbox1\").change(function() {
               //countSelected();            
        });";

        return sprintf($format, $c . $c1);
    }

    protected function createFooter1()
    {
        $format = '<script>%s</script>';

        $c = "$(\"#checkAll\").click(function () {
     $('input:checkbox').not(this).prop('checked', this.checked);});";

        return sprintf($format, $c);
    }
}

