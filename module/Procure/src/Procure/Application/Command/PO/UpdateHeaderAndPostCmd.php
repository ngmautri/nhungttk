<?php
namespace Procure\Application\Command\PO;

use Application\Application\Command\AbstractDoctrineCompositeCmd;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UpdateHeaderAndPostCmd extends AbstractDoctrineCompositeCmd
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CompositeCommand::execute()
     */
    public function execute()
    {
        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {
            parent::execute();
        } catch (\Exception $e) {
            $this->getDoctrineEM()
                ->getConnection()
                ->rollBack();
        }
    }
}
