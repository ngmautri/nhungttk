<?php
namespace Procure\Infrastructure\Persistence\Reporting\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface PoApReportInterface
{

    public function getList(ProcureAppSqlFilterInterface $filter);

    public function getListTotal(ProcureAppSqlFilterInterface $filter);
}
