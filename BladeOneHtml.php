<?php
namespace eftec\bladeone;
/**
 * trait BladeOneHtml
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 * Extends the tags of the class BladeOne.  Its optional
 * It adds the next tags
 * <code>
 * select:
 * @ select('idCountry','value',[,$extra])
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
 * @version 1.9.1 2018-06-11 (1)
 * @link https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 */
trait BladeOneHtml
{
    var $htmlItem=array(); // indicates the type of the current tag. such as select/selectgroup/etc.
    var $htmlCurrentId=array(); //indicates the id of the current tag.

    //<editor-fold desc="compile function">
    protected function compileSelect($expression) {

        array_push($this->htmlItem,'select');
        return $this->phpTag."echo \$this->select{$expression}; ?>";
    }
    protected function compileListBoxes($expression) {
        return $this->phpTag."echo \$this->listboxes{$expression}; ?>";
    }
    protected function compileLink($expression) {
        return $this->phpTag."echo \$this->link{$expression}; ?>";
    }
    protected function compileSelectGroup($expression) {

        array_push($this->htmlItem,'selectgroup');
        $this->compilePush('');
        return $this->phpTag."echo \$this->select{$expression}; ?>";
    }
    protected function compileRadio($expression) {
        array_push($this->htmlItem,'radio');
        return $this->phpTag."echo \$this->radio{$expression}; ?>";
    }
    protected function compileCheckbox($expression) {
        array_push($this->htmlItem,'checkbox');
        return $this->phpTag."echo \$this->checkbox{$expression}; ?>";
    }
    protected function compileEndSelect() {
        $r=@array_pop($this->htmlItem);
        if (is_null($r)) {
            $this->showError("@endselect","Missing @select or so many @endselect",true);
        }
        return $this->phpTag."echo '</select>'; ?>";
    }
    protected function compileEndRadio() {
        $r=@array_pop($this->htmlItem);
        if (is_null($r)) {
            return $this->showError("@EndRadio","Missing @Radio or so many @EndRadio",true);
        }
        return '';
    }
    protected function compileEndCheckbox() {
        $r=@array_pop($this->htmlItem);
        if (is_null($r)) {
            return $this->showError("@EndCheckbox","Missing @Checkbox or so many @EndCheckbox",true);
        }
        return '';
    }
    protected function compileItem($expression) {
        // we add a new attribute with the type of the current open tag
        $r=end($this->htmlItem);
        $x=trim($expression);
        $x="('{$r}',".substr($x,1);
        return $this->phpTag."echo \$this->item{$x}; ?>";
    }
    protected function compileItems($expression) {
        // we add a new attribute with the type of the current open tag
        $r=end($this->htmlItem);
        $x=trim($expression);
        $x="('{$r}',".substr($x,1);
        return $this->phpTag."echo \$this->items{$x}; ?>";
    }
    protected function compileTrio($expression) {
        // we add a new attribute with the type of the current open tag
        $r=end($this->htmlItem);
        $x=trim($expression);
        $x="('{$r}',".substr($x,1);
        return $this->phpTag."echo \$this->trio{$x}; ?>";
    }
    protected function compileTrios($expression) {
        // we add a new attribute with the type of the current open tag
        $r=end($this->htmlItem);
        $x=trim($expression);
        $x="('{$r}',".substr($x,1);
        return $this->phpTag."echo \$this->trios{$x}; ?>";
    }
    protected function compileInput($expression) {
        return $this->phpTag."echo \$this->input{$expression}; ?>";
    }
    protected function compileFile($expression) {
        return $this->phpTag."echo \$this->file{$expression}; ?>";
    }
    protected function compileImage($expression) {
        return $this->phpTag."echo \$this->image{$expression}; ?>";
    }
    protected function compileTextArea($expression) {
        return $this->phpTag."echo \$this->textArea{$expression}; ?>";
    }
    protected function compileHidden($expression) {
        return $this->phpTag."echo \$this->hidden{$expression}; ?>";
    }
    protected function compileLabel($expression) {
        return $this->phpTag."// {$expression} \n echo \$this->label{$expression}; ?>";
    }
    protected function compileCommandButton($expression) {
        return $this->phpTag."echo \$this->commandButton{$expression}; ?>";
    }
    protected function compileForm($expression) {
        return $this->phpTag."echo \$this->form{$expression}; ?>";
    }
    protected function compileEndForm() {
        return $this->phpTag."echo '</form>'; ?>";
    }
    //</editor-fold>

    //<editor-fold desc="used function">
    public function select($name,$value,$extra='') {
        if (strpos($extra,'readonly')===false) {
            return "<select id='" . static::e($name) . "' name='" . static::e($name) . "' {$this->convertArg($extra)}>\n";
        } else {
            return "
                <input id='".static::e($name)."' name='".static::e($name)."' type='hidden' value='".static::e($value)."' />
                <select id='" . static::e($name) . "_disable' name='" . static::e($name) . "_disable' disabled {$this->convertArg($extra)}>\n";
        }
    }
    public function Link($url,$label,$extra='') {
        return "<a href='{$url}' {$this->convertArg($extra)}>{$label}</a>";
    }

