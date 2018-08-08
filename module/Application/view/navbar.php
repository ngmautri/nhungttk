<?php // $current_module from the template; ?> 


<?php if ($this->user !== null): ?>

<!-- INBOX MENU -->
<!-- 
<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"
	href="#" style="padding-left: 20px; padding-right: 10px;">My Inbox <span
		class="caret"></span></a>
							<?php include (ROOT.'/module/Application/view/inbox_menu.php'); ?>
							</li>
 -->
 
<!-- FINANCE MENU -->
<li
	class="dropdown <?php if ($current_module=="PAYMENT"): echo "active";endif;?>"><a
	class="dropdown-toggle" data-toggle="dropdown" href="#"
	style="padding-left: 10px; padding-right: 10px;"><?php echo $this->translate("Cash");?> <span
		class="caret"></span></a>
							<?php include (ROOT.'/module/Application/view/payment_menu.php'); ?>
						</li>

<!-- FINANCE MENU -->
<li
	class="dropdown <?php if ($current_module=="FINANCE"): echo "active";endif;?>"><a
	class="dropdown-toggle" data-toggle="dropdown" href="#"
	style="padding-left: 10px; padding-right: 10px;"><?php echo $this->translate("Finance");?> <span
		class="caret"></span></a>
							<?php include (ROOT.'/module/Application/view/finance_menu.php'); ?>
						</li>

<!-- INVENTORY MENU -->
<li
	class="dropdown <?php if ($current_module=="INVENTORY"): echo "active";endif;?>"><a
	class="dropdown-toggle" data-toggle="dropdown" href="#"
	style="padding-left: 10px; padding-right: 10px;"><?php echo $this->translate("Inventory")?> <span
		class="caret"></span></a>
							<?php include (ROOT.'/module/Application/view/inventory_menu.php'); ?>			
						</li>

<!-- Production MENU -->
<li
	class="dropdown <?php if ($current_module=="PRODUCTION"): echo "active";endif;?>"><a
	class="dropdown-toggle" data-toggle="dropdown" href="#"
	style="padding-left: 10px; padding-right: 10px;"><?php echo $this->translate("Production");?> <span class="caret"></span></a>
							<?php include (ROOT.'/module/Application/view/production_menu.php'); ?>
						  </li>

<!-- BP MENU -->
<li
	class="dropdown <?php if ($current_module=="BP"): echo "active";endif;?>"
	style="padding: 0px; margin: 0px;"><a class="dropdown-toggle"
	data-toggle="dropdown" href="#"
	style="padding-left: 10px; padding-right: 10px;"><?php echo $this->translate("Partner")?> <span
		class="caret"></span></a>
							<?php include (ROOT.'/module/Application/view/bp_menu.php'); ?>
						</li>

<!-- PROCURE MENU -->
<?php if ($this->cart_items !== null): ?>

<li
	class="dropdown <?php if ($current_module=="PROCURE"): echo "active";endif;?>"><a
	class="dropdown-toggle" data-toggle="dropdown" href="#"
	style="padding-left: 10px; padding-right: 10px;">
						    
						    <?php if ($this->cart_items >0): ?>
						 	   <span id="cart_items" class="badge">
						    <?php
            
echo $this->cart_items;
        else :
            ?>
	  					   		<span id="cart_items" class="">
						    <?php endif;?>						    
						    
							    </span>&nbsp;<?php echo $this->translate("Procure");?> <span
			class="caret"></span>

</a>			    
						    	<?php include (ROOT.'/module/Application/view/procure_menu.php'); ?>
							</li>
<?php endif;?>



<!-- HR MENU -->
<li
	class="dropdown <?php if ($current_module=="HR"): echo "active";endif;?>"><a
	class="dropdown-toggle" data-toggle="dropdown" href="#"
	style="padding-left: 10px; padding-right: 10px;">HR <span class="caret"></span></a>
							<?php include (ROOT.'/module/Application/view/hr_menu.php'); ?>
						  </li>


<!-- OTHER MENU -->
<li
	class="dropdown <?php if ($current_module=="OTHER"): echo "active";endif;?>"><a
	class="dropdown-toggle" data-toggle="dropdown" href="#"
	style="padding-left: 10px; padding-right: 10px;"><?php echo $this->translate("Other");?> <span
		class="caret"></span></a>
						  <?php include (ROOT.'/module/Application/view/other_menu.php'); ?>
						  </li>

<!-- SETTING MENU -->
<?php if ($this->isAdmin==true): ?>

<li
	class="dropdown <?php if ($current_module=="Application"): echo "active";endif;?>"><a
	class="dropdown-toggle" data-toggle="dropdown" href="#"
	style="padding-left: 10px; padding-right: 10px;"> <span
		class="label label-success"> <small> <span
				class="glyphicon glyphicon-wrench"> </span></small>
	</span>&nbsp;&nbsp;<?php echo $this->translate("Setup");?> <span
		class="caret"></span></a>
								  <?php include (ROOT.'/module/Application/view/setting_menu.php'); ?>
				 			</li>
<?php endif;?>
			
						  	<?php if ($this->isProcurement==true): ?>
<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"
	href="#"><span class="label label-default"></span> PROCUREMENT <span
		class="caret"></span></a>
	<ul class="dropdown-menu">
		<li><a href="<?php echo $this->baseUrl ?>/procurement/pr/all-pr">All
				PR</a></li>
		<li><a href="<?php echo $this->baseUrl ?>/procurement/pr/pr-items">All
				PR Items</a></li>
		<li class="divider"></li>

		<li><a href="<?php echo $this->baseUrl ?>/inventory/article/all">Show
				all items</a></li>

		<li class="divider"></li>

		<li><a href="<?php echo $this->baseUrl ?>/procurement/vendor/list">Show
				all vendors </a></li>

	</ul></li>
<?php endif;?>

<?php if ($this->currentLocale !== null): ?>
<!-- LOCALE MENU -->
<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"
	href="#" style="padding-left: 10px; padding-right: 10px;"> <!-- <i class="fa fa-globe fa-lg" aria-hidden="true"></i>  -->
	<?php
        
        switch ($this->currentLocale) {
            
            case 'en_US':
                echo '<img alt="" src="/images/flag/flag_uk.png">&nbsp;EN';
                ;
                break;
            case 'vi_VN':
                echo '<img alt="" src="/images/flag/flag_vn.png">&nbsp;VN';
                break;
            case 'lo_LA':
                echo '<img alt="" src="/images/flag/flag_la.png">&nbsp;LA';
                break;
            case 'de_DE':
                echo '<img alt="" src="/images/flag/flag_germany.png">&nbsp;DE';
        }
        
        ?>
	<span class="caret"></span></a>
							 <?php include (ROOT.'/module/Application/view/locale_menu.php'); ?>
						</li>
<?php endif;?>
<!-- PROFIL MENU -->
<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"
	href="#" style="padding-left: 10px; padding-right: 10px;"><span
		class="label label-primary"> <small><span
				class="glyphicon glyphicon-user"> </span></small>
	</span>&nbsp;&nbsp;<?php echo $this->user?> <span class="caret"></span></a>
							 <?php include (ROOT.'/module/Application/view/profil_menu.php'); ?>
						</li>

<?php endif;?>
<li id="clock1"
	style="color: #FFFFCC; padding: 6px 4px 2px 15px; font-size: 10pt;"></li>
