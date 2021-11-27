<?php
namespace Application\Domain\Util\FileSystem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FolderHelper
{

    static public function linuxPath($path)
    {

        /**
         * Important! for UBUNTU
         */
        return str_replace('\\', '/', $path);
    }
}

