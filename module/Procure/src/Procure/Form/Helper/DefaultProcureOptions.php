<?php
namespace Procure\Form\Helper;

use Procure\Domain\Contracts\ProcureDocStatus;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultProcureOptions
{

    public static function createPRDocStatusOption()
    {
        $input = [
            "draft" => ProcureDocStatus::DRAFT,
            "posted" => ProcureDocStatus::POSTED
        ];

        $tmp = [
            'value' => 'all',
            'label' => 'all'
        ];
        $o[] = $tmp;

        foreach ($input as $k => $v) {
            $tmp = [
                'value' => $v,
                'label' => $k
            ];

            $o[] = $tmp;
        }

        return $o;
    }

    public static function createPRBalanceOption()
    {
        $input = [
            "completed" => 1,
            "pending" => 2
        ];

        $tmp = [
            'value' => 100,
            'label' => 'all'
        ];
        $o[] = $tmp;

        foreach ($input as $k => $v) {
            $tmp = [
                'value' => $v,
                'label' => $k
            ];

            $o[] = $tmp;
        }

        return $o;
    }

    public static function createPRRowBalanceOption()
    {
        $input = [
            "completed" => 'completed',
            "pending" => 'pending',
            "quoted" => 'quoted'
        ];

        $tmp = [
            'value' => 100,
            'label' => 'all'
        ];
        $o[] = $tmp;

        foreach ($input as $k => $v) {
            $tmp = [
                'value' => $v,
                'label' => $k
            ];

            $o[] = $tmp;
        }

        return $o;
    }
}
