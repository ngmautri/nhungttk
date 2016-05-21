update
(
   mla_purchase_cart   
) 
set  mla_purchase_cart.status  = "ORDERED"
where 1
and mla_purchase_cart.status is null
and mla_purchase_cart.created_by = 39

