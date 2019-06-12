<?php
namespace Inventory\Application\Service\Search\ZendSearch;

use Application\Notification;
use Application\Service\AbstractService;
use Inventory\Application\DTO\Item\ItemSearchResultDTO;
use Inventory\Domain\Service\Search\ItemSearchInterface;
use Inventory\Infrastructure\Persistence\DoctrineItemReportingRepository;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\Boolean;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSearchService extends AbstractService implements ItemSearchInterface
{

    const ITEM_INDEX = "/data/inventory/indexes/item";

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Search\ItemSearchInterface::updateItemIndex()
     */
    public function updateItemIndex($itemId, $isNew = TRUE, $optimized = false)
    {
        $notification = new Notification();

        if ($itemId == null)
            return ($notification->addError("ItemId is empty. Nothing to index"));

        // start indexing
        // take long time
        set_time_limit(1500);
        try {

            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            Analyzer::setDefault(new CaseInsensitive());

            $m = "Document added";
            if ($isNew == FALSE) {

                $m = "Document updated";
                // update
                $ck_query = 'item_token_keyword:__' . $itemId;
                $ck_hits = $index->find($ck_query);

                if (count($ck_hits) > 0) {
                    foreach ($ck_hits as $hit) {
                        $index->delete($hit->id);
                    }
                }
            }

            $rep = new DoctrineItemReportingRepository();
            $rep->setDoctrineEM($this->getDoctrineEM());
            $records = $rep->getAllItemWithSerial($itemId);

            if (count($records) == 0) {
                $notification->setErrors(sprintf("[INFO] Nothing for indexing"));
                return $notification;
            }

            foreach ($records as $row) {
                $this->_addDocument($index, $row);
            }

            if ($optimized === true) {
                $index->optimize();
            }
            $notification->addSuccess(sprintf("[0k] %s! Total documents:%s.", $m, $index->numDocs()));
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        return $notification;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Search\ItemSearchInterface::search()
     */
    public function search($q, $isActive = 1)
    {
        $resultDTO = new ItemSearchResultDTO();

        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            Analyzer::setDefault(new CaseInsensitive());

            $isActive = 1;

            $final_query = new Boolean();

            $q = strtolower($q);

            $terms = explode(" ", $q);

            if (count($terms) > 1) {

                foreach ($terms as $t) {

                    $t = preg_replace('/\s+/', '', $t);

                    if (strlen($t) == 0) {
                        continue;
                    }

                    if (strpos($t, '*') != false) {
                        $pattern = new Term($t);
                        $query = new Wildcard($pattern);
                        $final_query->addSubquery($query, true);
                    } else {

                        $subquery = new MultiTerm();
                        $subquery->addTerm(new Term($t));
                        $final_query->addSubquery($subquery, true);
                    }
                }
            } else {

                if (strpos($q, '*') != false) {
                    $pattern = new Term($q);
                    $query = new Wildcard($pattern);
                    $final_query->addSubquery($query, true);
                } else {
                    $subquery = new MultiTerm();
                    $subquery->addTerm(new Term($q));
                    $final_query->addSubquery($subquery, true);
                }
            }

            $subquery1 = new MultiTerm();
            $subquery1->addTerm(new Term($isActive, 'is_active'));

            $final_query->addSubquery($subquery1, true);
            $hits = $index->find($final_query);

            $resultDTO->setHits($hits);
            $resultDTO->setMessage(sprintf("%s result(s) found for query: <b>%s</b>", count($hits), $q));
        } catch (\Exception $e) {

            $resultDTO->setMessage(sprintf("Query: <b>%s</b> sent, but exception catched: <b>%s</b>", $q, $e->getMessage()));
        }

        return $resultDTO;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Search\ItemSearchInterface::optimizeIndex()
     */
    public function optimizeIndex()
    {
        // take long time
        set_time_limit(1500);
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            $index->optimize();
            return sprintf("Index has been optimzed!<br> Index Size: %s, Number of documents: %s", $index->count(), $index->numDocs());
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function createDoc($doc, $isNew = true)
    {}

    public function searchFixedAsset($q)
    {}

    public function searchInventoryItem($q)
    {}

    public function searchServiceItem($q)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Search\ItemSearchInterface::createIndex()
     */
    public function createIndex()
    {
        $notification = new Notification();

        // take long time
        set_time_limit(2000);
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            Analyzer::setDefault(new CaseInsensitive());

            $rep = new DoctrineItemReportingRepository();
            $rep->setDoctrineEM($this->getDoctrineEM());
            $records = $rep->getAllItemWithSerial();

            if (count($records) == 0) {
                $notification->setErrors(sprintf("[INFO] Nothing for indexing"));
                return $notification;
            }

            foreach ($records as $row) {
                $this->_addDocument($index, $row);
            }

            $index->optimize();

            $notification->addSuccess(sprintf("[0k] %s! Total documents:%s.", $index->numDocs()));
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }
        return $notification;
    }

    /**
     *
     * @param \ZendSearch\Lucene\SearchIndexInterface $index
     * @param array $row
     */
    private function _addDocument(\ZendSearch\Lucene\SearchIndexInterface $index, $row)
    {
        if ($index == null || $row == null) {
            return;
        }

        $doc = new Document();

        // Keyword for check
        $doc->addField(Field::Keyword('item_token_keyword', "__" . $row['id']));

        $doc->addField(Field::UnIndexed('item_id', $row['id']));
        $doc->addField(Field::UnIndexed('token', $row['token']));
        $doc->addField(Field::UnIndexed('checksum', $row['checksum']));
        $doc->addField(Field::Keyword('item_token_serial_keyword', $row['token'] . "__" . $row['id'] . "__" . $row['serial_id']));
        $doc->addField(Field::Keyword('item_sku_key', $row['item_sku']));
        $doc->addField(Field::Keyword('item_sku1_key', $row['item_sku1']));
        $doc->addField(Field::Keyword('item_sku2_key', $row['item_sku2']));
        $doc->addField(Field::Keyword('item_sys_number', $row['sys_number']));
        $doc->addField(Field::Keyword('manufacturer_key', $row['manufacturer']));
        $doc->addField(Field::Keyword('manufacturer_model_key', $row['manufacturer_model']));
        $doc->addField(Field::Keyword('manufacturer_serial_key', $row['manufacturer_serial']));
        $doc->addField(Field::Keyword('manufacturer_code_key', $row['manufacturer_code']));
        $doc->addField(Field::Keyword('origin_key', $row['origin']));
        $doc->addField(Field::Keyword('is_fixed_asset', $row['is_fixed_asset']));
        $doc->addField(Field::Keyword('is_sparepart', $row['is_sparepart']));
        $doc->addField(Field::Keyword('is_active', $row['is_active']));
        $doc->addField(Field::Keyword('is_stocked', $row['is_stocked']));
        $doc->addField(Field::Keyword('uom', $row['uom']));
        $doc->addField(Field::Keyword('sp_label_key', $row['sparepart_label']));
        $doc->addField(Field::Keyword('asset_label_key', $row['asset_label']));

        // echo $row->getId () ."::".$row->getItemName () . '::: ' . mb_detect_encoding($row->getItemName ()) . '<br>'; // false

        $doc->addField(Field::text('item_sku', $row['item_sku'], 'UTF-8'));
        $doc->addField(Field::text('item_sku1', $row['item_sku1'], 'UTF-8'));
        $doc->addField(Field::text('item_sku2', $row['item_sku2'], 'UTF-8'));

        $doc->addField(Field::text('item_name', $row['item_name']));
        // $doc->addField ( Field::text ( 'item_name1', $row->getItemNameForeign () ) );
        $doc->addField(Field::text('item_description', $row['item_description']));
        $doc->addField(Field::text('keywords', $row['keywords']));

        $doc->addField(Field::text('manufacturer', $row['manufacturer']));
        $doc->addField(Field::text('manufacturer_model', $row['manufacturer_model']));
        $doc->addField(Field::text('manufacturer_serial', $row['manufacturer_serial']));
        $doc->addField(Field::text('manufacturer_code', $row['manufacturer_code']));
        $doc->addField(Field::text('origin', $row['origin']));
        $doc->addField(Field::text('remarks', $row['remarks_text']));

        $doc->addField(Field::text('sp_label', $row['sparepart_label']));
        $doc->addField(Field::text('asset_label', $row['asset_label']));

        $doc->addField(Field::UnIndexed('serial_id', $row['serial_id']));

        $doc->addField(Field::Keyword('serial_number_key', $row['serial_no']));
        $doc->addField(Field::Keyword('serial_number_1_key', $row['serial_no1']));
        $doc->addField(Field::Keyword('serial_number_2_key', $row['serial_no2']));

        $doc->addField(Field::text('serial_number', $row['serial_no']));
        $doc->addField(Field::text('serial_number_1', $row['serial_no1']));
        $doc->addField(Field::text('serial_number_2', $row['serial_no2']));

        $doc->addField(Field::text('mfg_name', $row['mfg_name']));
        $doc->addField(Field::text('mfg_description', $row['mfg_description']));
        $doc->addField(Field::text('serial_remarks', $row['serial_remarks']));

        $doc->addField(Field::Keyword('mfg_serial_number_key', $row['mfg_serial_number']));
        $doc->addField(Field::text('mfg_serial_number', $row['mfg_serial_number']));

        $doc->addField(Field::Keyword('mfg_model_key', $row['mfg_model']));
        $doc->addField(Field::Keyword('mfg_model1_key', $row['mfg_model1']));
        $doc->addField(Field::Keyword('mfg_model2_key', $row['mfg_model2']));

        $doc->addField(Field::text('mfg_model', $row['mfg_model']));
        $doc->addField(Field::text('mfg_model1', $row['mfg_model1']));
        $doc->addField(Field::text('mfg_model2', $row['mfg_model2']));

        $s = $row['serial_no'];
        $l = strlen($s);
        $l > 3 ? $p = strpos($s, "-", 3) + 1 : $p = 0;

        $doc->addField(Field::Text('serial_number_lastnumber', substr($s, $p, $l - $p) * 1));

        $s = $row['asset_label'];
        $l = strlen($s);
        $l > 3 ? $p = strpos($s, "-", 3) + 1 : $p = 0;

        $doc->addField(Field::Text('asset_label_lastnumber', substr($s, $p, $l - $p) * 1));

        $index->addDocument($doc);

        // =================================
    }
}
