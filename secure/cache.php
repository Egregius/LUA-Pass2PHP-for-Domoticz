<?php
if(isset($_REQUEST['fetch']))echo apcu_fetch($_REQUEST['fetch']);
elseif(isset($_REQUEST['store'])&&isset($_REQUEST['value']))apcu_store($_REQUEST['store'],$_REQUEST['value']);
