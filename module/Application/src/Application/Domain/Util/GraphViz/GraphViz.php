<?php
namespace Application\Domain\Util\GraphViz;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GraphViz
{

    static public function translate($text)
    {
        // dot -Tpng input.gv -o outfile.png
        $f = 'dot %s -T%s -o %s';

        $executable = system(escapeshellarg($executable) . ' -T ' . escapeshellarg($this->format) . ' ' . escapeshellarg($tmp) . ' -o ' . escapeshellarg($tmp . '.' . $this->format), $ret);
    }
}


