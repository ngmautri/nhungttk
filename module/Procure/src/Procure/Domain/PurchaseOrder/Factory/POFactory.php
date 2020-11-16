<?php
namespace Procure\Domain\PurchaseOrder\Factory;

use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\APFromPO;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\SharedService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class POFactory
{

    public static function createFromPo(PODoc $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        return APFromPO::createFromPo($sourceObj, $options, $sharedService);
    }
}