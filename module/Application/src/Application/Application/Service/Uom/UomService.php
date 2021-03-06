<?php
namespace Application\Application\Service\Uom;

use Application\Application\Service\Uom\Contracts\UomServiceInterface;
use Application\Application\Service\ValueObject\ValueObjectService;
use Application\Domain\Shared\Uom\Uom;
use Application\Infrastructure\Persistence\Doctrine\UomCrudRepositoryImpl;
use Application\Infrastructure\Persistence\Filter\UomSqlFilter;

class UomService extends ValueObjectService implements UomServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\ValueObject\ValueObjectService::setCrudRepository()
     */
    protected function getCrudRepository()
    {
        return new UomCrudRepositoryImpl($this->getDoctrineEM());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\ValueObject\ValueObjectService::setFilter()
     */
    protected function getFilter()
    {
        return new UomSqlFilter();
    }

    protected function createValueObjectFromArray($data)
    {
        return Uom::createFromArray($data);
    }

    protected function createValueObjectFromSnapshot($snapshot)
    {
        return Uom::createFrom($snapshot);
    }

    public function generateCode()
    {
        $methodBuffer = '';

        $buffer = <<<PHP
<?php

namespace Application\Domain\Shared\Uom;

/**
 * This is a automatically generated file.
 *
PHPDOC
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 */
trait UomFactory
{
    /**
     *
     * @param string \$method
       @param string \$arguments
     * @return Uom
     *
     * @throws \InvalidArgumentException
     */
    public static function __callStatic(\$method, \$arguments)
    {
        return new Uom(\$method);
    }
}

PHP;

        foreach ($this->getValueCollecion() as $uom) {
            $methodBuffer .= sprintf(" * @method static Uom %s()\n", \strtoupper($uom->getUomName()));
        }

        $buffer = str_replace('PHPDOC', rtrim($methodBuffer), $buffer);
        $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));

        $file = $root . '/Domain/Shared/Uom/UomFactory.php';
        echo $file;

        file_put_contents($file, $buffer);
    }

    public function generateResFile()
    {
        $methodBuffer = '';

        $buffer = <<<PHP
<?php
return [
PHPDOC
];

PHP;

        foreach ($this->getValueCollecion() as $uom) {

            $v = sprintf("'uomCode'=>'%s',", $uom->getUomCode());
            $v = $v . sprintf("'uomName'=>'%s',", $uom->getUomName());
            $v = $v . sprintf("'symbol'=>'%s',", $uom->getSymbol());
            $methodBuffer .= sprintf("'%s'=>[%s],", $uom->getUomName(), $v);
        }

        $buffer = str_replace('PHPDOC', rtrim($methodBuffer), $buffer);
        $root = realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
        $file = $root . '/resources/uom_res.php';
        echo $file;

        file_put_contents($file, $buffer);
    }
}
