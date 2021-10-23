<?php
namespace ApplicationTest\UtilityTest;

use Cake\Utility\Inflector;
use PHPUnit_Framework_TestCase;

class CompositeTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        $results = [
            "po_id",
            "po_sys_number",
            "doc_type",
            "doc_id",
            "doc_token",
            "doc_sys_number",
            "doc_currency",
            "doc_net_amount",
            "local_net_amount",
            "doc_posting_date",
            "doc_date",
            "doc_created_date"
        ];

        foreach ($results as $s) {
            // echo sprintf("\npublic $%s;", Inflector::variable($s));
            echo sprintf("\n" . '$dto->set%s($result["%s"]);', Inflector::camelize($s), $s);
        }
    }
}
