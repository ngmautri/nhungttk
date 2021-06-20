<?php
namespace ApplicationTest\Brand\CodeGenerate;

use Application\Domain\Company\Brand\BrandSnapshot;
use Application\Domain\Company\Brand\BrandSnapshotAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = BrandSnapshotAssembler::createFormElementsExclude(BrandSnapshot::class);
            $result = BrandSnapshotAssembler::createFormElementsFunctionExclude(BrandSnapshot::class);

            // $result = AccountSnapshotAssembler::G(AccountSnapshot::class);

            // $result = GenericObjectAssembler::getMethodsComments(AppCoaAccount::class);
            // $result = GenericObjectAssembler::createGetMapping(BrandSnapshot::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}