<?php

namespace BP\Service;

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
use Application\Entity\NmtBpVendor;

/**
 *
 * @author nmt
 *        
 */
class VendorSearchService {
	const ITEM_INDEX = "/data/bp/indexes/vendor";
	protected $doctrineEM;
	
	/**
	 *
	 * @return string
	 */
	public function createVendorIndex() {
		
		// take long time
		set_time_limit ( 1500 );
		
		$result = array ();
		$sucess = 0;
		$fail = 0;
		
		try {
			$index = Lucene::create ( getcwd () . self::ITEM_INDEX );
			
			Analyzer::setDefault ( new CaseInsensitive () );
			
			$row = new NmtBpVendor ();
			
			$records = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBPVendor' )->findAll ();
			
			if (count ( $records ) > 0) {
				
				foreach ( $records as $r ) {
					
					try {
						$doc = new Document ();
						$row = $r;
						$doc->addField ( Field::UnIndexed ( 'vendor_id', $row->getId () ) );
						$doc->addField ( Field::UnIndexed ( 'token', $row->getToken () ) );
						$doc->addField ( Field::UnIndexed ( 'checksum', $row->getChecksum () ) );
						
						$doc->addField ( Field::text ( 'vendor_name', $row->getVendorName () ) );
						$doc->addField ( Field::text ( 'vendor_name_short', $row->getVendorShortName () ) );
						$doc->addField ( Field::text ( 'keywords', $row->getKeywords () ) );
						$doc->addField ( Field::text ( 'remarks', $row->getRemarks () ) );
						$doc->addField ( Field::text ( 'country_name', $row->getCountry ()->getCountryName () ) );
						$doc->addField ( Field::Keyword ( 'is_active', $row->getIsActive () ) );
						
						$index->addDocument ( $doc );
						// $log [] = $row->getVendorName () . " added!";
						$sucess ++;
					} catch ( Exception $e ) {
						// $log [] = $e->getMessage ();
						$fail ++;
					}
				}
				$index->optimize ();
				
				// $log [] = 'Vendor indexes is created successfully!<br> Index Size:' . $index->count () . '<br>Documents: ' . $index->numDocs ();
				
				$result = array (
						'message' => 'Vendor indexes is created successfully!<br> Index Size:' . $index->count () . '<br>Documents: ' . $index->numDocs (),
						'sucess' => $sucess,
						'fail' => $fail 
				);
				return $result;
			} else {
				
				$result = array (
						'message' => 'Nothing for indexing!',
						'sucess' => $sucess,
						'fail' => $fail 
				);
				return $result;
			}
		} catch ( Exception $e ) {
			
			$result = array (
					'message' => $e->getMessage (),
					'sucess' => $sucess,
					'fail' => $fail 
			);
			return $result;
		}
	}
	
	/**
	 *
	 * @param unknown $item        	
	 * @return string|unknown
	 */
	public function addDocument($item, $optimized) {
		
		// take long time
		set_time_limit ( 1500 );
		try {
			$index = Lucene::open ( getcwd () . self::ITEM_INDEX );
			Analyzer::setDefault ( new CaseInsensitive () );
			
			$row = new NmtInventoryItem ();
			
			if ($item instanceof NmtInventoryItem) {
				$doc = new Document ();
				
				$row = $item;
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
				
				$doc->addField ( Field::text ( 'item_sku', $row->getItemSku () ), 'UTF-8' );
				$doc->addField ( Field::text ( 'item_name', $row->getItemName () ) );
				// $doc->addField ( Field::text ( 'item_name1', $row->getItemNameForeign () ) );
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
				
				if (optimized === true) {
					$index->optimize ();
				}
				
				return 'Document has been added successfully !<br> Index Size:' . $index->count () . '<br>Documents: ' . $index->numDocs ();
			} else {
				return 'Input is invalid';
			}
		} catch ( Exception $e ) {
			return $e->getMessage ();
		}
	}
	
	/**
	 *
	 * @return string|unknown
	 */
	public function optimizeIndex() {
		
		// take long time
		set_time_limit ( 1500 );
		try {
			$index = Lucene::open ( getcwd () . self::ITEM_INDEX );
			$index->optimize ();
			return 'Index has been optimzed!<br> Index Size:' . $index->count () . '<br>Documents: ' . $index->numDocs ();
		} catch ( Exception $e ) {
			return $e->getMessage ();
		}
	}
	
	/**
	 *
	 * @param unknown $q        	
	 * @param unknown $department_id        	
	 */
	public function search($q) {
		try {
			
			$index = Lucene::open ( getcwd () . self::ITEM_INDEX );
			
			if (strpos ( $q, '*' ) != false) {
				$pattern = new Term ( $q );
				$query = new Wildcard ( $pattern );
				$hits = $index->find ( $query );
			} else {
				// $query = QueryParser::parse ( $q );
				$hits = $index->find ( $q );
			}
			
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
