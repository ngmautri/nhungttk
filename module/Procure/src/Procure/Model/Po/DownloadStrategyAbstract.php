<?php
namespace Procure\Model\Po;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class DownloadStrategyAbstract
{
    abstract public function doDownload($pr, $rows);
}