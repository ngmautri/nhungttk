<?php
namespace ProcureTest\Specification;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use PHPUnit_Framework_TestCase;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Procure\Application\Service\FXService;
use Procure\Domain\GoodsReceipt\Validator\DefaultHeaderValidator;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Domain\GoodsReceipt\Validator\DefaultRowValidator;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;

class SpecsTest extends PHPUnit_Framework_TestCase
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
            
            $factory = new ProcureSpecificationFactory($doctrineEM);
            $spec = $factory->getPoRowSpecification();
            $subject = array(
                "vendorId" => 1331,
                "poRowId" => 2169
            );
            var_dump($spec->isSatisfiedBy($subject));
            
           
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}