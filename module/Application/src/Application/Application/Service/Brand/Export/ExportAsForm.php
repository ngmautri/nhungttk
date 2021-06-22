<?php
namespace Application\Application\Service\Brand\Export;

use Application\Domain\Company\Brand\GenericBrand;
use Application\Domain\Company\ItemAttribute\GenericAttributeGroup;
use Application\Domain\Util\Translator;
use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use Application\Domain\Util\Collection\Contracts\FilterInterface;
use Application\Domain\Util\Collection\Export\AbstractExport;
use Application\Domain\Util\Collection\Filter\DefaultFilter;
use Application\Domain\Util\Collection\Formatter\NullFormatter;
use Traversable;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ExportAsForm extends AbstractExport
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

        $cssClass = 'btn btn-primary btn-sm';
        $formId = 'variant_create_form';
        $submitBtn = sprintf(' <a class="%s" style="color: white" onclick="submitForm(\'%s\');" href="javascript:;">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $cssClass, $formId, (Translator::translate("Generate")));

        $result_msg = sprintf('<div style="color:graytext; padding:10pt;">%s</div><form id="%s">', $tmp, $formId);

        $table = $result_msg . '
<table id="mytable26" class="table table-bordered table-hover">
	<thead>
		<tr>
			<td><b>#</b></td>
			<td><b>Attribute</b></td>

	        <td><b>Action</b></td>
		</tr>
	</thead>
	<tbody>
%s
    </tbody>
</table>
</form>';

        $bodyHtml = '';
        $n = 0;

        foreach ($collection as $element) {

            /**@var GenericBrand $element ;*/

            $n ++;

            $element = $formatter->format($element);

            $showUrl = \sprintf("<a href=\"/application/brand/view?id=%s\">Show</a>", $element->getId());

            $bodyHtml = $bodyHtml . "<tr>\n";
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $n + $filter->getOffset());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $element->getBrandName());
            // $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $this->_createMultiSelect($element));

            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $showUrl);

            $bodyHtml = $bodyHtml . "</tr>";
        }

        $bodyHtml = $bodyHtml . sprintf("<tr>%s</tr>", "");

        return sprintf($table, $bodyHtml);
    }

    /**
     *
     * @param GenericAttributeGroup $element
     */
    private function _createMultiSelect(GenericAttributeGroup $element)
    {
        $collection = $element->getLazyAttributeCollection();
        if ($collection->isEmpty()) {
            return '';
        }

        // $n = Inflector::camelize($element->getGroupName());
        $n = 'attribute_group_' . $element->getId();

        $select = \sprintf('<select name="%s[]" data-placeholder="Select multiple attribute value..." class="chosen-select" multiple>', $n);

        foreach ($collection as $e) {
            $format = '<option value="%s">%s</option>';
            $select = $select . \sprintf($format, $e->getId(), $e->getAttributeName());
        }
        $select = $select . '</select>';
        return $select;
    }
}

