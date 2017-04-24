<?php

namespace Inventory\Service;

use Doctrine\ORM\EntityManager;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;
use Application\Entity\NmtInventoryItem;
use ZendSearch\Lucene\Search\Query\Boolean;
use ZendSearch\Lucene\Search\QueryParser;

/**
 *
 * @author nmt
 *        
 */
class ItemSearchService {
	const ITEM_INDEX = "/data/inventory/indexes/item";
	protected $doctrineEM;
	
	/**
	 *
	 * @return string
	 */
	public function createItemIndex() {
		
		// take long time
		set_time_limit ( 150 );
		try {
			$index = Lucene::create ( getcwd () . self::ITEM_INDEX );
			
			Analyzer::setDefault ( new CaseInsensitive () );
			
			$row = new NmtInventoryItem ();
			
			$records = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findAll ();
			
			if (count ( $records ) > 0) {
				foreach ( $records as $r ) {
					$doc = new Document ();
					$row = $r;
					$doc->addField ( Field::UnIndexed ( 'item_id', $row->getId () ) );
					
					$doc->addField ( Field::Keyword ( 'item_sku_key', $row->getItemSku () ) );
					
					$doc->addField ( Field::Keyword ( 'manufacturer_key', $row->getManufacturer () ) );
					$doc->addField ( Field::Keyword ( 'manufacturer_model_key', $row->getManufacturerModel () ) );
					$doc->addField ( Field::Keyword ( 'manufacturer_serial_key', $row->getManufacturerSerial () ) );
					$doc->addField ( Field::Keyword ( 'manufacturer_code_key', $row->getManufacturerCode () ) );
					$doc->addField ( Field::Keyword ( 'origin_key', $row->getOrigin () ) );
					
					$doc->addField ( Field::Keyword ( 'is_fixed_asset', $row->getIsFixedAsset () ) );
					$doc->addField ( Field::Keyword ( 'is_sparepart', $row->getIsSparepart () ) );
					$doc->addField ( Field::Keyword ( 'is_active', $row->getIsActive () ) );
					$doc->addField ( Field::Keyword ( 'is_stocked', $row->getIsStocked () ) );
					
					$doc->addField ( Field::Keyword ( 'uom', $row->getUom () ) );
					$doc->addField ( Field::Keyword ( 'sp_label_key', $row->getSparepartLabel () ) );
					$doc->addField ( Field::Keyword ( 'asset_label_key', $row->getAssetLabel () ) );
					
					$doc->addField ( Field::text ( 'item_sku', $row->getItemSku () ) );
					$doc->addField ( Field::text ( 'item_name', $row->getItemName () ) );
					$doc->addField ( Field::text ( 'item_name1', $row->getItemNameForeign () ) );
					$doc->addField ( Field::text ( 'item_description', $row->getItemDescription () ) );
					
					$doc->addField ( Field::text ( 'manufacturer', $row->getManufacturer () ) );
					$doc->addField ( Field::text ( 'manufacturer_model', $row->getManufacturerModel () ) );
					$doc->addField ( Field::text ( 'manufacturer_serial', $row->getManufacturerSerial () ) );
					$doc->addField ( Field::text ( 'manufacturer_code', $row->getManufacturerCode () ) );
					$doc->addField ( Field::text ( 'origin', $row->getOrigin () ) );
					$doc->addField ( Field::text ( 'remarks', $row->getRemarks () ) );
					
					$doc->addField ( Field::text ( 'sp_label', $row->getSparepartLabel () ) );
					$doc->addField ( Field::text ( 'asset_label', $row->getAssetLabel () ) );
					
					$s = $row->getAssetLabel ();
					$l = strlen ( $s );
					$l > 3 ? $p = strpos ( $s, "-", 3 ) + 1 : $p = 0;
					
					$doc->addField ( Field::Text ( 'asset_label_lastnumber', substr ( $s, $p, $l - $p ) * 1 ) );
					
					$index->addDocument ( $doc );
				}
				return 'Item resource indexes is created successfully!';
			} else {
				return 'Nothing for indexing!';
			}
		} catch ( Exception $e ) {
			return $e->getMessage ();
		}
	}
	
