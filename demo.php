<?php
include("p.php");
$p=new php_get_flash();
$p->set_url('http://v.ku6.com/special/show_6590679/bKvT6mD5yyLu8KrdlLgtcA...html');
print_r($p->doparse());
$p1=new php_get_flash();
$p1->set_url('http://www.tudou.com/listplay/5pti50RRCJo.html');
print_r($p1->doparse());
$p2=new php_get_flash();
$p2->set_url('http://v.youku.com/v_show/id_XNDk2ODE1NzA4.html');
print_r($p2->doparse());

