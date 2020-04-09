<?php
namespace Application\Application\Service\Document;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface DocumentBuilderInterface
{

    public function buildHeader($params = null);

    public function buildBody($params = null);

    public function buildFooter($params = null);
    
    public function getDocument();
}
