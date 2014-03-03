<?php

require "../../vendor/oyejorge/less.php/lessc.inc.php";

$less = new Less_Parser();
$less->parseFile('../less/tactilecrm.less');

header("Content-Type: text/css");
echo $less->getCss();
