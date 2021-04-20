<?php
namespace Application\Domain\Company\AccountChart;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericAccount extends BaseAccount
{

    public static function constructFromDB(AccountSnapshot $snapshot)
    {
        Assert::isInstanceOf($snapshot, AccountSnapshot::class, "AccountSnapshot is required!");

        $instance = new GenericAccount();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }

    public static function createFromSnapshot(GenericChart $rootDoc, AccountSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, GenericChart::class, "GenericChart required!");
        Assert::isInstanceOf($snapshot, AccountSnapshot::class, "AccountSnapshot is required!");

        $instance = new GenericAccount();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }
}
