<?php
namespace Procure\Model\Re;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class DownloadStrategyAbstract
{
    abstract public function doDownload($pr, $rows);
}