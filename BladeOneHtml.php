<?php
namespace eftec\bladeone;
/**
 * trait BladeOneHtml
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 * Extends the tags of the class BladeOne.  Its optional
 * It adds the next tags
 * <code>
 * select:
 * @ select('idCountry'[,$extra])
 * @ item('0','--select a country'[,$extra])
 * @ items($countries,'id','name',$currentCountry[,$extra])
 * @ endselect()
 * input:
 * @ input('iduser',$currentUser,'text'[,$extra])
 * button:
 * @ commandbutton('idbutton','value','text'[,$extra])
 *
 * </code>
 * Note. The names of the tags are based in Java Server Faces (JSF)
 * @package  BladeOneHtml
 * @version 1.4 2016-06-25
 * @link https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 */

trait BladeOneHtml
{
    var $htmlItem=array();
    var $htmlCurrentId=array();

    //<editor-fold desc="compile function">
    public function compileSelect($expression) {

        array_push($this->htmlItem,'select');
        return $this->phpTag."echo \$this->select{$expression}; ?>";
    }
    public function compileSelectGroup($expression) {

        array_push($this->htmlItem,'selectgroup');
        $this->compilePush('');
        return $this->phpTag."echo \$this->select{$expression}; ?>";
    }
    public function compileRadio($expression) {
        array_push($this->htmlItem,'radio');
        return $this->phpTag."echo \$this->radio{$expression}; ?>";
    }
    public function compileCheckbox($expression) {
        array_push($this->htmlItem,'checkbox');
        return $this->phpTag."echo \$this->checkbox{$expression}; ?>";
    }

    public function compileEndSelect() {
        $r=@array_pop($this->htmlItem);
        if (is_null($r)) {
            $this->showError("@endselect","Missing @select or so many @endselect",true);
        }
        return $this->phpTag."echo '</select>'; ?>";
    }
    public function compileEndRadio() {
        $r=@array_pop($this->htmlItem);
        if (is_null($r)) {
            return $this->showError("@EndRadio","Missing @Radio or so many @EndRadio",true);
        }
        return '';
    }
    public function compileEndCheckbox() {
        $r=@array_pop($this->htmlItem);
        if (is_null($r)) {
            return $this->showError("@EndCheckbox","Missing @Checkbox or so many @EndCheckbox",true);
        }
        return '';
    }
    public function compileItem($expression) {
        // we add a new attribute with the type of the current open tag
        $r=end($this->htmlItem);
        $x=trim($expression);
        $x="('{$r}',".substr($x,1);
        return $this->phpTag."echo \$this->item{$x}; ?>";
    }
    public function compileItems($expression) {
        // we add a new attribute with the type of the current open tag
        $r=end($this->htmlItem);
        $x=trim($expression);
        $x="('{$r}',".substr($x,1);
        return $this->phpTag."echo \$this->items{$x}; ?>";
    }
    public function compileTrio($expression) {
        // we add a new attribute with the type of the current open tag
        $r=end($this->htmlItem);
        $x=trim($expression);
        $x="('{$r}',".substr($x,1);
        return $this->phpTag."echo \$this->trio{$x}; ?>";
    }
    public function compileTrios($expression) {
        // we add a new attribute with the type of the current open tag
        $r=end($this->htmlItem);
        $x=trim($expression);
        $x="('{$r}',".substr($x,1);
        return $this->phpTag."echo \$this->trios{$x}; ?>";
    }
    public function compileInput($expression) {
        return $this->phpTag."echo \$this->input{$expression}; ?>";
    }
    public function compileTextArea($expression) {
        return $this->phpTag."echo \$this->textArea{$expression}; ?>";
    }
    public function compileHidden($expression) {
        return $this->phpTag."echo \$this->hidden{$expression}; ?>";
    }
    public function compileLabel($expression) {
        return $this->phpTag."echo \$this->label{$expression}; ?>";
    }
    public function compileCommandButton($expression) {
        return $this->phpTag."echo \$this->commandButton{$expression}; ?>";
    }
    public function compileForm($expression) {
        return $this->phpTag."echo \$this->form{$expression}; ?>";
    }
    public function compileEndForm() {
        return $this->phpTag."echo '</form>'; ?>";
    }
    //</editor-fold>

