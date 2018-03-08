<?php
namespace HRTest;

use PHPUnit_Framework_TestCase;
use HR\Payroll\PITIncome;

/**
 * Test HR
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class HRTest extends PHPUnit_Framework_TestCase
{

    public function testDBTest()
    {
        
        // $sv = Bootstrap::getServiceManager ()->get ( 'HR\Service\EmployeeSearchService' );
        $n = new PITIncome();
        echo "taxe payble: " . $n->isPayble();
    }
}