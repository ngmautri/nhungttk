<?php
namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class ArticleCategoryMember
{

    public $id;

    public $article_id;

    public $article_cat_id;

    public $updated_by;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->article_id = (! empty($data['article_id'])) ? $data['article_id'] : null;
        $this->article_cat_id = (! empty($data['article_cat_id'])) ? $data['article_cat_id'] : null;
        $this->updated_by = (! empty($data['updated_by'])) ? $data['updated_by'] : null;
    }
}

