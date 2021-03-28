<?php
namespace Application\Domain\Util\Tree\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface TreeQueryRepositoryInterface
{

    public function getNodeById($nodeId);

    public function getRootNote($rootId);

    public function getNodeByName($nodeName);
}