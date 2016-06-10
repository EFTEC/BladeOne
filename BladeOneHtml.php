<?php

/**
 * Class BladeOneHtml
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 * Extends the tags of the class BladeOne.  Its optional
 * It adds the next tags
 * <code>
 * select:
 * @ selectonemenu('idCountry'[,$extra])
 * @ selectitem('0','--select a country'[,$extra])
 * @ selectitems($countries,'id','name',$currentCountry[,$extra])
 * @ endselectonemenu()
 * input:
 * @ input('iduser',$currentUser,'text'[,$extra])
 * button:
 * @ commandbutton('idbutton','value','text'[,$extra])
 *
 * </code>
 * Note. The names of the tags are based in Java Server Faces (JSF)
 * @package  BladeOneHtml
 * @version 1.2 2016-06-10
 * @link https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 */

namespace eftec\bladeone;

class BladeOneHtml extends BladeOne
{
    //<editor-fold desc="compile function">
    public function compileSelectOneMenu($expression) {
        return $this->phpTag."echo \$this->selectOneMenu{$expression}; ?>";
    }
    public function compileEndSelectOneMenu($expression) {
        return $this->phpTag."echo \$this->endSelectOneMenu{$expression}; ?>";
    }
    public function compileSelectItem($expression) {
        return $this->phpTag."echo \$this->selectItem{$expression}; ?>";
    }
    public function compileSelectItems($expression) {
        return $this->phpTag."echo \$this->selectItems{$expression}; ?>";
    }
    public function compileInput($expression) {
        return $this->phpTag."echo \$this->input{$expression}; ?>";
    }
    public function compileCommandButton($expression) {
        return $this->phpTag."echo \$this->commandButton{$expression}; ?>";
    }
    //</editor-fold>

    //<editor-fold desc="used function">
    public function selectOneMenu($name,$extra='') {
        return "<select id='{$name}' name='{$name}' {$this->convertArg($extra)}>\n";
    }
    public function endSelectOneMenu() {
        return "</select>";
    }
    public function selectItem($id,$text,$extra='') {
        return "<option value='{$id}' {$this->convertArg($extra)}>{$text}</option>";
    }

    /**
     * @param $array [] Array of objects or other array
     * @param $id string Field of the id
     * @param $text string Field of the value visible
     * @param string $selectedItem Item selected (optional)
     * @param string $extra (optional) is used for add additional information for the html object (such as class)
     * @version 1.0
     * @return string
     */
    public function selectItems($array,$id,$text,$selectedItem='',$extra='') {
        if (count($array)==0) {
            return "";
        }
        $t=is_object($array[0]);
        $result='';
        if ($t) {
            foreach($array as $v) {
                $selected=($v->{$id}==$selectedItem)?'selected':'';
                $result.="<option value='".$v->{$id}."' $selected {$this->convertArg($extra)}>".$v->{$text}."</option>\n";
            }
        } else {
            foreach($array as $v) {
                $selected=($v[$id]==$selectedItem)?'selected':'';
                $result.="<option value='".$v[$id]."' $selected {$this->convertArg($extra)}>".$v[$text]."</option>\n";
            }
        }
        return $result;
    }

    public function input($id,$value='',$type='text',$extra='')
    {
        return "<input id='{$id}' name='{$id}' type='{$type}' {$this->convertArg($extra)} value='{$value}' />\n";
    }
    public function commandButton($id,$value='',$text='Button',$extra='')
    {
        return "<button type='submit' id='{$id}' name='{$id}' value='{value}' {$this->convertArg($extra)}>{$text}</button>\n";
    }
    //</editor-fold>
}