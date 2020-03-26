<?php
namespace AssetTypeTest\Model;

use PHPUnit_Framework_TestCase;

class PtherTest extends PHPUnit_Framework_TestCase
{

    public function testAssetGroupTest()
    {
        $path_array = explode("/", "8/9/13/14/15/");

        $role_level = array();
        if (count($path_array) > 0) {
            $level = 0;
            foreach ($path_array as $a) {
                $level ++;
                $tmp = array(
                    $a => $level
                );
                $role_level[] = $tmp;
            }
        }

        // var_dump($role_level);

        foreach ($role_level as $l) {

            foreach ($l as $k => $v) {
                // echo $k;

                if ($k == 9) {
                    echo "Level " . $v;
                }
            }
        }
    }
}