<?php
namespace InventoryTest\Transatcion\GR;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Doctrine\ORM\EntityManager;
use Inventory\Application\Command\Transaction\Options\CopyFromGROptions;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\GR\GRFromPurchasing;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;
use Inventory\Domain\Transaction\Validator\Header\DefaultHeaderValidator;
use Inventory\Domain\Transaction\Validator\Row\DefaultRowValidator;
use Inventory\Domain\Transaction\Validator\Row\WarehouseValidator;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class GRFromPurchasingTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new GRQueryRepositoryImpl($doctrineEM);
            /*
             * $id = 122;
             * $token = "e2cfba04-279d-4054-b77d-72e70efa94ea";
             */
            $id = 132;
            $token = "73dccf90-9006-4e9f-88ec-a284a1889e8e";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            /*
             * $results = $rootEntity->slipByDepartment();
             * // var_dump($results->getAll());
             * foreach ($results->getAll() as $k => $v) {
             * echo "\n" . $k . "\n";
             * foreach ($v as $c) {
             * echo "---" . $c[0]->getItemName() . '; ' . $c[0]->getPrNumber() . "\n";
             * }
             * }
             */

            $companyId = 1;
            $userId = 39;

            $options = new CopyFromGROptions($companyId, $userId, __METHOD__);

            $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
            $fxService = new FXService();
            $fxService->setDoctrineEM($doctrineEM);
            $sharedService = new SharedService($sharedSpecsFactory, $fxService);

            $headerValidators = new HeaderValidatorCollection();
            $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);

            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
            $rowValidators->add($validator);

            $rowValidators = new RowValidatorCollection();
            $validator = new WarehouseValidator($sharedSpecsFactory, $fxService);
            $rowValidators->add($validator);

            $cmdRepository = new TrxCmdRepositoryImpl($doctrineEM);
            $postingService = new TrxPostingService($cmdRepository);
            $snapshot = new TrxSnapshot();

            $gm = new GRFromPurchasing();
            $gm->createFromProcureGR($rootEntity, $snapshot, $options, $headerValidators, $rowValidators, $sharedService, $postingService);

            var_dump($gm->getTotalRows());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}