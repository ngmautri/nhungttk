<ul style="font-size: 10.5pt" class="dropdown-menu">
   <li>
      <a href="/procure/ap/create">
         <i class="fa fa-plus" aria-hidden="true"></i>
         &nbsp;&nbsp;New A/P Invoice
      </a>
   </li>
   <li>
      <a href="/finance/v-invoice/add">
         <i class="fa fa-plus" aria-hidden="true"></i>
         &nbsp;&nbsp;New A/P Invoice from Contract/PO
      </a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/ap-report/header-status">
         <i class="fa fa-list" aria-hidden="true"></i>
         &nbsp;A/P Invoice List
      </a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/finance/je-row/list">
         <small>
            <span class="glyphicon glyphicon-triangle-right"></span>
         </small>&nbsp;<?php
        echo $this->translate("Journal Entry");
        ?></a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/finance/account/list">
         <small>
            <span class="glyphicon glyphicon-triangle-right"></span>
         </small>&nbsp;<?php

        echo $this->translate("Chart of Accounts");
        ?></a>
   </li>
   <li>
      <a href="/finance/posting-period/list">
         <small>
            <span class="glyphicon glyphicon-triangle-right"></span>
         </small>&nbsp;<?php

        echo $this->translate("Posting Period");
        ?></a>
   </li>
   <li>
      <a href="/finance/fx/list">
         <small>
            <span class="glyphicon glyphicon-triangle-right"></span>
         </small>&nbsp;<?php

        echo $this->translate("Exchange Rate");
        ?></a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/finance/activity-log/list">
         <i class="fa fa-caret-right" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<?php

        echo $this->translate("Log");
        ?></a>
   </li>
</ul>