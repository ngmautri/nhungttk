<?php
namespace Inventory\Application\Service\Upload\Transaction\Contracts;

use Inventory\Domain\Transaction\GenericTrx;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface TrxRowsUploadInterface
{

    public function doUploading(GenericTrx $trx, $file);
}
    