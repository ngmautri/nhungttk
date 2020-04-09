<?php
namespace ProcureTest\PO;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Service\FXService;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\Validator\Row\DefaultRowValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\PoRowValidator;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use PHPUnit_Framework_TestCase;
use Procure\Domain\GoodsReceipt\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\GLAccountValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\NullHeaderValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\NullRowValidator;
use Procure\Application\Command\GR\CreateFromPOCmd;
use Procure\Application\Command\GR\Options\CreateFromPOOptions;
use Procure\Domain\GoodsReceipt\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\GrPostingValidator;
use Procure\Application\Command\GR\CopyFromPOCmd;
use Procure\Application\Command\GR\Options\CopyFromPOOptions;

class POQueryRepositoryTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $rep = new DoctrinePOQueryRepository($doctrineEM);

            // $id = 302;
            // $token = "b69a9fbe-e7e5-48da-a7a7-cf7e27040d1b";

            $id = 338;
            $token = "040b6a84-e217-4cfa-80e3-a9a9eb2c76ef";

            // $id = 283;
            // $token = "6Q7fdJQdhX_GyaE8h5qLD7fwQZ2QjwfE";

            $options = new CopyFromPOOptions(1, 39, __METHOD__);

            $po = $rep->getPODetailsById($id, $token);

            $headerValidators = new HeaderValidatorCollection();

            $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
            $procureSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
            $fxService = new FXService();
            $fxService->setDoctrineEM($doctrineEM);

            $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService,$procureSpecsFactory);
            $headerValidators->add($validator);

            $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);
            
            $validator = new GrPostingValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);
            
            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
            $rowValidators->add($validator);

            $validator = new GLAccountValidator($sharedSpecsFactory, $fxService);
            $rowValidators->add($validator);

            
            $gr = GRDoc::createFromPo($po,$options,$headerValidators, $rowValidators);
            var_dump($gr->makeDetailsDTO());
            
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}