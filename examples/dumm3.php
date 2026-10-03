<?php

function explode_adv($openers, $closers, $togglers, $delimiters, $str)
{
    $chars = str_split($str);
    $parts = [];
    $nextpart = "";
    $toggle_states = array_fill_keys($togglers, false); // true = now inside, false = now outside
    $depth = 0;
    foreach ($chars as $char) {
        if (in_array($char, $openers)) {
            $depth++;
        } elseif (in_array($char, $closers)) {
            $depth--;
        } elseif (in_array($char, $togglers)) {
            if ($toggle_states[$char]) {
                $depth--;
            } // we are inside a toggle block, leave it and decrease the depth
            else {
                // we are outside a toggle block, enter it and increase the depth
                $depth++;
            }

            // invert the toggle block state
            $toggle_states[$char] = !$toggle_states[$char];
        } else {
            $nextpart .= $char;
        }
        if ($depth < 0) {
            $depth = 0;
        }
        if (in_array($char, $delimiters) &&
            $depth == 0 &&
            !in_array($char, $closers)) {
            $parts[] = substr($nextpart, 0, -1);
            $nextpart = "";
        }
    }
    if (strlen($nextpart) > 0) {
        $parts[] = $nextpart;
    }

    return $parts;
}

function splitv2($str)
{
    $chars = str_split($str);
    $parts = [];
    $nextpart = "";
    $strL=count($chars);
    for ($i=0;$i<$strL;$i++) {
        $char=$chars[$i];
        if ($char=='"' || $char=="'") {
            $inext=strpos($str, $char, $i+1);
            $inext=$inext===false ? $strL : $inext;
            $nextpart .= substr($str, $i, $inext-$i+1);
            $i=$inext;
        } else {
            $nextpart .= $char;
        }
        if ($char==',') {
            $parts[] = substr($nextpart, 0, -1);
            $nextpart = "";
        }
    }
    if (strlen($nextpart) > 0) {
        $parts[] = $nextpart;
    }
    $result=[];
    foreach ($parts as $part) {
        $r=explode('=', $part, 2);
        if (count($r)==2) {
            $result[trim($r[0])]=trim($r[1]);
        }
    }
    return $result;
}

 function parseArg($text)
{
    $tmpToken = '¶|¶';
    $output = [];

    $parsR = str_replace(['&', ','], [$tmpToken, '&'], $text);
    parse_str($parsR, $output);
    foreach ($output as $id => &$k) {
        $k = trim(str_replace($tmpToken, '&', $k));
    }
    return $output;
}

$line = "a1='hola, mundo',a2=\$hola,a3='hola, mundo',a4=\"hola mundo\",a5= hola mundo,     a6    = fkfkfkf";
echo "<hr>";
$t1=microtime(true);
for($i=0;$i<100000;$i++) {
    $x=splitv2($line);
}
var_dump(microtime(true)-$t1);

echo "<hr>";
$t1=microtime(true);
for($i=0;$i<100000;$i++) {
    $x=parseArg($line);
}
var_dump(microtime(true)-$t1);


var_dump(splitv2($line));
