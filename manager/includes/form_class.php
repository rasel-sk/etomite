<?php
/*
*****************************************
filename: form_class.php
created by: Ralph A. Dahlgren
Created: 2006-05-22
Last Modified: 2007-05-21
[*] Fixed several bugs in the selectbox function
class definitions for form elements
utilizes Tigra Form Validation javascript
*****************************************
*/
class formClass{
  // global variables
  var $selected, $validate, $buttonPrefix, $buttonSuffix, $namePrefix, $nameSuffix, $labelPrefix, $labelSuffix, $tigraRules, $formLabels, $formName, $formId, $formFields;

  // constructor
  function formClass(){
    $this->validate=1; $this->selected=array(); $this->buttonPrefix=""; $this->buttonSuffix=""; $this->namePrefix="frm_"; $this->nameSuffix=""; $this->labelPrefix=""; $this->labelSuffix="_lbl"; $this->tigraRules=""; $this->formLabels=array(); $this->formName=""; $this->formId="myform"; $this->formFields=array();
  }

  // open form
  function openform($attributes=array()){
    $html='<form';
    foreach($attributes as $attribute=>$value){
      if($attribute=='name') $this->formName=$value;
      if($attribute=='id') $this->formId=$value;
      $html.=' '.$attribute.'="'.$value.'"';
    }
    $html.='>';
    $this->formFields['openForm'] = $html;
    return $html;
  }

  // close form
  function closeform(){
    $html = '</form>';
    $this->formFields['closeForm'] = $html;
    return $html;
  }

  // input|hidden|password|button|reset|submit|file|image|radiobutton|checkbox
  function input($attributes=array()){
    extract($attributes);
    if(in_array($type,array('button','reset','submit'))){
      $name = $this->buttonPrefix.$name.$this->buttonSuffix;
    } else {
      $name = $this->namePrefix.$name.$this->nameSuffix;
    }
    $html='<input ';
    if(($attributes['type']=='checkbox')&&($attributes['checked']=='')) unset($attributes['checked']);
    foreach($attributes as $attribute=>$value){
      switch($attribute) {
        case 'id':
          $value=$this->namePrefix.$value.$this->nameSuffix;
          $html.=$attribute.'="'.$value.'" ';
          break;
        case 'name': $value=$name; $html.=$attribute.'="'.$value.'" '; break;
        case 'label': $this->label($value); break;
        case 'validate': $value['name']=$name; $this->tigraAddRule($value); break;
        default: $html.=$attribute.'="'.$value.'" '; break;
      }
    }
    $html.=' />';
    $this->formFields[$name] = $html;
    return $html;
  }

  // textarea
  function textarea($attributes=array()){
    extract($attributes);
    $name=$this->namePrefix.$name.$this->nameSuffix;
    $html='<textarea ';
    $textvalue='';
    foreach($attributes as $attribute=>$value){
      switch($attribute) {
        case 'id':
          $value=$this->namePrefix.$value.$this->nameSuffix;
          $html.=$attribute.'="'.$value.'" ';
          break;
        case 'name': $value=$name; $html.=$attribute.'="'.$value.'" '; break;
        case 'label': $this->label($value); break;
        case 'validate': $value['name']=$name; $this->tigraAddRule($value); break;
        case 'value': $textvalue=$value; break;
        default: $html.=$attribute.'="'.$value.'" '; break;
      }
    }
    $html=preg_replace("/\"? $/","\">",$html);
    $html.=$textvalue.'</textarea>';
    $this->formFields[$name] = $html;
    return $html;
  }