    /**
     * Find an element in a array of arrays
     * If the element doesn't exist in the array then it returns false, otherwise returns true
     * @param $find
     * @param array $array array of primitives or objects
     * @param array $field field to search
     * @return bool
     */
    private function listboxesFindArray($find,$array,$field) {

        if (count($array)==0) {
            return false;
        }
        if (!is_array($array[0])) {
            return in_array($find,$array);
        } else {
            foreach ($array as $elem) {
                if ($elem[$field] == $find) {
                    return true;
                }
            }
        }
        return false;
    }
    public function listboxes($name,$allvalues, $fieldId, $fieldText,$selectedId,$extra='') {
        $html="";
        $html.= "<table>\n";
        $html.= "    <tr>\n";
        $html.= "	    <td>\n";
        $html.= "		    <select id='{$name}_noselected' size='6' multiple='multiple' $extra>\n";
        if (count($allvalues)==0) {
            $allvalues=[];
        }
        $html2="";
        foreach($allvalues as $v) {
            if (is_object($v)) {
                $v=(array) $v;
            }
            if (!$this->listboxesFindArray($v[$fieldId],$selectedId,$fieldId)) {
                $html .= "<option value='".$v[$fieldId]."'>".$v[$fieldText]."</option>\n";
            } else {
                $html2.= "<option value='".$v[$fieldId]."'>".$v[$fieldText]."</option>\n";
            }
        }
        $html.= "			</select>\n";
        $html.= "		</td>\n";
        $html.= "		<td style='text-align:center;'>\n";
        $html.= "            <input type='button' value='>' id='{$name}_add'/><br>\n";
        $html.= "            <input type='button' value='>>' id='{$name}_addall'/><br>\n";
        $html.= "            <input type='button' value='<' id='{$name}_delete'/><br>\n";
        $html.= "            <input type='button' value='<<' id='{$name}_deleteall'/><br>\n";
        $html.= "		</td>\n";
        $html.= "		<td>\n";
        $html.= "			<select id='{$name}' name='{$name}' size='6' multiple='multiple'>\n";
        $html.= $html2;
        $html.= "			</select>\n";
        $html.= "		</td>\n";
        $html.= "	</tr>\n";
        $html.= "</table>\n";
        return $html;
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

    /**
     * @param $id
     * @param string $value
     * @param string $text
     * @param string|null $valueSelected
     * @param string $extra
     * @return string
     */
    public function checkbox($id,$value='',$text='',$valueSelected='',$extra='') {
        $num=func_num_args();
        if ($num>2) {
            if ($value==$valueSelected ) {
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
     * @param array|string $valueId if is an array then the first value is used as value, the second is used as extra
     * @param $valueText
     * @param array|string $selectedItem Item selected (optional)
     * @param string $wrapper Wrapper of the element.  For example, <li>%s</li>
     * @param string $extra
     * @return string
     * @internal param string $fieldId Field of the id
     * @internal param string $fieldText Field of the value visible
     */
    public function item($type,$valueId, $valueText, $selectedItem='',$wrapper='', $extra='') {
        $id=@end($this->htmlCurrentId);
        $wrapper=($wrapper=='')?'%s':$wrapper;
        if (is_array($selectedItem)) {
            $found=in_array($valueId,$selectedItem);
        } else {
            $found = $valueId == $selectedItem;

        }

        $valueHtml= (!is_array($valueId))?"value='{$valueId}'":"value='{$valueId[0]}' data='{$valueId[1]}'";
        switch ($type) {
            case 'select':
                $selected=($found)?'selected':'';
                return sprintf($wrapper,"<option $valueHtml $selected ".
                    $this->convertArg($extra).">{$valueText}</option>\n");
                break;
            case 'radio':
                $selected=($found)?'checked':'';
                return sprintf($wrapper,"<input type='radio' id='".static::e($id)
                    ."' name='".static::e($id)."' $valueHtml $selected "
                    .$this->convertArg($extra)."> {$valueText}\n");
                break;
            case 'checkbox':
                $selected=($found)?'checked':'';
                return sprintf($wrapper,"<input type='checkbox' id='".static::e($id)
                    ."' name='".static::e($id)."' $valueHtml $selected "
                    .$this->convertArg($extra)."> {$valueText}\n");
                break;

            default:
                return '???? type undefined: [$type] on @item<br>';
        }
    }

    /**
     * @param string $type type of the current open tag
     * @param array $arrValues Array of objects/arrays to show.
     * @param string $fieldId Field of the id (for arrValues)
     * @param string $fieldText Field of the id of selectedItem
     * @param array|string $selectedItem Item selected (optional)
     * @param string $selectedFieldId field of the selected item.
     * @param string $wrapper Wrapper of the element.  For example, <li>%s</li>
     * @param string $extra (optional) is used for add additional information for the html object (such as class)
     * @return string
     * @version 1.1 2017
     */
    public function items($type, $arrValues, $fieldId, $fieldText, $selectedItem='',$selectedFieldId='', $wrapper='', $extra='') {
        if (count($arrValues)==0) {
            return "";
        }

        if (is_object(@$arrValues[0])) {
            $arrValues=(array) $arrValues;
        }
        if (is_array($selectedItem)) {
            if (is_object(@$selectedItem[0])) {
                $primitiveArray=[];
                foreach($selectedItem as $v) {
                    $primitiveArray[]=$v->{$selectedFieldId};
                }
                $selectedItem=$primitiveArray;
            }
        }
        $result='';
        if (is_object($selectedItem)) {
            $selectedItem=(array)$selectedItem;
        }
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
                    $this->convertArg($extra).">{$valueText}</option>\n");
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
    public function file($id,$fullfilepath='',$file='',$extra='')
    {
        return "<a href='$fullfilepath'>$file</a>
        <input id='".static::e($id)."_file' name='".static::e($id)."_file' type='hidden' value='".static::e($file)."' />
        <input id='".static::e($id)."' name='".static::e($id)."' type='file' ".$this->convertArg($extra)." value='".static::e($fullfilepath)."' />\n";
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