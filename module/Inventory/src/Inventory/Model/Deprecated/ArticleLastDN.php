<?php
namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class ArticleLastDN
{

    public $id;

    public $article_id;

    public $last_workflow_id;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->article_id = (! empty($data['article_id'])) ? $data['article_id'] : null;
        $this->last_workflow_id = (! empty($data['last_workflow_id'])) ? $data['last_workflow_id'] : null;
    }
}

