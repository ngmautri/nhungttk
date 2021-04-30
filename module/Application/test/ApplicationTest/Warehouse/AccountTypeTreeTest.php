<?php
namespace ApplicationTest\AccountChart;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class AccountTypeTreeTest extends PHPUnit_Framework_TestCase
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

        // $builder = new DefaultAccountTypeTree();
        // $builder->initTree();
        // $root = $builder->createTree(AccountType::ROOT, 0);

        // var_dump($root->display());
        function getValues()
        {
            // Get memory usage data
            echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB' . PHP_EOL;
            for ($i = 1; $i < 1000000000; $i ++) {
                yield $i;
                // Do performance analysis, so you can measure memory usage
                if (($i % 200000) == 0) {
                    // Memory usage in MB
                    echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB' . PHP_EOL;
                }
            }
        }

        $myValues = getValues(); // No action before the loop

        echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB' . PHP_EOL;
        foreach ($myValues as $value) {}
        echo "===================";
        echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB' . PHP_EOL;
        echo 'Done1';
    }

    public function test1Other()
    {

        /*
         * function getValues1()
         * {
         * $valuesArray = [];
         * // Get the initial memory usage
         * echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB' . PHP_EOL;
         * for ($i = 1; $i < 9800000; $i ++) {
         * $valuesArray[] = $i;
         * // In order for us to analyze, we measure the amount of memory used.
         * if (($i % 200000) == 0) {
         * // Get memory usage in MB
         * echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB' . PHP_EOL;
         * }
         * }
         * return $valuesArray;
         * }
         * $myValues = getValues1(); // Once we call the function we will create an array here
         * foreach ($myValues as $value) {}
         * echo 'Done1';
         */
    }
}