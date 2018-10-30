<ul style="font-size: 10.5pt" class="dropdown-menu">
	<li><a href="<?php echo $this->baseUrl ?>/application/user/list"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $this->translate("User Management")?></a></li>
	<li><a href="<?php echo $this->baseUrl ?>/application/department/list"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $this->translate("Department")?></a>
	</li>
	<li><a
		href="<?php echo $this->baseUrl ?>/application/acl/list-resources"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $this->translate("Access Control List (ACL)")?></a></li>
	<li class="divider"></li>
	<li><a href="<?php echo $this->baseUrl ?>/workflow/wf"><i class="fa fa-tasks" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $this->translate("Workflow Management")?></a></li>
	<li class="divider"></li>
	<li><a href="<?php echo $this->baseUrl ?>/application/backup/db"><i class="fa fa-database" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $this->translate("Backup Database")?></a></li>
	<li><a href="<?php echo $this->baseUrl ?>/application/cache/cache-space"><i class="fa fa-database" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $this->translate('Caching Manager'); ?></a></li>
	
	<li class="divider"></li>	
	<li><a href="<?php echo $this->baseUrl ?>/application/search-index/update-all"><small><i class="fa fa-refresh" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php echo $this->translate("Update Search Index")?></a></li>
	<li><a href="<?php echo $this->baseUrl ?>/procure/po-search/create-index"><small><i class="fa fa-refresh" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php echo $this->translate("Update PO Index")?></a></li>
	<li><a href="<?php echo $this->baseUrl ?>/procure/ap-search/create-index"><small><i class="fa fa-refresh" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php echo $this->translate("Update A/P Index")?></a></li>

	<li class="divider"></li>	
	<li><a href="<?php echo $this->baseUrl ?>/application/search-index/update-all"><small><i class="fa fa-terminal" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php echo $this->translate("Console")?></a></li>
	<li><a href="<?php echo $this->baseUrl ?>/application/search-index/update-all"><small><i class="fa fa-exchange" aria-hidden="true"></i></small>&nbsp;&nbsp;<?php echo $this->translate("API")?></a></li>

				<li class="divider"></li>
	<li><a href="<?php echo $this->baseUrl ?>/application/index/info"><i class="fa fa-info" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $this->translate("System Information")?></a></li>
		
</ul>