    //<editor-fold desc="used function">
    public function select($name,$extra='') {
        return "<select id='".static::e($name)."' name='".static::e($name)."' {$this->convertArg($extra)}>\n";
    }
    public function selectGroup($name,$extra='') {
        return $this->selectGroup($name,$extra);
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
            return $this->input($id, $value, 'radio', $extra) . ' ' . $text;
        } else {
            array_push($this->htmlCurrentId,$id);
            return '';
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
            return $this->input($id, $value, 'checkbox', $extra) . ' ' . $text;
        } else {
            array_push($this->htmlCurrentId,$id);
            return '';
        }
    }

    /**
     * @param string $type type of the current open tag
     * @param string $fieldId Field of the id
     * @param string $fieldText Field of the value visible
     * @param array|string $selectedItem Item selected (optional)
     * @param string $wrapper Wrapper of the element.  For example, <li>%s</li>
     * @param string $extra
     * @return string
     */
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
                    $this->convertArg($extra).">{$valueText}</option>");
                break;
            case 'radio':
                $selected=($found)?'checked':'';
                return sprintf($wrapper,"<input type='radio' id='".static::e($id)
                    ."' name='".static::e($id)."' value='{$valueId}' $selected "
                    .$this->convertArg($extra)."> {$valueText}");
                break;
            case 'checkbox':
                $selected=($found)?'checked':'';
                return sprintf($wrapper,"<input type='checkbox' id='".static::e($id)
                    ."' name='".static::e($id)."' value='{$valueId}' $selected "
                    .$this->convertArg($extra)."> {$valueText}");
                break;

            default:
                return '???? type undefined: [$type] on @item<br>';
        }
    }



    /**
     * @param string $type type of the current open tag
     * @param array $arrValues Array of objects/arrays to show.
     * @param string $fieldId Field of the id
     * @param string $fieldText Field of the value visible
     * @param array|string $selectedItem Item selected (optional)
     * @param string $wrapper Wrapper of the element.  For example, <li>%s</li>
     * @param string $extra (optional) is used for add additional information for the html object (such as class)
     * @return string
     * @version 1.0
     */
    public function items($type, $arrValues, $fieldId, $fieldText, $selectedItem='', $wrapper='', $extra='') {
        if (count($arrValues)==0) {
            return "";
        }
        if (is_object($arrValues[0])) {
            $arrValues=(array) $arrValues;
        }
        $result='';
        foreach($arrValues as $v) {
            if (is_object($v)) {
                $v=(array)$v;
            }
            $result.=$this->item($type,$v[$fieldId],$v[$fieldText],$selectedItem,$wrapper,$extra);
        }
        return $result;
    }

    /**
     * @param string $type type of the current open tag
     * @param string $valueId value of the trio
     * @param string $valueText visible value of the trio.
     * @param string $value3 extra third value for select value or visual
     * @param array|string $selectedItem Item selected (optional)
     * @param string $wrapper Wrapper of the element.  For example, <li>%s</li>
     * @param string $extra
     * @return string
     * @internal param string $fieldId Field of the id
     * @internal param string $fieldText Field of the value visible
     */
    public function trio($type,$valueId, $valueText,$value3='', $selectedItem='',$wrapper='', $extra='') {
        $id=@end($this->htmlCurrentId);
        $wrapper=($wrapper=='')?'%s':$wrapper;
        if (is_array($selectedItem)) {
            $found=in_array($valueId,$selectedItem);
        } else {
            $found = $valueId == $selectedItem;
        }
        switch ($type) {
            case 'selectgroup':
                $selected=($found)?'selected':'';
                return sprintf($wrapper,"<option value='{$valueId}' $selected ".
                    $this->convertArg($extra).">{$valueText}</option>");
                break;
            default:
                return '???? type undefined: [$type] on @item<br>';
        }
    }

    /**
     * @param string $type type of the current open tag
     * @param array $arrValues Array of objects/arrays to show.
     * @param string $fieldId Field of the id
     * @param string $fieldText Field of the value visible
     * @param string $fieldThird
     * @param array|string $selectedItem Item selected (optional)
     * @param string $wrapper Wrapper of the element.  For example, <li>%s</li>
     * @param string $extra (optional) is used for add additional information for the html object (such as class)
     * @return string
     * @version 1.0
     */
    public function trios($type, $arrValues, $fieldId, $fieldText,$fieldThird, $selectedItem='', $wrapper='', $extra='') {
        if (count($arrValues)==0) {
            return "";
        }
        if (is_object($arrValues[0])) {
            $arrValues=(array) $arrValues;
        }
        $result='';
        $oldV3="";
        foreach($arrValues as $v) {
            if (is_object($v)) {
                $v=(array)$v;
            }
            $v3=$v[$fieldThird];
            if ($type=='selectgroup') {
                if ($v3!=$oldV3) {
                    if ($oldV3!="") {
                        $result.="</optgroup>";
                    }
                    $oldV3=$v3;
                    $result.="<optgroup label='{$v3}'>";
                }
            }
            if ($result) {
                $result .= $this->trio($type, $v[$fieldId], $v[$fieldText], $v3, $selectedItem, $wrapper, $extra);
            }
        }
        if ($type=='selectgroup' && $oldV3!="") {
            $result.="</optgroup>";
        }
        return $result;
    }
    public function input($id,$value='',$type='text',$extra='')
    {
        return "<input id='".static::e($id)."' name='".static::e($id)."' type='".$type."' ".$this->convertArg($extra)." value='".static::e($value)."' />\n";
    }
    public function textArea($id,$value='',$extra='')
    {
        $value=str_replace('\n',"\n",$value);
        return "<textarea id='".static::e($id)."' name='".static::e($id)."' ".$this->convertArg($extra)." >$value</textarea>\n";
    }
    public function hidden($id,$value='',$extra='')
    {
        return $this->input($id,$value,'hidden',$extra);
    }
    public function label($id,$value='',$extra='')
    {
        return "<label for='{$id}' {$this->convertArg($extra)}>{$value}</label>";
    }
    public function commandButton($id,$value='',$text='Button',$type='submit',$extra='')
    {
        return "<button type='{$type}' id='".static::e($id)."' name='".static::e($id)."' value='".static::e($value)."' {$this->convertArg($extra)}>{$text}</button>\n";
    }
    public function form($action,$method='post',$extra='') {
        return "<form $action='{$action}' method='{$method}' {$this->convertArg($extra)}>";
    }

    //</editor-fold>
}