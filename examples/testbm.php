<?php

class ExampleCode {

    public $value=null;
    public static $singleton=null;

    public static function getSingleton() {
        if (self::$singleton===null) {
            self::$singleton=new ExampleCode();
        }
        return self::$singleton;
    }
    
    public static function Keep($value) {
        $single=self::getSingleton();
        return $single;
    }
    public function show() {
        
        echo "value is ".$this->value;
    }
}

ExampleCode::Keep("hello")->show();


die(1);

$text='@primero   @@ignorar           @segundo(2arg)  @tercer("3arg)3argf")  @cuarto(4arg,b,c) @quinto::ff("5arg") eso no va incluido no incluido no incluido';
$text.=str_pad($text, 500, $text.'----------------');

$expr='/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x';
    
$num=50000;

$t1=microtime(true);
for ($i=0;$i<$num;$i++) {
    $arrPreg = [];
    preg_match_all($expr, $text, $arrPreg, PREG_OFFSET_CAPTURE);
}
$t2=microtime(true);

echo "<h1>preg: ".($t2-$t1)."</h1>";


$t1=microtime(true);
$p0=-1;
for ($i=0;$i<$num;$i++) {
    $arr = [];
    $positions=[];
    while (true) {
        $p0 = strpos($text, '@', $p0+1);
        if ($p0===false) {
            break;
        }
        $p1s = strpos($text, ' ', $p0+1);
        $p1 = strpos($text, ')', $p0+1);
        $positions[]=token_get_all('<?php'.substr($text, $p0+1, min($p1s, $p1)-$p0).';?>');
    }
}
$t2=microtime(true);



echo "<h1>for: ".($t2-$t1)."</h1>";
echo "<pre>";
var_dump($positions);
echo "</pre>";

echo "<hr>";
echo "<pre>";
var_dump($arrPreg);

echo "</pre>";
