<?php
namespace Application\Application\Helper\ParamQuery\Contracts;

/**
 * Abstract ParamQuery
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractParamQueryPageModel
{

    protected $curPage;

    protected $rPP;

    protected $rPPOptions;

    protected $totalPages;

    protected $type;
}
