<ul style="font-size: 10.5pt" class="dropdown-menu">

	<li><a href="<?php
echo $this->baseUrl?>/application/user/list"><i
			class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;<?php

echo $this->translate("User Management")?></a></li>
	<li><a href="<?php

echo $this->baseUrl?>/application/department/list2"><i
			class="fa fa-list" aria-hidden="true"></i>&nbsp;&nbsp;<?php

echo $this->translate("Department")?></a>
	</li>
   <li><a href="<?php

echo $this->baseUrl?>/application/account-chart/list"><i
         class="fa fa-list" aria-hidden="true"></i>&nbsp;&nbsp;<?php

        echo $this->translate("Chart of accounts")?></a>
   </li>

      </li>
   <li><a href="<?php

echo $this->baseUrl?>/application/item-attribute/list"><i
         class="fa fa-list" aria-hidden="true"></i>&nbsp;&nbsp;<?php

        echo $this->translate("Item Attributes")?></a>
   </li>

    <li><a href="<?php

    echo $this->baseUrl?>/application/brand/list"><i
         class="fa fa-list" aria-hidden="true"></i>&nbsp;&nbsp;<?php

        echo $this->translate("Brands")?></a>
   </li>

   <li class="divider"></li>
	<li><a
		href="<?php

echo $this->baseUrl?>/application/acl/list-resources"><i
			class="fa fa-key" aria-hidden="true"></i>&nbsp;&nbsp;<?php

echo $this->translate("Access Control List (ACL)")?></a></li>
  <li class="divider"></li>
    <li><a href="<?php

    echo $this->baseUrl?>/application/warehouse/list"><i
         class="fa fa-list" aria-hidden="true"></i>&nbsp;&nbsp;<?php

        echo $this->translate("Warehouse")?></a>
   </li>
	<li class="divider"></li>
	<li><a href="<?php

echo $this->baseUrl?>/workflow/wf"><i
			class="fa fa-tasks" aria-hidden="true"></i>&nbsp;&nbsp;<?php

echo $this->translate("Workflow Management")?></a></li>
	<li class="divider"></li>

	<li class="dropdown-submenu"><a class="test" tabindex="-1" href="#">Scheduler&nbsp;&nbsp;<i
			class="fa fa-caret-right" aria-hidden="true"></i></a>
		<ul class="dropdown-menu">
			<li><a tabindex="-1" href="#">2nd level dropdown</a></li>
			<li><a tabindex="-1" href="#">2nd level dropdown</a></li>
			<li class="dropdown-submenu"><a class="test" href="#">Another
					dropdown&nbsp;&nbsp;<i class="fa fa-caret-right" aria-hidden="true"></i>
			</a>
				<ul class="dropdown-menu">
					<li><a href="#">3rd level dropdown</a></li>
					<li><a href="#">3rd level dropdown</a></li>
				</ul></li>
		</ul></li>
	<li class="divider"></li>
	<li><a href="<?php

echo $this->baseUrl?>/application/backup/db"><i
			class="fa fa-database" aria-hidden="true"></i>&nbsp;&nbsp;<?php

echo $this->translate("Backup Database")?></a></li>
	<li><a
		href="<?php

echo $this->baseUrl?>/application/cache/clear"><i
			class="fa fa-trash-o" aria-hidden="true"></i>&nbsp;&nbsp;<?php

echo $this->translate('Clear cache');
?></a></li>

	<li class="divider"></li>
	<li><a
		href="<?php

echo $this->baseUrl?>/application/search-index/update-all"><small><i
				class="fa fa-refresh" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php

    echo $this->translate("Update Search Index")?></a></li>
	<li class="divider"></li>

	<li><a
		href="<?php

echo $this->baseUrl?>/inventory/item-search/create-index"><small><i
				class="fa fa-refresh" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php

    echo $this->translate("Update Item Index")?></a></li>

	<li><a
		href="<?php

echo $this->baseUrl?>/procure/po-search/create-index"><small><i
				class="fa fa-refresh" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php

    echo $this->translate("Update PO Index")?></a></li>
	<li><a
		href="<?php

echo $this->baseUrl?>/procure/ap-search/create-index"><small><i
				class="fa fa-refresh" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php

    echo $this->translate("Update A/P Index")?></a></li>

	<li class="divider"></li>
	<li><a
		href="<?php

echo $this->baseUrl?>/application/search-index/update-all"><small><i
				class="fa fa-terminal" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php

    echo $this->translate("Console")?></a></li>
	<li><a
		href="<?php

echo $this->baseUrl?>/application/search-index/update-all"><small><i
				class="fa fa-exchange" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php

    echo $this->translate("API")?></a></li>

	<li class="divider"></li>
	<li><a
		href="<?php

echo $this->baseUrl?>/application/index/check-attachment"><i
			class="fa fa-info" aria-hidden="true"></i>&nbsp;&nbsp;<?php

echo $this->translate("Check Attachment")?></a></li>

	<li class="divider"></li>
	<li><a href="<?php

echo $this->baseUrl?>/application/index/info"><i
			class="fa fa-info" aria-hidden="true"></i>&nbsp;&nbsp;<?php

echo $this->translate("System Information")?></a></li>

	<li class="divider"></li>



</ul>

<style>
.dropdown-submenu {
	position: relative;
}

.dropdown-submenu .dropdown-menu {
	top: 0;
	left: 100%;
	margin-top: -1px;
}
</style>

<script>
$(document).ready(function(){
  $('.dropdown-submenu a.test').on("click", function(e){
    $(this).next('ul').toggle();
    e.stopPropagation();
    e.preventDefault();
  });
});
</script>