	/**
	 *
	 * @param unknown $q        	
	 * @param unknown $department_id        	
	 */
	public function searchAllItem($q) {
		$index = Lucene::open ( getcwd () . self::ITEM_INDEX );
		
		try {
			if (strpos ( $q, '*' ) != false) {
				$pattern = new Term ( $q );
				$query = new Wildcard ( $pattern );
				$hits = $index->find ( $query );
			} else {
				$query = QueryParser::parse($q);
				$hits = $index->find ( $query );
			}
			
			
			
			/* foreach ($hits as $h){
				echo $query->highlightMatches($h->item_name);
			} */
			
			$result = [ 
					"message" => count ( $hits ) . " result(s) found for query: <b>" . $q . "</b>",
					"query"=>$query,
					"hits" => $hits,
			];
			
			return $result;
		} catch ( \Exception $e ) {
			$result = [ 
					"message" => 'Query: <b>' . $q . '</b> sent , but exception catched: <b>' . $e->getMessage () . "</b>\n",
					"query"=>$query,
					"hits" => null,
			];
			return $result;
		}
	}
	
	/**
	 *
	 * @param unknown $q        	
	 * @param unknown $department_id        	
	 */
	public function searchAssetItem($q) {
		try {
			$index = Lucene::open ( getcwd () . self::ITEM_INDEX );
			
			$final_query = new Boolean ();
			
			if (strpos ( $q, '*' ) != false) {
				$pattern = new Term ( $q );
				$query = new Wildcard ( $pattern );
				$final_query->addSubquery ( $query, true );
			} else {
				$subquery = new MultiTerm ();
				$subquery->addTerm ( new Term ( $q ) );
				$final_query->addSubquery ( $subquery, true );
			}
			
			$subquery1 = new MultiTerm ();
			$subquery1->addTerm ( new Term ( '1', 'is_fixed_asset' ) );
			
			$final_query->addSubquery ( $subquery1, true );
			
			//var_dump ( $final_query );
			$hits = $index->find ( $final_query );
			
			
			$result = [
					"message" => count ( $hits ) . " result(s) found for query: <b>" . $q . "</b>",
					"hits" => $hits
			];
			
			return $result;
			
			
		} catch ( \Exception $e ) {
			$result = [ 
					"message" => 'Query: <b>' . $q . '</b> sent , but exception catched: <b>' . $e->getMessage () . "</b>\n",
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
	public function searchSPItem($q) {
		try {
			$index = Lucene::open ( getcwd () . self::ITEM_INDEX );
			
			$final_query = new Boolean ();
			
			if (strpos ( $q, '*' ) != false) {
				$pattern = new Term ( $q );
				$query = new Wildcard ( $pattern );
				$final_query->addSubquery ( $query, true );
			} else {
				$subquery = new MultiTerm ();
				$subquery->addTerm ( new Term ( $q ) );
				$final_query->addSubquery ( $subquery, true );
			}
			
			$subquery1 = new MultiTerm ();
			$subquery1->addTerm ( new Term ( '1', 'is_sparepart' ) );
			
			$final_query->addSubquery ( $subquery1, true );
			
			//var_dump ( $final_query );
			$hits = $index->find ( $final_query );
			
			
			$result = [
					"message" => count ( $hits ) . " result(s) found for query: <b>" . $q . "</b>",
					"hits" => $hits
			];
			
			return $result;
			
			
		} catch ( \Exception $e ) {
			$result = [
					"message" => 'Query: <b>' . $q . '</b> sent , but exception catched: <b>' . $e->getMessage () . "</b>\n",
					"hits" => null
			];
			return $result;
		}
	}
	
	/**
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	
	/**
	 *
	 * @param EntityManager $doctrineEM        	
	 * @return \Inventory\Service\ItemSearchService
	 */
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
}
