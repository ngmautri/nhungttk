<?php
namespace ApplicationTest\UtilityTest;

use Application\Domain\Util\Pagination\Paginator;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class PaginationTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        $paginator = new Paginator(15, 2, 14);
        $baseUrl = 'http://localhost/procure/qr/list';
        $connector_symbol = '?';

        var_dump($paginator->getTotalResults());
        var_dump($paginator->getOffset());
        var_dump($paginator->getLimit());
    }
}