<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_SLOW);



//<editor-fold desc="Example data">
$name="New User";
$records=array(1,2,3);
$users=array();
$usr=new stdClass();
$usr->id=1;
$usr->name="John Doe";
$usr->type=1;
$usr->number=1;
$users[]=$usr;
$usr=new stdClass();
$usr->id=2;
$usr->name="Anna Smith";
$usr->type=2;
$usr->number=5;
$users[]=$usr;

$drinks7=array('Cocacola','Pepsi','Fanta','Sprite','7up');
$drinks8=array('Cocacola','Pepsi','Fanta','Sprite','7up','Bilz&Pap');
//</editor-fold>

$blade->directiveRT("lamb",function($mary='Empty Mary') { echo "$mary had a little lamb. Its fleece was white as snow.";});
$blade->directiveRT("calculator",function($n1,$n2) { echo $n1+$n2;});

$blade->directive('datetime', function ($expression) {
    return "<?php echo ($expression)->format('m/d/Y H:i'); ?>";
});

$blade->directiveRT('datetimert', function ($expression) {
    echo $expression->format('m/d/Y H:i');
});


try {
    echo $blade->run("Test2.directive", ['mary'=>'Mary Sue','now'=>new DateTime()]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
