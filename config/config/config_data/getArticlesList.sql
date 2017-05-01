select
*
from 
(
select
	*,
	(mla_articles.total_inflow-mla_articles.total_outflow) as article_balance
from
(
	select 
		mla_articles.*,
		ifnull(article_total_inflow.total_inflow,0) as total_inflow,
		ifnull(article_total_outflow.total_outflow,0) as total_outflow
	from mla_articles

	/*total infow*/
	left join
	(
		select 
		mla_articles_movements.article_id,
		ifnull(sum(mla_articles_movements.quantity),0) as total_inflow
		from mla_articles_movements
		where mla_articles_movements.flow = 'IN'
		group by article_id
	)
	as article_total_inflow
	on article_total_inflow.article_id = mla_articles.id

	/*total outflow*/
	left join
	(
		select 
		mla_articles_movements.article_id,
		ifnull(sum(mla_articles_movements.quantity),0)as total_outflow
		from mla_articles_movements
		where mla_articles_movements.flow = 'OUT'
		group by article_id
	)
	as article_total_outflow
	on article_total_outflow.article_id = mla_articles.id
)
as mla_articles

join
(
	
    /**USER-DEPARTMENT beginns*/
    select 
        mla_users.title, 
        mla_users.firstname, 
        mla_users.lastname, 
        mla_departments_members_1.*
    from mla_users
    join 
	(	select 
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
		join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1 
    on mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/
)
as mla_users
on mla_users.user_id = mla_articles.created_by
)
as mla_articles
WHERE 1
and id = 14