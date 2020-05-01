<?php
namespace ProcureTest\PO\Service;

use ProcureTest\Bootstrap;
use Procure\Application\Reporting\PO\PoReporter;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\PO\POService;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Filter\PoReportSqlFilter;
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
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');
            $isActive = 1;
            $balance = 1;
            $docYear = 2019;
            $docStatus = "posted";

            $reporter = $sv = Bootstrap::getServiceManager()->get(PoReporter::class);
            $filter = new PoReportSqlFilter();
            $filter->setIsActive($isActive);
            $filter->setBalance($balance);
            $filter->setDocYear($docYear);
            $filter->setDocStatus($docStatus);

            $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
            $total_records = $reporter->getAllRowTotal($filter);
            $sort_by = null;
            $sort = null;
            $limit = null;
            $offset = null;
            $result = $reporter->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);

            $a_json_final['data'] = $result;
            $decoded = json_encode($a_json_final);

            if (! function_exists('json_last_error')) {
                if ($decoded === false || $decoded === null) {
                    throw new \Exception('Could not decode JSON!');
                }
            } else {

                // Get the last JSON error.
                $jsonError = json_last_error();

                // In some cases, this will happen.
                if (is_null($decoded) && $jsonError == JSON_ERROR_NONE) {
                    throw new \Exception('Could not decode JSON!');
                }

                // If an error exists.
                if ($jsonError != JSON_ERROR_NONE) {
                    $error = 'Could not decode JSON! ';

                    // Use a switch statement to figure out the exact error.
                    switch ($jsonError) {
                        case JSON_ERROR_DEPTH:
                            $error .= 'Maximum depth exceeded!';
                            break;
                        case JSON_ERROR_STATE_MISMATCH:
                            $error .= 'Underflow or the modes mismatch!';
                            break;
                        case JSON_ERROR_CTRL_CHAR:
                            $error .= 'Unexpected control character found';
                            break;
                        case JSON_ERROR_SYNTAX:
                            $error .= 'Malformed JSON';
                            break;
                        case JSON_ERROR_UTF8:
                            $error .= 'Malformed UTF-8 characters found!';
                            break;
                        default:
                            $error .= 'Unknown error!';
                            break;
                    }
                    throw new \Exception($error);
                }
            }
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}