select
* 
from mla_users
where mla_users.id Not in
(
	select
		mla_users.id
	from mla_acl_user_role 
	left join mla_users
	on mla_users.id = mla_acl_user_role.user_id
	where 1
	and mla_acl_user_role.role_id = 1
)