<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 26-06-2016
 * Time: 12:37
 */

namespace eftec\bladeone;


trait BladeOneHtmlBootstrap
{
    use BladeOneHtml {
        BladeOneHtml::select as selectParent;
        BladeOneHtml::input as inputParent;
        BladeOneHtml::commandButton as commandButtonParent;
        BladeOneHtml::textArea as textAreaParent;
        BladeOneHtml::item as itemParent;
        BladeOneHtml::checkbox as checkboxParent;
        BladeOneHtml::compileEndCheckbox as compileEndCheckboxParent;
        BladeOneHtml::radio as radioParent;
        BladeOneHtml::compileEndRadio as compileEndRadioParent;

    }
    //<editor-fold desc="Override methods">
    public function select($name,$extra='') {
        $extra=$this->addClass($extra,'form-control');
        return $this->selectParent($name,$extra);
    }
    public function input($id,$value='',$type='text',$extra='') {
        $extra=$this->addClass($extra,'form-control');
        return $this->inputParent($id,$value,$type,$extra);
    }
    public function commandButton($id,$value='',$text='Button',$type='submit',$extra='') {
        $extra=$this->addClass($extra,'btn');
        return $this->commandButtonParent($id,$value,$text,$type,$extra);
    }
    public function textArea($id,$value='',$extra='') {
        $extra=$this->addClass($extra,'form-control');
        return $this->textAreaParent($id,$value,$extra);
    }
    public function item($type,$valueId, $valueText, $selectedItem='',$wrapper='', $extra='') {
        $id=@end($this->htmlCurrentId);
        $wrapper=($wrapper=='')?'%s':$wrapper;
        if (is_array($selectedItem)) {
            $found=in_array($valueId,$selectedItem);
        } else {
            $found = $valueId == $selectedItem;
        }
        switch ($type) {
            case 'select':
                $selected=($found)?'selected':'';
                return sprintf($wrapper,"<option value='{$valueId}' $selected ".
                    $this->convertArg($extra).">{$valueText}</option>\n");
                break;
            case 'radio':
                $selected=($found)?'checked':'';
                return sprintf($wrapper,"<label><input type='radio' id='".static::e($id)
                    ."' name='".static::e($id)."' value='{$valueId}' $selected "
                    .$this->convertArg($extra)."> {$valueText}</label>\n");
                break;
            case 'checkbox':
                $selected=($found)?'checked':'';
                return sprintf($wrapper,"<label><input type='checkbox' id='".static::e($id)
                    ."' name='".static::e($id)."' value='{$valueId}' $selected "
                    .$this->convertArg($extra)."> {$valueText}</label>\n");
                break;

            default:
                return '???? type undefined: [$type] on @item<br>';
        }
    }
    public function checkbox($id,$value='',$text='',$valueSelected='',$extra='') {
        $num=func_num_args();
        if ($num>2) {
            if ($value==$valueSelected) {
                if (is_array($extra)) {
                    $extra['checked'] = 'checked';
                } else {
                    $extra.=' checked="checked"';
                }
            }
            return '<div class="checkbox"><label>'.$this->inputParent($id, $value, 'checkbox', $extra) . ' ' . $text.'</label></div>';
        } else {
            array_push($this->htmlCurrentId,$id);
            return '<div class="checkbox">';
        }
    }
    public function radio($id,$value='',$text='',$valueSelected='',$extra='') {
        $num=func_num_args();
        if ($num>2) {
            if ($value==$valueSelected) {
                if (is_array($extra)) {
                    $extra['checked'] = 'checked';
                } else {
                    $extra.=' checked="checked"';
                }
            }
            return '<div class="radio"><label>'.$this->inputParent($id, $value, 'radio', $extra) . ' ' . $text.'</label></div>';
        } else {

            array_push($this->htmlCurrentId,$id);
            return '<div class="radio">';
        }
    }
    public function compileEndCheckbox() {
        $r=$this->compileEndCheckboxParent();
        $r.='</div>';
        return $r;
    }
    public function compileEndRadio() {
        $r=$this->compileEndRadioParent();
        $r.='</div>';
        return $r;
    }
    //</editor-fold>

    //<editor-fold desc="Misc members">

    /**
     * It adds a class to a html tag parameter
     * @example addClass('type="text" class="btn","btn-standard")
     * @param string|array $txt
     * @param string $newclass The class(es) to add, example "class1" or "class1 class"
     * @return string
     */
    function addClass($txt,$newclass) {
        if (is_array($txt)) {
            $txt=array_change_key_case($txt);
            @$txt['class'] = ' ' . $newclass;
            return $txt;
        }
        $p0=stripos(' '.$txt,' class');
        if ($p0===false) {
            // if the content of the tag doesn't contain a class then it adds one.
            return $txt.' class="'.$newclass.'"';
        }
        // the class tag exists so we found the closes character ' or " and we add the class (or classes) inside it
        // may be it could duplicates the tag.
        $p1=strpos($txt,"'",$p0);
        $p2=strpos($txt,'"',$p0);
        $p1=($p1===false)?99999:$p1;
        $p2=($p2===false)?99999:$p2;

        if ($p1<$p2) {
            return substr_replace($txt, $newclass.' ', $p1+1, 0);
        } else {
            echo $p2."#";
            return substr_replace($txt, $newclass.' ', $p2+1, 0);
        }
    }
    function separatesParam($txt) {
        $result=[];
        preg_match_all("~\"[^\"]++\"|'[^']++'|\([^)]++\)|[^,]++~", $txt,$result);
        return $result;
    }
    //</editor-fold>
}