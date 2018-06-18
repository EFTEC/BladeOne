<?php


namespace eftec\bladeone;

/**
 * Trait BladeOneLang
 * It adds the next tags
 * <code>
 * select:
 * @ _e('hello')
 * @ _n('Product','Products',$n)
 * @ _ef('hello %s',$user)
 * </code>
 * @package eftec\bladeone
 * @version 1.0 2017-12-17 (1)
 * @link https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 * @copyright  2017 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 */
trait BladeOneLang
{
    var $missingLog=''; // if empty then every missing key is not saved.

    static public $dictionary=array(); // static is for a small optimization.

    //<editor-fold desc="compile">
    function compile_e($expression){
        return $this->phpTag . "echo \$this->_e{$expression}; ?>";
    }
    function compile_ef($expression){
        return $this->phpTag . "echo \$this->_ef{$expression}; ?>";
    }
    function compile_n($expression){
        return $this->phpTag . "echo \$this->_n{$expression}; ?>";
    }
    //</editor-fold>

    function _e($phrase){
        if ((!array_key_exists($phrase, BladeOneLang::$dictionary))) {
            $this->missingTranslation($phrase);
            return $phrase;
        } else {
            return BladeOneLang::$dictionary[$phrase];
        }
    }
    function _ef($phrase){
        $argv = func_get_args();
        $r=$this->_e($phrase);
        $argv[0]=$r; // replace the first argument with the translation.
        $result=@call_user_func_array("sprintf", $argv);
        $result=($result===false)?$r:$result;
        return $result;
    }
    /**
     * if num is more than one then it returns the phrase in plural, otherwise the phrase in singular.
     * Note: the translation should be as follow: $msg['Person']='Person' $msg=['Person']['p']='People'
     * @param string $phrase
     * @param string $phrases
     * @param int $num
     * @return string
     */
    function _n($phrase,$phrases,$num=0){
        if ((!array_key_exists($phrase,BladeOneLang::$dictionary))) {
            $this->missingTranslation($phrase);
            return ($num<=1)?$phrase:$phrases;
        } else {
            return ($num<=1)?$this->_e($phrase):$this->_e($phrases);
        }
    }

    private function missingTranslation($txt) {
        if (!$this->missingLog) return; // if there is not a file assigned then it skips saving.
        $fz=@filesize($this->missingLog);
        if (is_object($txt) || is_array($txt)) {
            $txtW=print_r($txt,true);
        } else {
            $txtW=$txt;
        }
        if ($fz>100000) {
            // mas de 100kb = reducirlo a cero.
            $fp = fopen($this->missingLog, 'w');
        } else {
            $fp = fopen($this->missingLog, 'a');
        }
        fwrite($fp, $txtW."\n");
        fclose($fp);
    }


}