<?php
namespace ApplicationTest\UtilityTest;

use Doctrine\ORM\EntityManager;
use Fhaculty\Graph\Graph;
use Graphp\GraphViz\GraphViz;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - Ngmautri@gmail.com
 *        
 */
class GraphVizTest extends PHPUnit_Framework_TestCase
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
        $tmp = tempnam(sys_get_temp_dir(), 'graphviz');
        var_dump($tmp);

        $graph = new Graph();

        $blue = $graph->createVertex();
        $blue->setAttribute('taillabel', 'blue');
        $blue->setAttribute('graphviz.color', 'blue');

        $red = $graph->createVertex();
        $red->setAttribute('taillabel', 'red');
        $red->setAttribute('graphviz.color', 'red');

        $e = $blue->createEdge($red);
        $graph->addEdge($e);

        $graphviz = new GraphViz();
        $graphviz->display($graph);
    }
}