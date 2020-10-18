<?php
namespace Application\Domain\Shared\Uom\Collection;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Implement this to provide a list of currencies.
 *
 * @author Mathias Verraes
 */
final class UomCollection extends ArrayCollection
{

    private static $lazyCollection;

    protected $lazyCollectionReference;

    public function setLazyRowSnapshotCollectionReference($lazyRowSnapshotCollectionReference)
    {
        $this->lazyRowSnapshotCollectionReference = $lazyRowSnapshotCollectionReference;
    }

    /**
     *
     * @return NULL|ArrayCollection
     */
    public function getLazyRowSnapshotCollection()
    {
        $ref = $this->getLazyRowSnapshotCollectionReference();
        if ($ref == null) {
            return null;
        }

        $this->lazyRowSnapshotCollection = $ref();
        return $this->lazyRowSnapshotCollection;
    }
}
