<?php
namespace Inventory\Service;

use Doctrine\ORM\EntityManager;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
// use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num\CaseInsensitive; // Not worked
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;
use Application\Entity\NmtInventoryItem;
use ZendSearch\Lucene\Search\Query\Boolean;
use ZendSearch\Lucene\Search\QueryParser;
use Exception;

/**
 *
 * @author nmt
 *        
 */
class ItemSearchService
{

    const ITEM_INDEX = "/data/inventory/indexes/item";

    protected $doctrineEM;

    /**
     *
     * @return string
     */
    public function createItemIndex()
    {
        
        // take long time
        set_time_limit(1500);
        try {
            $index = Lucene::create(getcwd() . self::ITEM_INDEX);
            
            Analyzer::setDefault(new CaseInsensitive());
            
            $row = new NmtInventoryItem();
            
            $records = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findAll();
            
            if (count($records) > 0) {
                
                $log = array();
                foreach ($records as $r) {
                    
                    try {
                        $doc = new Document();
                        $row = $r;
                        $doc->addField(Field::UnIndexed('item_id', $row->getId()));
                        $doc->addField(Field::UnIndexed('token', $row->getToken()));
                        $doc->addField(Field::UnIndexed('checksum', $row->getChecksum()));
                        $doc->addField(Field::Keyword('item_token_keyword', $row->getToken() . "__" . $row->getId()));
                        
                        $doc->addField(Field::Keyword('item_sku_key', $row->getItemSku()));
                        $doc->addField(Field::Keyword('item_sys_number', $row->getSysNumber()));
                        
                        $doc->addField(Field::Keyword('manufacturer_key', $row->getManufacturer()));
                        $doc->addField(Field::Keyword('manufacturer_model_key', $row->getManufacturerModel()));
                        $doc->addField(Field::Keyword('manufacturer_serial_key', $row->getManufacturerSerial()));
                        $doc->addField(Field::Keyword('manufacturer_code_key', $row->getManufacturerCode()));
                        $doc->addField(Field::Keyword('origin_key', $row->getOrigin()));
                        
                        $doc->addField(Field::Keyword('is_fixed_asset', $row->getIsFixedAsset()));
                        $doc->addField(Field::Keyword('is_sparepart', $row->getIsSparepart()));
                        $doc->addField(Field::Keyword('is_active', $row->getIsActive()));
                        $doc->addField(Field::Keyword('is_stocked', $row->getIsStocked()));
                        
                        $doc->addField(Field::Keyword('uom', $row->getUom()));
                        $doc->addField(Field::Keyword('sp_label_key', $row->getSparepartLabel()));
                        $doc->addField(Field::Keyword('asset_label_key', $row->getAssetLabel()));
                        
                        // echo $row->getId () ."::".$row->getItemName () . '::: ' . mb_detect_encoding($row->getItemName ()) . '<br>'; // false
                        
                        $doc->addField(Field::text('item_sku', $row->getItemSku()), 'UTF-8');
                        $doc->addField(Field::text('item_name', $row->getItemName()));
                        // $doc->addField ( Field::text ( 'item_name1', $row->getItemNameForeign () ) );
                        $doc->addField(Field::text('item_description', $row->getItemDescription()));
                        $doc->addField(Field::text('keywords', $row->getKeywords()));
                        
                        $doc->addField(Field::text('manufacturer', $row->getManufacturer()));
                        $doc->addField(Field::text('manufacturer_model', $row->getManufacturerModel()));
                        $doc->addField(Field::text('manufacturer_serial', $row->getManufacturerSerial()));
                        $doc->addField(Field::text('manufacturer_code', $row->getManufacturerCode()));
                        $doc->addField(Field::text('origin', $row->getOrigin()));
                        $doc->addField(Field::text('remarks', $row->getRemarks()));
                        
                        $doc->addField(Field::text('sp_label', $row->getSparepartLabel()));
                        $doc->addField(Field::text('asset_label', $row->getAssetLabel()));
                        
                        $s = $row->getAssetLabel();
                        $l = strlen($s);
                        $l > 3 ? $p = strpos($s, "-", 3) + 1 : $p = 0;
                        
                        $doc->addField(Field::Text('asset_label_lastnumber', substr($s, $p, $l - $p) * 1));
                        
                        $index->addDocument($doc);
                        $log[] = "Doc added";
                    } catch (Exception $e) {
                        $log[] = $e->getMessage();
                    }
                }
                $index->optimize();
                $log[] = 'Item resource indexes is created successfully!<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs();
                return $log;
            } else {
                $log[] = 'Nothing for indexing!';
                return $log;
            }
        } catch (Exception $e) {
            $log[] = 'Nothing for indexing!';
            return $log;
        }
    }

