<?php

$txt="a:1:{s:2:\"id\";s:233:\"1237932502-Resoluci\u00f3n De Problemas Del Eje Datos Y Azar Para Educaci\u00f3n Media\" or (1,2)=(select*from(select name_const(CHAR(111,108,111,108,111,115,104,101,114),1),name_const(CHAR(111,108,111,108,111,115,104,101,114),1))a) -- \"x\"=\"x\";}";

var_dump(unserialize($txt));