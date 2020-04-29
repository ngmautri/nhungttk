<?php
namespace ProcureTest\PO\Service;

use ProcureTest\Bootstrap;
use Procure\Application\Command\PO\Options\CopyFromQuoteOptions;
use Procure\Application\Service\PO\POService;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class POServiceTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            /** @var POService $sv ; */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');

            $companyId = 1;
            $userId = 39;
            $options = new CopyFromQuoteOptions($companyId, $userId, __METHOD__);

            $po = $sv->createFromQuotation(347, "bc1a6447-cca7-43a2-be70-32f4a87b2596", $options);
            var_dump($po);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}