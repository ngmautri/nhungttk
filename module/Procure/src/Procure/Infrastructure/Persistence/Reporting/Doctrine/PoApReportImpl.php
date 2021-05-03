<?php
namespace Procure\Infrastructure\Persistence\Reporting\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Procure\Infrastructure\Persistence\Reporting\Contracts\PoApReportInterface;
use Procure\Infrastructure\Persistence\Reporting\Contracts\ProcureAppSqlFilterInterface;
use Procure\Infrastructure\Persistence\Reporting\Helper\PoApReportHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoApReportImpl extends AbstractDoctrineRepository implements PoApReportInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\Reporting\Contracts\PoApReportInterface::getList()
     */
    public function getList(ProcureAppSqlFilterInterface $filter)
    {
        // var_dump($filter);
        $results = PoApReportHelper::getList($this->getDoctrineEM(), $filter);
        return $results;
    }

    public function getListTotal(ProcureAppSqlFilterInterface $filter)
    {
        $result = PoApReportHelper::getListTotal($this->getDoctrineEM(), $filter);
        return $result;
    }

    public function getList1(ProcureAppSqlFilterInterface $filter)
    {
        // var_dump($filter);
        $results = PoApReportHelper::getList($this->getDoctrineEM(), $filter);
        return $results;

        /*
         * if ($results == null) {
         * yield null;
         * }
         *
         * foreach ($results as $r) {
         * $obj = json_decode(json_encode((object) $r), FALSE);
         * // $obj = GenericObjectAssembler::updateAllFieldsFromArray(new PoApDTO(), $r);
         * yield $obj;
         * }
         */
    }
}
