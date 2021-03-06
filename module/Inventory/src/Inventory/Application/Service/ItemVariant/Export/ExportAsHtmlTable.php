<?php
namespace Inventory\Application\Service\ItemVariant\Export;

use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use Application\Domain\Util\Collection\Contracts\FilterInterface;
use Application\Domain\Util\Collection\Export\AbstractExport;
use Application\Domain\Util\Collection\Filter\DefaultFilter;
use Application\Domain\Util\Collection\Formatter\NullFormatter;
use Inventory\Domain\Item\Variant\GenericVariant;
use Traversable;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ExportAsHtmlTable extends AbstractExport
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Contracts\ExportInterface::execute()
     */
    public function execute(Traversable $collection, FilterInterface $filter = null, ElementFormatterInterface $formatter = null)
    {
        if ($collection->isEmpty()) {
            return "Nothing found!";
        }

        if ($formatter == null) {
            $formatter = new NullFormatter();
        }

        if ($filter == null) {
            $filter = new DefaultFilter();
        }

        foreach ($collection as $element) {
            $element = $formatter->format($element);
        }

        $tmp = sprintf('Record %s to %s found!', $filter->getLimit() + 1, $filter->getLimit() + $collection->count());
        $result_msg = sprintf('<div style="color:graytext; padding-top:10pt;font:courier;">%s</div>', $tmp);

        $table = $result_msg . '
<table id="mytable26" class="table table-bordered table-hover" style="color:graytext; padding-top:10pt;font:courier;">
	<thead>
		<tr>
			<td><b>#</b></td>
            <td><b>Item</b></td>
 <td><b>SysNo.</b></td>
			<td><b>Attribute</b></td>

	        <td><b>Action</b></td>
		</tr>
	</thead>
	<tbody>
%s
    </tbody>
</table>
';

        $bodyHtml = '';
        $n = 0;

        foreach ($collection as $element) {

            /**@var GenericVariant $element ;*/

            $n ++;

            $element = $formatter->format($element);

            $showUrl = \sprintf("<a href=\"/application/account-chart/view?id=%s\">Show</a>", $element->getId());

            $bodyHtml = $bodyHtml . "<tr>\n";
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $n + $filter->getOffset());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", \ucwords($element->getItemName()));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", \ucwords($element->getSysNumber()));

            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", \strtoupper($element->getFullCombinedName()));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $showUrl);

            $bodyHtml = $bodyHtml . "</tr>";
        }

        return sprintf($table, $bodyHtml);
    }
}

