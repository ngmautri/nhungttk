<?php
namespace Procure\Application\Service\Search\ZendSearch\PO;

use Application\Service\AbstractService;
use Procure\Domain\DocSnapshot;
use Procure\Domain\RowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\Service\Search\PoSearchIndexInterface;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
use ZendSearch\Lucene\Document\Field;
use Exception;
use Procure\Domain\GenericRow;
use Procure\Domain\PurchaseOrder\PORow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoSearchIndexImpl extends AbstractService implements PoSearchIndexInterface
{

    public function updateIndex($entityId, $isNew = true, $optimized = false)
    {}

    public function optimizeIndex()
    {}

    private function _createDoc(\ZendSearch\Lucene\SearchIndexInterface $index, GenericRow $row)
    {
        $doc = new Document();

        if (! $row instanceof PORow) {
            throw new \InvalidArgumentException("PoRow empty");
        }

        // var_dump($row);

        $doc->addField(Field::UnIndexed('po_row_id', $row->getId()));
        $doc->addField(Field::UnIndexed('po_row_token', $row->getToken()));

        $doc->addField(Field::Keyword('row_token_keyword', $row->getToken() . "__" . $row->getId()));
        $doc->addField(Field::Keyword('row_identifer_keyword', $row->getRowIdentifer()));

        $doc->addField(Field::UnIndexed('row_quantity', $row->getDocQuantity()));
        $doc->addField(Field::UnIndexed('row_conversion_factor', $row->getConversionFactor()));

        $doc->addField(Field::UnIndexed('row_unit', $row->getUnit()));
        $doc->addField(Field::UnIndexed('row_unit_price', $row->getUnitPrice()));

        $doc->addField(Field::text('row_remarks', $row->getRemarks()));
        $doc->addField(Field::text('row_name', $row->getVendorItemCode()));

        $doc->addField(Field::UnIndexed('po_id', $row->getDocId()));
        $doc->addField(Field::UnIndexed('po_token', $row->getDocToken()));
        $doc->addField(Field::text('po_number', $row->getDocNumber()));
        $doc->addField(Field::Keyword('po_sys_number', $row->getDocSysNumber()));

        $doc->addField(Field::UnIndexed('vendor_id', $row->getVendorId()));
        $doc->addField(Field::UnIndexed('vendor_token', $row->getVendorToken()));

        $doc->addField(Field::UnIndexed('item_id', $row->getItem()));
        $doc->addField(Field::UnIndexed('item_token', $row->getItemToken()));
        $doc->addField(Field::Keyword('item_sku_key', $row->getItemSKU()));
        $doc->addField(Field::text('item_name', $row->getItemName()));

        $doc->addField(Field::Keyword('po_doc_status', $row->getDocStatus()));

        $index->addDocument($doc);
    }

    public function createIndex()
    {}

    public function updateDoc(DocSnapshot $doc)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Search\PoSearchIndexInterface::createDoc()
     */
    public function createDoc(DocSnapshot $doc)
    {
        if (! $doc instanceof POSnapshot) {
            throw new \InvalidArgumentException("PoSnapshot not given");
        }

        $rows = $doc->getDocRows();

        if ($rows == null) {
            throw new \InvalidArgumentException("PoSnapshot empty");
        }

        try {
            // take long time
            set_time_limit(1500);

            $index = Lucene::create(getcwd() . PoSearch::INDEX_PATH);
            Analyzer::setDefault(new CaseInsensitive());

            foreach ($rows as $row) {
                $this->_createDoc($index, $row);
            }

            $index->optimize();

            return 'Document has been added successfully !<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
