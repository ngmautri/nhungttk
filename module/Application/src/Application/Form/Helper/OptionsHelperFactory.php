<?php
namespace Application\Form\Helper;

use Application\Application\DTO\Common\FormOptionDTO;
use Application\Application\DTO\Company\AccountChart\AccountForOptionDTO;
use Application\Application\DTO\Company\TreeNode\TreeNodeForOptionDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OptionsHelperFactory
{

    public static function createDepartmentOptions($list)
    {
        if ($list == null) {
            return null;
        }

        $tmp = [];
        foreach ($list as $l) {

            $tmp1 = array(
                $l->getDepartmentCode() => $l->getDepartmentShowName()
            );

            $tmp = \array_merge($tmp, $tmp1);
        }
        return $tmp;
    }

    public static function createDepartmentOptions1($list)
    {
        if ($list == null) {
            return null;
        }

        $tmp = [];
        foreach ($list as $l) {

            $tmp1 = [
                'value' => $l->getDepartmentName(),
                'label' => $l->getDepartmentShowName()
            ];

            $tmp[] = $tmp1;
        }
        // var_dump($tmp);
        return $tmp;
    }

    public static function createAccountOptions($list)
    {
        if ($list == null) {
            return null;
        }

        $tmp = [];
        foreach ($list as $l) {

            /**
             *
             * @var AccountForOptionDTO $l
             */
            $tmp1 = [
                'value' => $l->getAccountCode(),
                'label' => $l->getAccountShowName()
            ];

            $tmp[] = $tmp1;
        }
        // var_dump($tmp);
        return $tmp;
    }

    public static function createTreeNodeForOption($list)
    {
        if ($list == null) {
            return null;
        }

        $tmp = [];
        foreach ($list as $l) {

            /**
             *
             * @var TreeNodeForOptionDTO $l
             */
            $tmp1 = [
                'value' => $l->getNodeCode(),
                'label' => $l->getNodeShowName()
            ];

            $tmp[] = $tmp1;
        }
        return $tmp;
    }

    /**
     *
     * @param array $list
     * @return NULL|mixed[][]
     */
    public static function createValueOptions($list)
    {
        if ($list == null) {
            return null;
        }

        $tmp = [];
        foreach ($list as $l) {

            /**
             *
             * @var FormOptionDTO $l
             */
            $tmp1 = [
                'value' => $l->getValue(),
                'label' => $l->getName()
            ];

            $tmp[] = $tmp1;
        }
        return $tmp;
    }
}
