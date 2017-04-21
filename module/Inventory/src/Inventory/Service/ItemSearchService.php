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
					$doc->addField ( Field::Keyword ( 'item_name_key', $row->getItemName () ) );
					$doc->addField ( Field::Keyword ( 'item_name1_key', $row->getItemNameForeign () ) );
					$doc->addField ( Field::Keyword ( 'manufacturer_model_key', $row->getManufacturerModel () ) );
					$doc->addField ( Field::Keyword ( 'manufacturer_serial_key', $row->getManufacturerSerial () ) );
					$doc->addField ( Field::Keyword ( 'manufacturer_code_key', $row->getManufacturerCode () ) );
					
					$doc->addField ( Field::text ( 'item_sku', $row->getItemSku () ) );
					$doc->addField ( Field::text ( 'item_name', $row->getItemName () ) );
					$doc->addField ( Field::text ( 'item_name1', $row->getItemNameForeign () ) );
					$doc->addField ( Field::text ( 'manufacturer_model', $row->getManufacturerModel () ) );
					$doc->addField ( Field::text ( 'manufacturer_serial', $row->getManufacturerSerial () ) );
					$doc->addField ( Field::text ( 'manufacturer_code', $row->getManufacturerCode () ) );
					
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
		$index = Lucene::open ( getcwd () . self::ITEM_INDEX);
		
		
		try {
			if (strpos ( $q, '*' ) != false) {
				$pattern = new Term ( $q );
				$query = new Wildcard ( $pattern );
				$hits = $index->find ( $query );
			} else {
				$hits = $index->find ( $q );
			}
			
			$hits = $index->find ( $q );
			
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
