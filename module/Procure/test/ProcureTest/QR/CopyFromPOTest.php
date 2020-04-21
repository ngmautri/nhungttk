<?php
namespace ProcureTest\QR;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\GR\Options\CopyFromPOOptions;
use Procure\Application\Service\FXService;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\Validator\Header\APPostingValidator;
use Procure\Domain\AccountPayable\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\AccountPayable\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\AccountPayable\Validator\Row\DefaultRowValidator;
use Procure\Domain\AccountPayable\Validator\Row\GLAccountValidator;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class CopyFromPOTest extends PHPUnit_Framework_TestCase
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

            $id = 349;
            $token = "4947d69d-78c8-44f5-b89f-07952cdc81ef";

            // $id = 283;
            // $token = "6Q7fdJQdhX_GyaE8h5qLD7fwQZ2QjwfE";

            $options = new CopyFromPOOptions(1, 39, __METHOD__);

            $po = $rep->getPODetailsById($id, $token);

            $headerValidators = new HeaderValidatorCollection();

            $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
            $procureSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
            $fxService = new FXService();
            $fxService->setDoctrineEM($doctrineEM);

            $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService, $procureSpecsFactory);
            $headerValidators->add($validator);

            $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);

            $validator = new APPostingValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);

            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
            $rowValidators->add($validator);

            $validator = new GLAccountValidator($sharedSpecsFactory, $fxService);
            $rowValidators->add($validator);

            $ap = APDoc::createFromPo($po, $options, $headerValidators, $rowValidators);

            var_dump($ap);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}