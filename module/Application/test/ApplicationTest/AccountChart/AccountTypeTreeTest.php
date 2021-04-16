<?php
namespace ApplicationTest\AccountChart;

use Application\Domain\Company\AccountChart\AccountType\Tree\DefaultAccountTypeTree;
use Application\Domain\Company\Contracts\Account\AccountType;
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
        $builder = new DefaultAccountTypeTree();
        $builder->initTree();
        $root = $builder->createTree(AccountType::ROOT, 0);

        var_dump($root->display());
    }
}