    /**
     *
     * @param number $is_new
     * @param unknown $row
     * @param unknown $optimized
     * @return string|unknown
     */
    public function updateIndex($is_new = 0, $row, $optimized)
    {
        
        // take long time
        set_time_limit(1500);
        try {
            
            /** @var \Application\Entity\Application\Entity\NmtInventoryItem $row;*/
            if ($row instanceof NmtInventoryItem) {
                $index = Lucene::open(getcwd() . self::ITEM_INDEX);
                Analyzer::setDefault(new CaseInsensitive());
                
                if ($is_new !== 1) {
                    $ck_query = 'item_token_keyword:' . $row->getToken() . '__' . $row->getId();
                    $ck_hits = $index->find($ck_query);
                    
                    if (count($ck_hits) == 1) {
                        $ck_hit = $ck_hits[0];
                        $index->delete($ck_hit->id);
                    }
                }
                
                $doc = new Document();
                $doc->addField(Field::UnIndexed('item_id', $row->getId()));
                $doc->addField(Field::UnIndexed('token', $row->getToken()));
                $doc->addField(Field::UnIndexed('checksum', $row->getChecksum()));
                $doc->addField(Field::Keyword('item_token_keyword', $row->getToken() . "__" . $row->getId()));
                
                $doc->addField(Field::Keyword('item_sku_key', $row->getItemSku()));
                $doc->addField(Field::Keyword('item_sys_number', $row->getSysNumber()));
                
                $doc->addField(Field::Keyword('manufacturer_key', $row->getManufacturer()));
                $doc->addField(Field::Keyword('manufacturer_model_key', $row->getManufacturerModel()));
                $doc->addField(Field::Keyword('manufacturer_serial_key', $row->getManufacturerSerial()));
                $doc->addField(Field::Keyword('manufacturer_code_key', $row->getManufacturerCode()));
                $doc->addField(Field::Keyword('origin_key', $row->getOrigin()));
                
                $doc->addField(Field::Keyword('is_fixed_asset', $row->getIsFixedAsset()));
                $doc->addField(Field::Keyword('is_sparepart', $row->getIsSparepart()));
                $doc->addField(Field::Keyword('is_active', $row->getIsActive()));
                $doc->addField(Field::Keyword('is_stocked', $row->getIsStocked()));
                
                $doc->addField(Field::Keyword('uom', $row->getUom()));
                $doc->addField(Field::Keyword('sp_label_key', $row->getSparepartLabel()));
                $doc->addField(Field::Keyword('asset_label_key', $row->getAssetLabel()));
                
                // echo $row->getId () ."::".$row->getItemName () . '::: ' . mb_detect_encoding($row->getItemName ()) . '<br>'; // false
                
                $doc->addField(Field::text('item_sku', $row->getItemSku()), 'UTF-8');
                $doc->addField(Field::text('item_name', $row->getItemName()));
                // $doc->addField ( Field::text ( 'item_name1', $row->getItemNameForeign () ) );
                $doc->addField(Field::text('item_description', $row->getItemDescription()));
                $doc->addField(Field::text('keywords', $row->getKeywords()));
                
                $doc->addField(Field::text('manufacturer', $row->getManufacturer()));
                $doc->addField(Field::text('manufacturer_model', $row->getManufacturerModel()));
                $doc->addField(Field::text('manufacturer_serial', $row->getManufacturerSerial()));
                $doc->addField(Field::text('manufacturer_code', $row->getManufacturerCode()));
                $doc->addField(Field::text('origin', $row->getOrigin()));
                $doc->addField(Field::text('remarks', $row->getRemarks()));
                
                $doc->addField(Field::text('sp_label', $row->getSparepartLabel()));
                $doc->addField(Field::text('asset_label', $row->getAssetLabel()));
                
                $s = $row->getAssetLabel();
                $l = strlen($s);
                $l > 3 ? $p = strpos($s, "-", 3) + 1 : $p = 0;
                
                $doc->addField(Field::Text('asset_label_lastnumber', substr($s, $p, $l - $p) * 1));
                
                $index->addDocument($doc);
                
                if (optimized === true) {
                    $index->optimize();
                }
                return $row->getItemName() . " has been updated /added successfully!";
            } else {
                return 'Input is invalid';
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *
     * @param unknown $item
     * @return string|unknown
     */
    public function addDocument($item, $optimized)
    {
        
        // take long time
        set_time_limit(1500);
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            Analyzer::setDefault(new CaseInsensitive());
            
            $row = new NmtInventoryItem();
            
            if ($item instanceof NmtInventoryItem) {
                $doc = new Document();
                
                $row = $item;
                $doc->addField(Field::UnIndexed('item_id', $row->getId()));
                $doc->addField(Field::UnIndexed('token', $row->getToken()));
                $doc->addField(Field::UnIndexed('checksum', $row->getChecksum()));
                
                $doc->addField(Field::Keyword('item_sku_key', $row->getItemSku()));
                $doc->addField(Field::Keyword('item_sys_number', $row->getSysNumber()));
                
                $doc->addField(Field::Keyword('manufacturer_key', $row->getManufacturer()));
                $doc->addField(Field::Keyword('manufacturer_model_key', $row->getManufacturerModel()));
                $doc->addField(Field::Keyword('manufacturer_serial_key', $row->getManufacturerSerial()));
                $doc->addField(Field::Keyword('manufacturer_code_key', $row->getManufacturerCode()));
                $doc->addField(Field::Keyword('origin_key', $row->getOrigin()));
                
                $doc->addField(Field::Keyword('is_fixed_asset', $row->getIsFixedAsset()));
                $doc->addField(Field::Keyword('is_sparepart', $row->getIsSparepart()));
                $doc->addField(Field::Keyword('is_active', $row->getIsActive()));
                $doc->addField(Field::Keyword('is_stocked', $row->getIsStocked()));
                
                $doc->addField(Field::Keyword('uom', $row->getUom()));
                $doc->addField(Field::Keyword('sp_label_key', $row->getSparepartLabel()));
                $doc->addField(Field::Keyword('asset_label_key', $row->getAssetLabel()));
                
                $doc->addField(Field::text('item_sku', $row->getItemSku()), 'UTF-8');
                $doc->addField(Field::text('item_name', $row->getItemName()));
                // $doc->addField ( Field::text ( 'item_name1', $row->getItemNameForeign () ) );
                $doc->addField(Field::text('item_description', $row->getItemDescription()));
                $doc->addField(Field::text('keywords', $row->getKeywords()));
                
                $doc->addField(Field::text('manufacturer', $row->getManufacturer()));
                $doc->addField(Field::text('manufacturer_model', $row->getManufacturerModel()));
                $doc->addField(Field::text('manufacturer_serial', $row->getManufacturerSerial()));
                $doc->addField(Field::text('manufacturer_code', $row->getManufacturerCode()));
                $doc->addField(Field::text('origin', $row->getOrigin()));
                $doc->addField(Field::text('remarks', $row->getRemarks()));
                
                $doc->addField(Field::text('sp_label', $row->getSparepartLabel()));
                $doc->addField(Field::text('asset_label', $row->getAssetLabel()));
                
                $s = $row->getAssetLabel();
                $l = strlen($s);
                $l > 3 ? $p = strpos($s, "-", 3) + 1 : $p = 0;
                
                $doc->addField(Field::Text('asset_label_lastnumber', substr($s, $p, $l - $p) * 1));
                
                $index->addDocument($doc);
                
                if (optimized === true) {
                    $index->optimize();
                }
                
                return 'Document has been added successfully !<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs();
            } else {
                return 'Input is invalid';
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *
     * @return string|unknown
     */
    public function optimizeIndex()
    {
        
        // take long time
        set_time_limit(1500);
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            $index->optimize();
            return 'Index has been optimzed!<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

   /**
    * 
    * @param unknown $q
    * @param number $isActive
    * @return string[]|array[]|\ZendSearch\Lucene\Search\QueryHit[]|string[]|NULL[]
    */
    public function searchAll($q, $isActive = 1)
    {
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            
            $final_query = new Boolean();
            
            $q = strtolower($q);
            
            $terms = explode(" ", $q);
             
            if (count($terms) > 1) {
                
                foreach ($terms as $t) {
                    
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
            
            // var_dump ( $final_query );
            $hits = $index->find($final_query);
            
            $result = [
                "message" => count($hits) . " result(s) found for query: <b>" . $q . "</b>",
                "hits" => $hits
            ];
            
            return $result;
        } catch (\Exception $e) {
            $result = [
                "message" => 'Query: <b>' . $q . '</b> sent , but exception catched: <b>' . $e->getMessage() . "</b>\n",
                "hits" => null
            ];
            return $result;
        }
    }

    /**
     *
     * @param unknown $q
     * @param unknown $department_id
     */
    public function searchAllItem($q)
    {
        try {
            
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            
            if (strpos($q, '*') != false) {
                $pattern = new Term($q);
                $query = new Wildcard($pattern);
                $hits = $index->find($query);
            } else {
                // $query = QueryParser::parse ( $q );
                $hits = $index->find($q);
            }
            
            /*
             * echo count ( $hits ) . " result(s) found for query: <b>" . $q . "</b>";
             *
             * $n=0;
             * foreach($hits as $h){
             * $n++;
             * echo $n. ": " . $h->getDocument()->getFieldUtf8Value("item_id") . "<br>";
             * $fields = $h->getDocument()->getFieldNames();
             *
             * foreach ($fields as $f){
             * echo $n. "--" . $f . "<br>";
             * }
             *
             *
             * }
             */
            
            $result = [
                "message" => count($hits) . " result(s) found for query: <b>" . $q . "</b>",
                "hits" => $hits
            ];
            
            return $result;
        } catch (\Exception $e) {
            $result = [
                "message" => 'Query: <b>' . $q . '</b> sent , but exception catched: <b>' . $e->getMessage() . "</b>\n",
                "hits" => null
            ];
            return $result;
        }
    }

    /**
     *
     * @param unknown $q
     * @param unknown $department_id
     */
    public function searchAssetItem($q)
    {
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            
            $final_query = new Boolean();
            
            if (strpos($q, '*') != false) {
                $pattern = new Term($q);
                $query = new Wildcard($pattern);
                $final_query->addSubquery($query, true);
            } else {
                $subquery = new MultiTerm();
                $subquery->addTerm(new Term($q));
                $final_query->addSubquery($subquery, true);
            }
            
            $subquery1 = new MultiTerm();
            $subquery1->addTerm(new Term('1', 'is_fixed_asset'));
            
            $final_query->addSubquery($subquery1, true);
            
            // var_dump ( $final_query );
            $hits = $index->find($final_query);
            
            $result = [
                "message" => count($hits) . " result(s) found for query: <b>" . $q . "</b>",
                "hits" => $hits
            ];
            
            return $result;
        } catch (\Exception $e) {
            $result = [
                "message" => 'Query: <b>' . $q . '</b> sent , but exception catched: <b>' . $e->getMessage() . "</b>\n",
                "hits" => null
            ];
            return $result;
        }
    }

    /**
     *
     * @param unknown $q
     * @return string[]|array[]|\ZendSearch\Lucene\Search\QueryHit[]|string[]|NULL[]
     */
    public function searchSPItem($q)
    {
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            
            $final_query = new Boolean();
            
            if (strpos($q, '*') != false) {
                $pattern = new Term($q);
                $query = new Wildcard($pattern);
                $final_query->addSubquery($query, true);
            } else {
                $subquery = new MultiTerm();
                $subquery->addTerm(new Term($q));
                $final_query->addSubquery($subquery, true);
            }
            
            $subquery1 = new MultiTerm();
            $subquery1->addTerm(new Term('1', 'is_sparepart'));
            
            $final_query->addSubquery($subquery1, true);
            
            // var_dump ( $final_query );
            $hits = $index->find($final_query);
            
            $result = [
                "message" => count($hits) . " result(s) found for query: <b>" . $q . "</b>",
                "hits" => $hits
            ];
            
            return $result;
        } catch (\Exception $e) {
            $result = [
                "message" => 'Query: <b>' . $q . '</b> sent , but exception catched: <b>' . $e->getMessage() . "</b>\n",
                "hits" => null
            ];
            return $result;
        }
    }

    // Analyzer::setDefault ( new CaseInsensitive () );
    // $analyzer = Analyzer::getDefault ( new CaseInsensitive () );
    
    /*
     * foreach ($hits as $h){
     *
     * echo $query->htmlFragmentHighlightMatches($h->item_sku);
     * $token = $analyzer->tokenize($h->item_sku);
     * echo "TOKEN" . count($token);
     * foreach ($token as $t){
     * var_dump("T::" . $t->getTermText());
     * }
     * };
     */
    
    /*
     * foreach ($hits as $h){
     * var_dump($h->getDocument());
     * break;
     * }
     */
    
    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \Inventory\Service\ItemSearchService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}
