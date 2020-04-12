<?php
namespace ProcureTest\GR\Command;

use Application\Entity\NmtProcureGrRow;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\GR\Options\CopyFromPOOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Infrastructure\Mapper\GrMapper;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;

class RepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
           
            $rep = new GRQueryRepositoryImpl($doctrineEM);
            
            $id = 94;
            $token="cc15908b-e12f-4403-bbda-ceb5d824f1f5";
            $rootEntity = $rep->getRootEntityByTokenId($id,$token);
            var_dump($rootEntity->getDocRows());
            
            
                        
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}