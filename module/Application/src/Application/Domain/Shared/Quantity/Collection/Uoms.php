<?php
namespace Application\Domain\Shared\Uom\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Uom\Contracts\UomsInterface;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomSnapshot;

/**
 * Implement this to provide a list of currencies.
 *
 * @author Mathias Verraes
 */
final class Uoms extends ArrayCollection
{

    private $input;

    private function loadUomsFromFile()
    {
        $file = __DIR__ . '/../../../../../../resources/uom.php';

        if (file_exists($file)) {
            $uomArray = require $file;
            $this->input = $uomArray;
            return;

        }
        throw new \RuntimeException('Failed to uom!');
    }

    public function __construct()
    {
        $this->loadUomsFromFile();

        ksort($this->input);

        foreach($this->input as $k=>$v){

            $snapshot =  new UomSnapshot();
            $snapshot->setUomName($k);
            $snapshot->setUomCode($v['uomCode']);
            $snapshot->setSymbol($v['symbol']);

            $this->add(Uom::createFrom($snapshot));

        }

    }

}
