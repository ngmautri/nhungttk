<?php
namespace ApplicationTest\ItemAttribute\CodeGenerate;

use Application\Domain\Company\ItemAttribute\AttributeSnapshot;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = ChartSnapshotAssembler::(ChartSnapshot::class);
            // $result = AccountSnapshotAssembler::G(AccountSnapshot::class);

            // $result = GenericObjectAssembler::getMethodsComments(AppCoaAccount::class);
            $result = GenericObjectAssembler::createStoreMapping(AttributeSnapshot::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}