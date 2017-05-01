<?php

namespace AssetTypeTest\Model;

use PHPUnit_Framework_TestCase;
use Zend\Db\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\EventManager\EventManager;
use Zend\EventManager\Event;
use Inventory\Model\AssetCategory;
use Inventory\Model\AssetCategoryTable;
use Inventory\Model\AssetGroup;
use Inventory\Model\AssetGroupTable;
use AssetTypeTest\Bootstrap;
use Inventory\Services\AssetService;


use Inventory\Model\SparepartCategory;
use Inventory\Model\SparepartCategoryMember;
use Inventory\Model\ArticleLastDN;
use Inventory\Model\ArticleLastDNTable;

use Inventory\Model\SparepartLastDN;
use Inventory\Model\SparepartLastDNTable;

use Inventory\Services\EpartnerService;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Header\ContentType;
use Zend\Mail\Message;
class AssetTypeTest extends PHPUnit_Framework_TestCase {
	/*
	public function testArticleCatergoryTest() {
		$list = Bootstrap::getServiceManager()->get('User\Service\ArticleCategory');
		//$list = $this->articleCategoryService;
		$list = $list->initCategory ();
		$list = $list->updateCategory ( 42, 0 );
		//$list = $list->generateJSTreeWithTotalMember ( 42 );
		var_dump($list->getCategories ()['42']);
		
	}
	
	/*
	 * public function testAssetTypeTable() {
	 *
	 *
	 * $resultSet = new ResultSet();
	 * $AssetTypeTable = Bootstrap::getServiceManager()->get('Inventory\Model\AssetTypeTable');
	 * $assetType = new AssetType;
	 * $assetType->type = "Machinery";
	 * $assetType->description = "Machinery";
	 *
	 * $AssetTypeTable->add($assetType);
	 * var_dump($AssetTypeTable->fetchAll());
	 *
	 * $as = Bootstrap::getServiceManager()->get('Inventory\Services\AssetService');
	 * $as->createAssetFolderById ( 7 );
	 * }
	 */
	
	/*
	 * public function testAssetCatergoryTest() {
	 * $resultSet = new ResultSet ();
	 * $AssetCategoryTable = Bootstrap::getServiceManager()->get('Inventory\Model\AssetCategoryTable');
	 *
	 * $assetType = new AssetCategory();
	 * $assetType->category = "Buiding ";
	 * $assetType->description = "Machinery details";
	 *
	 * //$AssetCategoryTable->add($assetType);
	 * echo $AssetCategoryTable->add($assetType);
	 *
	 * }
	 */
	
	/*
	public function testAssetServiceTest() {
		$resultSet = new ResultSet ();
		
 		$sv = Bootstrap::getServiceManager ()->get ( 'Inventory\Services\SparepartService' );
		
		for($i = 1; $i <= 917; $i ++) {
			print $sv->createSparepartFolderById ( $i );
	} 
	*/
		
		
 	/* 	$sv = Bootstrap::getServiceManager ()->get ( 'Inventory\Services\AssetService' );
		
		for($i = 1; $i <= 808; $i ++) {
			print $sv->createAssetFolderById ( $i );
		}  
	}
	*/
	
 	public function testAssetGroupTest() {
		//$resultSet = new ResultSet ();
		$table = Bootstrap::getServiceManager()->get('Inventory\Model\SparePartCategoryMemberTable');
			
			
		//$table->add($assetType);
		//$table->update($assetType,1);
		var_dump($table);
	
	} 
	
	/*
	public function testAssetSearchServieTest() {
		$resultSet = new ResultSet ();
	
		$sv = Bootstrap::getServiceManager ()->get ( 'Inventory\Services\AssetSearchService' );
	
		$sv->createIndex();
		
		//$sv->search("00051");
		
	}
	
	*/
	/*
	public function testAssetSearchServieTest() {
		$resultSet = new ResultSet ();
		$sv = Bootstrap::getServiceManager ()->get ( 'Inventory\Services\SparePartsSearchService' );
		//$sv->createIndex();
		
		//var_dump($sv->search('bobbin'));
		
		$pictures_dir =  ROOT . DIRECTORY_SEPARATOR . "/data" . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "asset_22".DIRECTORY_SEPARATOR."pictures".DIRECTORY_SEPARATOR;
	
		$name='0002.png';
		
		var_dump($pictures_dir.$name);
		
		$im = imagecreatefrompng($pictures_dir.$name);
			
		$ox = imagesx($im);
		$oy = imagesy($im);
		
		$final_width_of_image =450;
		
		$nx = $final_width_of_image;
		$ny = floor($oy * ($final_width_of_image / $ox));
			
		$nm = imagecreatetruecolor($nx, $ny);
		
		$name_thumbnail = 'thumbnail_450_'.$name ;
		var_dump($name_thumbnail);
			
		imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
		imagepng($nm, "$pictures_dir/$name_thumbnail");
		
	}
	*/
	
