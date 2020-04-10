<?php
namespace ProcureTest\GR\Command;

use Application\Entity\NmtProcureGrRow;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Infrastructure\Mapper\GrMapper;
use PHPUnit_Framework_TestCase;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Command\GR\Options\CopyFromPOOptions;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRRow;

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
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\GR\GRService');
            
            $rowEntity = new NmtProcureGrRow();
            $snapshot = new GRRowSnapshot();
            $snapshot->token = "sdfdfdsf";
            
            
            $source_id = 344;
            $source_token = "544fe921-8fd7-45dd-bc57-c8a98f5ee358";
            $version = 1;
            $userId = 39;
            $dto = new PoDTO();
            
            $options = new CopyFromPOOptions(1, $userId, __METHOD__);
            
            /**
             *
             * @var GRDoc $rootEntity ;
             */
            $rootEntity = $sv->createFromPO($source_id, $source_token, $options);
            
            $rows = $rootEntity->getDocRows();
            
            foreach ($rows as $row){
                
                /**
                 * @var GRRow $row ; 
                 */
                $rowEntity = new NmtProcureGrRow();
                $snapshot = $row->makeSnapshot();
                GrMapper::mapRowSnapshotEntity($doctrineEM, $snapshot, $rowEntity);
                var_dump($rowEntity->getToken());                   
            }
                        
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}