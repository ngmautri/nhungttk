<?php
namespace ProcureTest\PO;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\GR\Options\CopyFromPOOptions;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

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

            $rep = new POQueryRepositoryImpl($doctrineEM);

            // $id = 302;
            // $token = "b69a9fbe-e7e5-48da-a7a7-cf7e27040d1b";

            $id = 353;
            $token = "1ab31858-3bf7-4003-b852-8d07a9255112";

            // $id = 283;
            // $token = "6Q7fdJQdhX_GyaE8h5qLD7fwQZ2QjwfE";

            $options = new CopyFromPOOptions(1, 39, __METHOD__);

            $po = $rep->getPODetailsById($id, $token);

            var_dump($po);

            /*
             * $headerValidators = new HeaderValidatorCollection();
             *
             * $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
             * $procureSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
             * $fxService = new FXService();
             * $fxService->setDoctrineEM($doctrineEM);
             *
             * $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService,$procureSpecsFactory);
             * $headerValidators->add($validator);
             *
             * $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
             * $headerValidators->add($validator);
             *
             * $validator = new GrPostingValidator($sharedSpecsFactory, $fxService);
             * $headerValidators->add($validator);
             *
             * $rowValidators = new RowValidatorCollection();
             * $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
             * $rowValidators->add($validator);
             *
             * $validator = new GLAccountValidator($sharedSpecsFactory, $fxService);
             * $rowValidators->add($validator);
             *
             *
             * $gr = GRDoc::createFromPo($po,$options,$headerValidators, $rowValidators);
             * var_dump($gr->getDocRows());
             */
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}