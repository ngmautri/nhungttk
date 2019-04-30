<?php
namespace AssetTypeTest\Model;


use Ramsey\Uuid\Uuid;
use PHPUnit_Framework_TestCase;
use Application\Domain\Company\Company;
use Application\Model\Constants;

class AssetTypeTest extends PHPUnit_Framework_TestCase {
	
	
	 public function testOther() {
		
	     $id = Uuid::uuid4()->toString();
	     var_dump ($id);
	     
	     $cid = new \Application\Domain\Company($id);
	    
	     var_dump ($cid);
	 }

}