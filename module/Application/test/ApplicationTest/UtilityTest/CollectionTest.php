<?php
namespace ApplicationTest\UtilityTest;

use ApplicationTest\UtilityTest\Collection\TestCollectionRenderAsHtmlTable;
use Application\Application\Service\Document\Spreadsheet\TestOpenOfficeBuilder;
use Application\Domain\Util\Collection\GenericCollection;
use Application\Domain\Util\Collection\Render\TestRenderAsSpreadsheet;
use Application\Domain\Util\Pagination\Paginator;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use stdClass;

class CollectionTest extends PHPUnit_Framework_TestCase
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
        $collection = new GenericCollection();

        $element = new stdClass();
        $element->value1 = 'value1';
        $element->value2 = 'value2';
        $collection->add($element);

        $element = new stdClass();
        $element->value1 = 'value3';
        $element->value2 = 'value4';
        $collection->add($element);

        $collectionCount = 2;

        $builder = new TestOpenOfficeBuilder();
        $render = new TestRenderAsSpreadsheet($collectionCount, $collection);
        // $render->setBodyStartPos(5);
        // $render->setBuilder($builder);
        // $render->execute();
    }

    public function testRenderAsTable()
    {
        $collection = new GenericCollection();

        $element = new stdClass();
        $element->value1 = 'value1';
        $element->value2 = 'value2';
        $collection->add($element);

        $element = new stdClass();
        $element->value1 = 'value3';
        $element->value2 = 'value4';
        $collection->add($element);

        $collectionCount = 10;
        $paginator = new Paginator($collectionCount, 1, 2);
        $paginator->setBaseUrl('http://localhost/inventory/item/list2');
        $paginator->setUrlConnectorSymbol("?");
        $paginator->setDisplayHTMLDiv("nmt_test");

        $render = new TestCollectionRenderAsHtmlTable($collectionCount, $collection);
        $render->setPaginator($paginator);

        echo $render->execute();
        echo $render->printAjaxPaginator();
    }
}