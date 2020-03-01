<?php
namespace Inventory\Application\Service\Upload\Transaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractUploadStrategy
{

    abstract function doUploading($file);
}
    