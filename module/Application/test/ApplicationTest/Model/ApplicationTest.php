<?php
namespace ApplicationTest\Model;


use Ramsey\Uuid\Uuid;
use PHPUnit_Framework_TestCase;
use Application\Domain\Company\CompanyId;
use Application\Domain\Company\Company;

class ApplicationTest extends PHPUnit_Framework_TestCase {
	
	
	 public function testOther() {
		
	     $id = Uuid::uuid4()->toString();
	     $companId = new CompanyId($id);
	     $company = new Company($companId);
	    
	     var_dump ($company);
	 }

}