<ul style="font-size: 10pt" class="dropdown-menu">
   <li>
      <a href="/procure/pr/create">
         <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<?php
        echo $this->translate('New Purchase Request (PR)');
        ?></a>
   </li>
   <li>
      <a href="/procure/pr-report/header-status">
         <i class="fa fa-caret-right" aria-hidden="true"></i>
         &nbsp;&nbsp;&nbsp;PR list
      </a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/qr/create">
         <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<?php

        echo $this->translate("New Quotation");
        ?>(QO)</a>
   </li>
   <li>
      <a href="/procure/qr/list">
         <i class="fa fa-caret-right" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<?php

        echo $this->translate("Quotation List");
        ?></a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/po/create">
         <i class="fa fa-plus" aria-hidden="true"></i>
         &nbsp;New Contract (PO)
      </a>
   </li>
   <li>
      <a href="/procure/po-report/header-status">
         <i class="fa fa-caret-right" aria-hidden="true"></i>
         &nbsp;&nbsp;&nbsp;PO /Contract list
      </a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/gr/create">
         <i class="fa fa-plus" aria-hidden="true"></i>
         &nbsp;New Goods Receipt (GR)
         <i class="fa fa-truck" aria-hidden="true"></i>
      </a>
   </li>
   <li>
      <a href="/procure/gr/list">
         <i class="fa fa-caret-right" aria-hidden="true"></i>
         &nbsp;&nbsp;&nbsp;GR list
      </a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/ap/create">
         <i class="fa fa-plus" aria-hidden="true"></i>
         &nbsp;&nbsp;New A/P Invoice (AP)
      </a>
   </li>
   <li>
      <a href="/procure/ap-report/header-status">
         <i class="fa fa-list" aria-hidden="true"></i>
         &nbsp;A/P Invoice List
      </a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/return/add">
         <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<?php

        echo $this->translate("Goods Return");
        ?> (RE)</a>
   </li>
   <li>
      <a href="/procure/return/list">
         <i class="fa fa-caret-right" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<?php

        echo $this->translate("Return list");
        ?></a>
   </li>
   <li class="divider"></li>
     <li>
      <a href="/procure/report/index">
         <i class="fa fa-book" aria-hidden="true"></i>&nbsp;<?php

        echo $this->translate('Reporting');
        ?></a>
   </li>
   <li>
      <a href="/procure/activity-log/list">
         <i class="fa fa-caret-right" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<?php

        echo $this->translate("Log");
        ?></a>
   </li>
</ul>