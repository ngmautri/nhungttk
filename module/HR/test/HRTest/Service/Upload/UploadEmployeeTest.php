<?php
namespace HRTest\Service\Upload;

use Doctrine\ORM\EntityManager;
use HRTest\Bootstrap;
use HR\Application\Service\Upload\Employee\UploadEmployee;
use PHPUnit_Framework_TestCase;

class UploadEmployeeTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
            echo $root;

            /**
             *
             * @var UploadEmployee $uploader
             */
            $uploader = Bootstrap::getServiceManager()->get(UploadEmployee::class);
            $uploader->setUserId(39);
            $uploader->setCompanyId(1);
            $file = $root . "/HRTest/InputData/hr_individuals-3.xlsx";
            $trx = $uploader->run($file);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}