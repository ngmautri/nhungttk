select
*
from
(
select
	mla_articles_categories.*,
	ifnull(mla_articles_categories_members.totalMembers,0) as totalMembers,
    ifnull(mla_articles_categories_1.totalChildren,0) as totalChildren
	from mla_articles_categories
	left join
	(
	select 
		mla_articles_categories_members.article_cat_id,
		count(*) as totalMembers
		from mla_articles_categories_members
		group by mla_articles_categories_members.article_cat_id  
	) 
	as mla_articles_categories_members
	on mla_articles_categories.id =  mla_articles_categories_members.article_cat_id
    left join
    (
		select
		mla_articles_categories.parent_id as article_cat_id,
		count(*) as totalChildren
		from mla_articles_categories
		group by mla_articles_categories.parent_id
	) 
    as mla_articles_categories_1
    on mla_articles_categories_1.article_cat_id = mla_articles_categories.id
) 
as mla_articles_categories
WHERE 1
