<?php
namespace Application\Domain\Util\Math;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class Permuation
{

    public function iterativeMethod($n, $r)
    {}

    public function recursiveMethod($n, $r)
    {}

    private function helper($combinition, $data, $start, $end, $index)
    {
        if ($index == count($data)) {
            $combinition[] = $data;
            // combinations . add(combination);
        } else if ($index <= $end) {
            $data[$index] = $start;
            $this->helper($combinition, $data, $start + 1, $end, $index + 1);
            $this->helper($combinition, $data, $start + 1, $end, $index);
        }
    }
}