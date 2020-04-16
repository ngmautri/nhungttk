<?php
namespace ProcureTest\Ap\Rep;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\GR\Options\SaveCopyFromAPOptions;
use Procure\Application\Service\FXService;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\DefaultRowValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\PoRowValidator;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
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

            $rep = new APQueryRepositoryImpl($doctrineEM);

            $id = 2814;
            $token = "PdlU__QUEe_SJoYi4U4fu0Toe2MYY9mz";

            $id = 2817;
            $token = "8178443a-55ef-44e8-a819-874e68480614";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            $options = new SaveCopyFromAPOptions(1, 39, __METHOD__, $rootEntity);

            $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
            $procureSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
            $fxService = new FXService();
            $fxService->setDoctrineEM($doctrineEM);
            $sharedService = new SharedService($sharedSpecsFactory, $fxService);

            // Header validator
            $headerValidators = new HeaderValidatorCollection();
            $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);
            $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);

            // Row validator
            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
            $rowValidators->add($validator);

            $validator = new PoRowValidator($sharedSpecsFactory, $fxService, $procureSpecsFactory);
            $rowValidators->add($validator);
            $gr = GRDoc::copyFromAP($rootEntity, $options, $headerValidators, $rowValidators);

            var_dump($gr);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}