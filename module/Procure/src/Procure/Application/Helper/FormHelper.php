<?php
namespace Procure\Application\Helper;

use Application\Domain\Util\Pagination\Paginator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class FormHelper
{

    public static function createButton($name, $title, $url, $icon, $newTab = false)
    {
        $btn = '<a href="%s" title="%s" >%s</a>';
        $btn1 = '<a href="%s" title="%s" ><small><i class="%s"></i></small> %s</a>';

        if ($newTab) {
            $btn = '<a target="_blank" href="%s" title="%s" >%s</a>';
            $btn1 = '<a target="_blank" href="%s" title="%s" ><small><i class="%s"></i></small> %s</a>';
        }

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
     * @return void|string
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

    public static function createNewPaginator($base, \Application\Domain\Util\Pagination\Paginator $paginator = null, $connector_symbol)
    {
        if (! $paginator instanceof \Application\Domain\Util\Pagination\Paginator) {
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

    public static function createNewPaginatorAjax($base, \Application\Domain\Util\Pagination\Paginator $paginator = null, $connector_symbol, $result_div)
    {
        if (! $paginator instanceof \Application\Domain\Util\Pagination\Paginator || $result_div == null) {
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
