<?php
namespace Inventory\Application\Service\ItemVariant\Export;

use Application\Domain\Company\ItemAttribute\GenericAttributeGroup;
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

        $formId = 'variant_create_form';

        $tmp = sprintf('Record %s to %s found!', $filter->getLimit() + 1, $filter->getLimit() + $collection->count());

        $result_msg = '<div style="color:graytext; padding:10pt;">%s</div>';

        $table = '
<table id="mytable26" class="table table-bordered table-hover">
   <thead>
      <tr>
         <td><b>#</b></td>
         <td><b>Attribute</b></td>
            <td><b>Value</b></td>
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

            /**@var GenericAttributeGroup $element ;*/

            $n ++;

            $element = $formatter->format($element);

            $showUrl = \sprintf("<a href=\"/application/item-attribute/view?id=%s\">Show</a>", $element->getId());

            $bodyHtml = $bodyHtml . "<tr>\n";
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $n + $filter->getOffset());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $element->getGroupName());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $this->_createMultiSelect($element));

            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $showUrl);

            $bodyHtml = $bodyHtml . "</tr>";
        }

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
        // $n = 'attribute_group';
        $select = \sprintf('<select name="%s[]" data-placeholder="Select multiple attribute value..." class="chosen-select" multiple>', $n);

        foreach ($collection as $e) {
            $format = '<option value="%s">%s</option>';
            $select = $select . \sprintf($format, $e->getId(), $e->getAttributeName());
        }
        $select = $select . '</select>';
        return $select;
    }
}

