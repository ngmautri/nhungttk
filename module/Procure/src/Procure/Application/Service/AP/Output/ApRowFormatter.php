<?php
namespace Procure\Application\Service\AP\Output;

use Procure\Application\Service\Output\Formatter\RowFormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\AccountPayable\APRowSnapshot;

/**
 * AP Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApRowFormatter extends RowFormatterDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Formatter\AbstractRowFormatter::format()
     */
    public function format(RowSnapshot $row)
    {
        if (! $row instanceof RowSnapshot) {
            return null;
        }

        $this->formatter->setLocale($this->getLocale());
        $row = $this->formatter->format($row);

        // then decorate
        if ($row instanceof APRowSnapshot) {
            // check later.
        }

        return $row;
    }
}