  // selectbox
  function selectbox($attributes=array()){
    $options='';
    if(isset($attributes['selected'])) {
      if(strpos($attributes['name'],"[]")) {
        $selected=array();
        $selected=$attributes['selected'];
      } else {
        $selected=$attributes['selected'];
        if(count($selected)>1) {
          exit('Invalid number of preselected options for '.$attributes['name']);
        }
      }
      if(isset($attributes['selectedby'])) {
        $selectedby = ($attributes['selectedby']=='key') ? "key" : "value";
        unset($attributes['selectedby']);
      }
      $attributes['selected']=array();
    }
    $html="\n".'<select ';
    $name=$this->namePrefix.$attributes['name'].$this->nameSuffix;
    foreach($attributes as $attribute=>$value){
      switch($attribute) {
        case 'options':
          foreach($value as $key=>$label){
            $isselected = "";
            if(is_array($selected)) {
              if($which = ($selectedby=="value") ? in_array($label,$selected) : in_array($key,$selected)) {
                $isselected = " selected=\"selected\"";
              }
            } elseif(is_scalar($selected) && ($key==$selected)) {
              $isselected = " selected=\"selected\"";
            }
            $options.='<option value="'.$key.'"'.$isselected.'>'.$label.'</option>'."\n";
          }
          break;
        case 'id':
          $value=$this->namePrefix.$value.$this->nameSuffix;
          $html.=$attribute.'="'.$value.'" ';
          break;
        case 'name': $value=$name; $html.=$attribute.'="'.$value.'" '; break;
        case 'label': $this->label($value); break;
        case 'validate': $value['name']=$name; $this->tigraAddRule($value); break;
        case 'selected': case 'selectedby': break;
        default: $html.=$attribute.'="'.$value.'" '; break;
      }
    }
    $html=preg_replace("/\"? $/","\">",$html);
    $html.="\n".$options.'</select>'."\n";
    $this->formFields[$name] = $html;
    return $html;
  }

  // label
  function label($attributes=array()){
    $html = "<label";
    foreach($attributes as $attribute=>$value){
      switch($attribute) {
        case 'label': $label=$value; break;
        case "id": $html.= ' id="'.$this->labelPrefix.$value.$this->labelSuffix.'"'; break;
        case "for": $name=$value; $html.=' for="'.$this->namePrefix.$value.$this->nameSuffix.'"'; break;
        default: $html.=' '.$attribute.'="'.$value.'"';
      }
    }
    $html.=">".$label."</label>";
    $this->formLabels[$this->labelPrefix.$name.$this->labelSuffix]=$html;
    return $html;
  }

  // tigra rule creator
  function tigraAddRule($attributes=array()){
  /*
  form field description structure
    'l': 'div',       // div
    'r': true,        // required
    'f': 'null',      // format (alpha, alphanum, unsigned, integer, real, email, phone, date, time)
    't': 't_field',   // id of the element to highlight if input not validated
    'm': null,        // must match specified form field
    'mn': null,       // minimum length
    'mx': null        // maximum length
    <form> tag must contain: 'onsubmit'=>'return v.exec()'

    var a_fields = {
      'frm_name':{'l':'*Name:', 'r':true, 't':'name_lbl'},
      'frm_email':{'l':'*Email:', 'r':true, 't':'email_lbl'},
      'frm_weburl':{'l':'Web URL:','r':false,'t':'weburl_lbl'},
      'frm_webtitle':{'l':'Web title:','r':false,'t':'webtitle_lbl'},
      'frm_message':{'l':'*Message:','r':true,'t':'message_lbl'}
    },
    o_config = { 'to_disable' : ['Submit', 'Reset'], 'alert' : 1 }

    validator constructor call

    var v = new validator('myform', a_fields, o_config);
    */
    if(!isset($attributes['name'])){
      $attributes['name']=$this->namePrefix.$attributes['t'].$this->nameSuffix;
    }
    $html="  '".$attributes['name']."':{";
    foreach($attributes as $attribute=>$value){
      if($attribute=='t') $value=$this->labelPrefix.$value.$this->labelSuffix;
      if($attribute!='name') $html.="'".$attribute."':'".$value."', ";
    }
    $html.= "},\n";
    $html = str_replace(", }","}",$html);
    $this->tigraRules.=$html;
    return $html;
  }

  // tigra validation code creator
  function tigraGetRules(){
  $this->tigraRules = rtrim($this->tigraRules,"},\n")."}\n";
    return
// javascript code below is outdented for proper source code formatting
"\n<script type='text/javascript' src='./manager/media/tigra_form_validator/validator.js'></script>
<script type=\"text/javascript\">
//<![CDATA[
var a_fields={
".$this->tigraRules."};
var o_config = { 'to_disable' : ['Submit', 'Reset'], 'alert' : ".$this->validate." }
var v = new validator('".$this->formId."', a_fields, o_config);
//]]>
</script>
";
  }
}

?>
