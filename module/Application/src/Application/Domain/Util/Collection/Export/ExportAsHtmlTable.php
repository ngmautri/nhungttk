<?php
namespace Application\Domain\Util\Collection\Export;

use Application\Domain\Util\Collection\GenericCollection;
use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use Application\Domain\Util\Collection\Contracts\FilterInterface;
use Application\Domain\Util\Collection\Filter\DefaultFilter;
use Application\Domain\Util\Collection\Formatter\NullFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ExportAsHtmlTable extends AbstractExport
{

    public function execute(GenericCollection $collection, FilterInterface $filter = null, ElementFormatterInterface $formatter = null)
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

        $tmp = sprintf('Record %s to %s of %s found!', $filter->getLimit() + 1, $filter->getLimit() + $collection->count());
        $result_msg = sprintf('<div style="color:graytext; padding-top:10pt;">%s</div>', $tmp);

        $table = $result_msg;
        $table = $table . $this->getTableHeader();
        $table = $table . '<tbody>%s</tbody></table>';
        return sprintf($table, $this->getTableBody());
    }

    /**
     *
     * @return mixed
     */
    public function getTableHeader()
    {
        return $this->tableHeader;
    }

    /**
     *
     * @param mixed $tableHeader
     */
    public function setTableHeader($tableHeader)
    {
        $this->tableHeader = $tableHeader;
    }

    /**
     *
     * @return mixed
     */
    public function getTableBody()
    {
        return $this->tableBody;
    }

    /**
     *
     * @param mixed $tableBody
     */
    public function setTableBody($tableBody)
    {
        $this->tableBody = $tableBody;
    }
}

