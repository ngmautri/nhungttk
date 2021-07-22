<?php
namespace Application\Application\Helper;

use Application\Domain\Util\Pagination\Paginator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class FormHelper
{

    public static function createTabs($list)
    {
        if ($list == null) {
            return;
        }

        $tabs = '';
        foreach ($list as $l) {

            $tabs = $tabs . \sprintf('<li>%s</li>', $l);
        }

        return $tabs;
    }

    public static function createDropDownBtn($list)
    {
        if ($list == null) {
            return;
        }
        $dropDown = '<div style="margin: 2px; font-size: 9pt">';
        $dropDown = $dropDown . '<div class="dropdown">';
        $dropDown = $dropDown . '<button style="color: black; padding: 2pt 2pt 2pt 2pt; color: navy; font-size: 8.5pt" class="btn btn-default dropdown-toggle btn-sm" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
        $dropDown = $dropDown . '<i class="fa fa-bars" aria-hidden="true"></i>&nbsp;Action&nbsp;<span class="caret"></span></button>';
        $dropDown = $dropDown . '<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';

        foreach ($list as $l) {

            if ($l == "divider") {
                $dropDown = $dropDown . '<li role="separator" class="divider"></li>';
            } else {
                $dropDown = $dropDown . \sprintf('<li>%s</li>', $l);
            }
        }

        $dropDown . $dropDown . '</ul></div></div>';

        return $dropDown;
    }

    public static function createCustomDropDownMenu($list, $id)
    {
        if ($list == null) {
            return;
        }

        $dropDown = \sprintf('<ul style="font-size: 9.5pt;" class="dropdown-menu" aria-labelledby="%s">', $id);

        foreach ($list as $l) {

            if ($l == "divider") {
                $dropDown = $dropDown . '<li role="separator" class="divider"></li>';
            } else {
                $dropDown = $dropDown . \sprintf('<li>%s</li>', $l);
            }
        }

        $dropDown = $dropDown . '</ul>';

        return $dropDown;
    }

    public static function createButton($name, $title, $url, $icon)
    {
        $btn = '<a href="%s" title="%s" >%s</a>';
        $btn1 = '<a href="%s" title="%s" ><small><i class="%s"></i></small> %s</a>';

        if ($icon !== null) {
            return sprintf($btn1, $url, $title, $icon, $name);
        } else {
            return sprintf($btn, $url, $title, $name);
        }
    }

    public static function createProgressDiv($completion, $caption)
    {
        $progress_cls = "";
        $color = "black";

        if ($completion == 1) {
            $progress_cls = "progress-bar-success";
        }

        if ($completion <= 0.5) {
            $progress_cls = "progress-bar-warning";
            $color = "graytext";
        }

        if ($completion < 1 && $completion > 0.5) {
            $color = "white";
        }

        $completion = round($completion * 100, 0);

        $progress_div = sprintf('<div class="progress" style="height: 18px; margin-bottom:2pt;">
<div class="progress-bar %s" role="progressbar" style="width:%s%s;" aria-valuenow="%s" aria-valuemin="0" aria-valuemax="100">
 <span style="font-size:7.5pt; padding: 0px 0px 1px 1px; color:%s;">%s%s %s</span>
</div>
</div>', $progress_cls, $completion, "%", $completion, $color, $completion, "%", $caption);

        return $progress_div;
    }

    /**
     *
     * @param string $base
     * @param Paginator $paginator
     * @param string $connector_symbol
     */
    public static function createPaginator($base, Paginator $paginator = null, $connector_symbol)
    {
        if (! $paginator instanceof Paginator) {
            return;
        }

        $last = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $paginator->getTotalPages(), $paginator->getResultsPerPage());
        $first = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, 1, $paginator->getResultsPerPage());

        $p1 = ($paginator->getPage()) - 1;
        $p2 = ($paginator->getPage()) + 1;

        $prev = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $p1, $paginator->getResultsPerPage());
        $next = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $p2, $paginator->getResultsPerPage());

        $paginator_str = '<ul class="pagination pagination-sm">';

        if ($paginator->getPage() != 1 and $paginator->getTotalPages() > 10) {
            $paginator_str = $paginator_str . \sprintf('<li><a href="%s">%s</a></li>', $first, "|<");
            $paginator_str = $paginator_str . \sprintf('<li><a href="%s">%s</a></li>', $prev, "<");
        }

        for ($i = $paginator->getMinInPageSet(); $i <= $paginator->getMaxInPageSet(); $i ++) {

            $url = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $i, $paginator->getResultsPerPage());

            if ($i == $paginator->getPage()) {
                $paginator_str = $paginator_str . \sprintf('<li><a class="active" href="#">%s</a></li>', $i);
            } else {
                $paginator_str = $paginator_str . \sprintf('<li><a href="%s">%s</a></li>', $url, $i);
            }
        }
        if ($paginator->getPage() != $paginator->getTotalPages() and $paginator->getTotalPages() > 10) {

            $paginator_str = $paginator_str . \sprintf('<li><a href="%s">%s</a></li>', $next, ">");
            $paginator_str = $paginator_str . \sprintf('<li><a href="%s">%s</a></li>', $last, ">|");
        }
        $paginator_str = $paginator_str . '</ul>';

        return $paginator_str;
    }

    public static function createPaginator1($base, \Application\Domain\Util\Pagination\Paginator $paginator = null, $connector_symbol)
    {
        if (! $paginator instanceof Paginator) {
            return;
        }

        $last = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $paginator->getT, $paginator->getResultsPerPage());
        $first = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, 1, $paginator->getResultsPerPage());

        $p1 = ($paginator->getPage()) - 1;
        $p2 = ($paginator->getPage()) + 1;

        $prev = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $p1, $paginator->getResultsPerPage());
        $next = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $p2, $paginator->getResultsPerPage());

        $paginator_str = '<ul class="pagination pagination-sm">';

        if ($paginator->getPage() != 1 and $paginator->getTotalPages() > 10) {
            $paginator_str = $paginator_str . \sprintf('<li><a href="%s">%s</a></li>', $first, "|<");
            $paginator_str = $paginator_str . \sprintf('<li><a href="%s">%s</a></li>', $prev, "<");
        }

        for ($i = $paginator->getMinInPageSet(); $i <= $paginator->getMaxInPageSet(); $i ++) {

            $url = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $i, $paginator->getResultsPerPage());

            if ($i == $paginator->getPage()) {
                $paginator_str = $paginator_str . \sprintf('<li><a class="active" href="#">%s</a></li>', $i);
            } else {
                $paginator_str = $paginator_str . \sprintf('<li><a href="%s">%s</a></li>', $url, $i);
            }
        }
        if ($paginator->getPage() != $paginator->getTotalPages() and $paginator->getTotalPages() > 10) {

            $paginator_str = $paginator_str . \sprintf('<li><a href="%s">%s</a></li>', $next, ">");
            $paginator_str = $paginator_str . \sprintf('<li><a href="%s">%s</a></li>', $last, ">|");
        }
        $paginator_str = $paginator_str . '</ul>';

        return $paginator_str;
    }

    public static function createPaginatorAjax($base, Paginator $paginator = null, $connector_symbol, $result_div)
    {
        if (! $paginator instanceof Paginator || $result_div == null) {
            return;
        }

        $last = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $paginator->getTotalPages(), $paginator->getResultsPerPage());
        $first = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, 1, $paginator->getResultsPerPage());

        $first_js = \sprintf("doPaginator('%s','%s')", $first, $result_div);
        $last_js = \sprintf("doPaginator('%s','%s')", $last, $result_div);

        $p1 = ($paginator->getPage()) - 1;
        $p2 = ($paginator->getPage()) + 1;

        $prev = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $p1, $paginator->getResultsPerPage());
        $next = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $p2, $paginator->getResultsPerPage());
        $next_js = \sprintf("doPaginator('%s','%s')", $next, $result_div);
        $prev_js = \sprintf("doPaginator('%s','%s')", $prev, $result_div);

        $paginator_str = '<ul class="pagination pagination-sm">';

        if ($paginator->getPage() != 1 and $paginator->getTotalPages() > 10) {
            $paginator_str = $paginator_str . \sprintf('<li><a href="javascript:;" onclick="%s;">%s</a></li>', $first_js, "|<");
            $paginator_str = $paginator_str . \sprintf('<li><a href="javascript:;" onclick="%s;">%s</a></li>', $prev_js, "<");
        }

        for ($i = $paginator->getMinInPageSet(); $i <= $paginator->getMaxInPageSet(); $i ++) {

            $url = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $i, $paginator->getResultsPerPage());
            $url_js = \sprintf("doPaginator('%s','%s')", $url, $result_div);

            if ($i == $paginator->getPage()) {
                $paginator_str = $paginator_str . \sprintf('<li><a class="active" href="#">%s</a></li>', $i);
            } else {
                $paginator_str = $paginator_str . \sprintf('<li><a href="javascript:;" onclick="%s;">%s</a></li>', $url_js, $i);
            }
        }
        if ($paginator->getPage() != $paginator->getTotalPages() and $paginator->getTotalPages() > 10) {
            $paginator_str = $paginator_str . \sprintf('<li><a href="javascript:;" onclick="%s;">%s</a></li>', $next_js, ">");
            $paginator_str = $paginator_str . \sprintf('<li><a href="javascript:;" onclick="%s;">%s</a></li>', $last_js, ">|");
        }
        $paginator_str = $paginator_str . '</ul>';

        return $paginator_str;
    }
}
