<?php
namespace Application\Application\Service\AccountChart\Export;

use Application\Domain\Company\AccountChart\ChartSnapshot;
use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use Application\Domain\Util\Collection\Contracts\FilterInterface;
use Application\Domain\Util\Collection\Export\AbstractExport;
use Application\Domain\Util\Collection\Filter\DefaultFilter;
use Application\Domain\Util\Collection\Formatter\NullFormatter;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ExportAsGird extends AbstractExport
{

    public function execute(ArrayCollection $collection, FilterInterface $filter = null, ElementFormatterInterface $formatter = null)
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

        $tmp = sprintf('Record %s to %s found!', $filter->getLimit() + 1, $filter->getLimit() + $collection->count());
        $result_msg = sprintf('<div style="color:graytext; padding-top:10pt;">%s</div>', $tmp);

        $table = $result_msg . '
<table id="mytable26" class="table table-bordered table-hover">
	<thead>
		<tr>
			<td><b>#</b></td>
			<td><b>Chart</b></td>
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

            /**@var ChartSnapshot $element ;*/

            $n ++;

            $element = $formatter->format($element);

            $showUrl = \sprintf("<a href=\"/application/account-chart/view?id=%s\">Show</a>", $element->getId());

            $bodyHtml = $bodyHtml . "<tr>\n";
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $n + $filter->getOffset());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $element->getCoaCode());

            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $showUrl);

            $bodyHtml = $bodyHtml . "</tr>";
        }

        return sprintf($table, $bodyHtml);
    }
}

