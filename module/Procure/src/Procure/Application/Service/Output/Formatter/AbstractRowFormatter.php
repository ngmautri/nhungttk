<?php
namespace Procure\Application\Service\Output\Formatter;

use Procure\Domain\RowSnapshot;

/**
 * Row Output
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractRowFormatter
{

    protected $locale;

    abstract public function format(RowSnapshot $row);

    /**
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
