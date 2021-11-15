<?php
namespace ApplicationTest\Department\CodeGenerate;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Entity\AppCoaAccount;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = ChartSnapshotAssembler::createFormElementsFor(ChartSnapshot::class);
            // $result = AccountSnapshotAssembler::createFormElementsFunctionFor(AccountSnapshot::class);

            $result = GenericObjectAssembler::getMethodsComments(AppCoaAccount::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}