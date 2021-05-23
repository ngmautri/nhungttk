<?php
namespace Application\Domain\Util\Math;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class Combinition
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

    public function getPossibleCombinitions($groups, $prefix = '', $seperator = ';')
    {
        $result = array();
        $group = array_shift($groups);
        foreach ($group as $selected) {
            if ($groups) {
                $result = array_merge($result, $this->getPossibleCombinitions($groups, $prefix . $selected . $seperator, $seperator));
            } else {
                $result[] = $prefix . $selected;
            }
        }

        if ($result == null) {
            throw new \InvalidArgumentException('Can not create combination!');
        }

        return $result;
    }

    /**
     *
     * @param array $groups
     * @param string $prefix
     * @throws \InvalidArgumentException
     * @return [][]
     */
    public function getPossibleCombinitionArray($groups, $prefix = '')
    {
        $separator = '[;;]';
        $combinitions = $this->getPossibleCombinitions($groups, $prefix, $separator);

        $result = [];
        foreach ($combinitions as $c) {
            $attributeArray = \explode($separator, $c);

            $tmp = [];
            foreach ($attributeArray as $a) {
                if ($a == null) {
                    continue;
                }
                $tmp[] = $a;
            }
            $result[] = $tmp;
        }
        return $result;
    }

    public function getPossibleCombinitionsV1($groups, $prefix = '')
    {
        $result = array();
        $group = array_shift($groups);
        foreach ($group as $selected) {
            if ($groups) {
                $tmp[] = $selected;
                $result = array_merge($tmp, $this->getPossibleCombinitionsV1($groups, $prefix . $selected . ' '));
            } else {
                $tmp[] = $selected;
                $result[] = $selected;
            }
        }
        return $result;
    }
}