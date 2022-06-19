<?php
namespace Application\Form\Helper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultOptions
{

    public static function createResultPerPageOption()
    {
        $input = [
            "10" => 10,
            "15" => 15,
            "20" => 20,
            "30" => 30,
            "50" => 50,
            "100" => 100
        ];
        $o = [];
        foreach ($input as $k => $v) {
            $tmp = [
                'value' => $v,
                'label' => $k
            ];

            $o[] = $tmp;
        }

        return $o;
    }

    public static function createYearOption()
    {
        for ($y = 2012; $y <= 2030; $y ++) {

            $tmp = [
                'value' => $y,
                'label' => $y
            ];
            $o[] = $tmp;
        }
        return $o;
    }

    public static function createMonthOption()
    {
        $tmp = [
            'value' => 0,
            'label' => 'Please select month'
        ];
        $o[] = $tmp;

        for ($m = 1; $m <= 12; $m ++) {

            $tmp = [
                'value' => $m,
                'label' => $m
            ];
            $o[] = $tmp;
        }
        return $o;
    }
}
