<?php
namespace ProcureTest\PO\Service;

use ProcureTest\Bootstrap;
use Procure\Application\Reporting\AP\ApReporter;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\PO\POService;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Filter\ApReportSqlFilter;
use PHPUnit_Framework_TestCase;

class ReporterTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            /** @var POService $sv ; */

            $reporter = $sv = Bootstrap::getServiceManager()->get(ApReporter::class);

            $isActive = 1;
            $balance = 1;
            $docYear = 2019;
            $docStatus = "posted";

            $filter = new ApReportSqlFilter();
            $filter->setIsActive($isActive);
            $filter->setBalance($balance);
            $filter->setDocYear($docYear);
            $filter->setDocStatus($docStatus);

            $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
            $total_records = $reporter->getAllRowTotal($filter);
            $sort_by = null;
            $sort = null;
            $limit = 1;
            $offset = 10;
            $result = $reporter->getList($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);

            $a_json_final['data'] = $result;
            $decoded = json_encode($a_json_final);

            var_dump($decoded);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}