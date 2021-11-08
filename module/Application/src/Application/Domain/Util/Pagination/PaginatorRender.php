<?php
namespace Application\Domain\Util\Pagination;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PaginatorRender
{

    /**
     *
     * @param Paginator $paginator
     * @param string $base
     * @param string $connector_symbol
     * @return void|string
     */
    public static function createPaginator(Paginator $paginator, $base, $connector_symbol)

    {
        if (! $paginator instanceof Paginator) {
            return;
        }

        if ($paginator->getTotalPages() == 1) {
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

    /**
     *
     * @param Paginator $paginator
     * @param string $base
     * @param string $connector_symbol
     * @param string $result_div
     * @return void|string
     */
    public static function createPaginatorAjax(Paginator $paginator, $base, $connector_symbol, $result_div)
    {
        if (! $paginator instanceof Paginator || $result_div == null) {
            return;
        }

        if ($paginator->getTotalPages() == 1) {
            return;
        }

        $last = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $paginator->getTotalPages(), $paginator->getResultsPerPage());
        $first = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, 1, $paginator->getResultsPerPage());

        $first_js = \sprintf("doPaginatorV1('%s','%s')", $first, $result_div);
        $last_js = \sprintf("doPaginatorV1('%s','%s')", $last, $result_div);

        $p1 = ($paginator->getPage()) - 1;
        $p2 = ($paginator->getPage()) + 1;

        $prev = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $p1, $paginator->getResultsPerPage());
        $next = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $p2, $paginator->getResultsPerPage());
        $next_js = \sprintf("doPaginatorV1('%s','%s')", $next, $result_div);
        $prev_js = \sprintf("doPaginatorV1('%s','%s')", $prev, $result_div);

        $paginator_str = '<ul class="pagination pagination-sm">';

        if ($paginator->getPage() != 1 and $paginator->getTotalPages() > 10) {
            $paginator_str = $paginator_str . \sprintf('<li><a href="javascript:;" onclick="%s;">%s</a></li>', $first_js, "|<");
            $paginator_str = $paginator_str . \sprintf('<li><a href="javascript:;" onclick="%s;">%s</a></li>', $prev_js, "<");
        }

        for ($i = $paginator->getMinInPageSet(); $i <= $paginator->getMaxInPageSet(); $i ++) {

            $url = \sprintf("%s%spage=%s&perPage=%s", $base, $connector_symbol, $i, $paginator->getResultsPerPage());
            $url_js = \sprintf("doPaginatorV1('%s','%s')", $url, $result_div);

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