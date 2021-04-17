<?php
namespace ApplicationTest\Department\CodeGenerate;

use Application\Application\Contracts\GenericDTOAssembler;
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

            $result = GenericDTOAssembler::createGetMapping(AppCoaAccount::class);
            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}