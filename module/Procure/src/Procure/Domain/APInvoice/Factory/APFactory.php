<?php
namespace Procure\Domain\APInvoice;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APFactory
{

    public static function createAPDocument($apDocTypeId)
    {
        switch ($apDocTypeId) {

            case APDocType::AP_INVOICE:
                $entityRoot = new APInvoice();
                break;
        }
        return $entityRoot;
    }
}