	/*
	 public function testSparepartCatergoryTest() {
	  
	 $tbl = Bootstrap::getServiceManager()->get('Inventory\Model\WarehouseTable');
	 
	 var_dump($tbl->fetchAll()); 
	 }
	 */	
	 	
	 	 //$resultSet = new ResultSet ();
	 	// $tbl = Bootstrap::getServiceManager()->get('Inventory\Model\SparepartCategoryMemberTable');
	 	
	 	 //$data = new SparepartCategoryMember();
	 	 //$data->sparepart_id = 1;
	 	 //$data->sparepart_cat_id=1;
	 	 //var_dump($tbl->add($data)); 
	 	 //$result = $tbl->getTotalMembersOfCatID(12332);
	 	 //var_dump($result);
	 	 
		 /*
		 foreach ($result as $user) {
	        echo $user['name'];
	     }
	     */
	 	 
	  	 //$result = $tbl->getMovements('2016-03-01','2016-03-31','OUT');
	  	 
	  	//var_dump($result);
		
	  	 //$s = '22-4-00039';
	  	 //$p = strpos($s, "-",3) +1;
	  	 
	  	 //var_dump(strpos($s, "-",3));
	  	 
	  	 //var_dump(substr ($s,$p, strlen($s)-$p)*1);
	  	 
	 	 /*
	 	 var_dump($result = $tbl->fetchAll());
	  	 foreach ($result as $user) {
	 	 	var_dump ($user);
	  	 }
	  	 */
	  	 
	 	 /*
	 	 var_dump($result = $tbl->getArticles(0,0,0));
	 	 foreach ($result as $user) {
	 	 	var_dump ($user);
	 	 }
	 	 */
	 	
	 	//$sv = new EpartnerService();
	 	//var_dump($sv->get());
	  	  	 
	 //$table->add($assetType);
	
		//var_dump($tbl->getArticlesOf(39));
	 	 //$tbl->createIndex();
	 /* 
	 	 $hits =$tbl->search('boot',2);
		
	 	 $data = array();
	 	 	
	 	 foreach ($hits as $key => $value)
	 	 {
	 	 	$n = (int)$key;
	 	 	$data[$n]['id'] = $value->article_id;
	 	 	$data[$n]['name'] =  $value->name;
	 	 	$data[$n]['department_id'] =  $value->department_id;
	 	 }
	 	 
	 	 var_dump($data);
	 */ 
	 	/* 
	 	 
	 	 $emailText = <<<EOT
	 	 
<p>Welcome Mascot Laos Plattform!</p>
	 	 
Your account is created.<br/>
click on below link to activate your account:
<p>
Regards<br/>
MLA Team
</p>
<p>(<em>This Email is generated by the system.</em>)</p>
EOT;
	 	 
	 	 $html = new MimePart($emailText);
	 	 //$html->type = Mime::TYPE_HTML;
	 	 
	 	 $body = new MimeMessage();
	 	 $body->setParts(array($html));
	 	 
	 	 // build message
	 	 $message = new Message ();
	 	 $message->addFrom ( 'mib-team@web.de' );
	 	 $message->addTo ('ngmautri@outlook.com');
	 	 $message->setSubject ( 'Mascot Laos Plattform Register' );
	 	 
	 	 $type = new ContentType();
	 	 $type->setType('text/html');
	 	 
	 	 $message->getHeaders()->addHeader($type);
	 	 $message->setBody ($emailText);
	 	 
	 	 $mailTransport = Bootstrap::getServiceManager ()->get ( 'SmtpTransportService' );
	 	 
	 	 // send message
	 	 $mailTransport->send ( $message );
	 	 echo $emailText;
	 }
	 */

}