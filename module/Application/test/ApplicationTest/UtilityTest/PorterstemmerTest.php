<?php
namespace ApplicationTest\UtilityTest;

use Application\Domain\Util\Search\Stemmer\PorterStemmer;
use Cake\Utility\Inflector;
use PHPUnit_Framework_TestCase;

class CompositeTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        $word = 'connection';

        $result = PorterStemmer::stem($word);

        $result = Inflector::camelize('Garment Size');
        \var_dump($result);
    }
}
