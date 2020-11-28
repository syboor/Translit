<!DOCTYPE html>
<?php
  error_reporting(E_ALL & ~E_NOTICE); // Don't report notices
?>
<html>
<head>
<title>Latin (transliterated) Greek to Unicode Greek Converter</title>
<meta charset="UTF-8">
<script type="text/javascript">
function inserttextatcursor(field, insert) {
// http://parentnode.org/javascript/working-with-the-cursor-position/

  var newtext;
  var start;

  if (document.selection) {
    // IE
    field.focus();
    var range = document.selection.createRange();
    range.text = insert;
  } else if (field.selectionStart || field.selectionStart == '0') {
    var selLength = field.textLength;
    start = field.selectionStart;
    var end = field.selectionEnd;
    field.value = (field.value).substring(0, start) + insert + (field.value).substring(end, field.value.length);
    setCaretTo(field, start + insert.length);
    field.focus();
  } else {
    // Andere browsers: voeg de tags toe aan het eind van de tekst.
    field.value += insert;
    field.focus();
  }
}
// Stel cursorpositie in in textarea
function setCaretTo(obj, pos) {   
    if(obj.createTextRange) {   
        /* Create a TextRange, set the internal pointer to  
           a specified position and show the cursor at this  
           position  
        */  
        var range = obj.createTextRange();   
        range.move("character", pos);   
        range.select();   
    } else if(obj.selectionStart) {   
        /* Gecko is a little bit shorter on that. Simply  
           focus the element and set the selection to a  
           specified position  
        */  
        obj.focus();   
        obj.setSelectionRange(pos, pos);   
    }   
} 
</script>
<style type="text/css">
  body, div, p, th, td, li, dd {
    font-family: Trebuchet MS, Arial, sans-serif;
    font-size: 11px;
    line-height: 1.5;
    color: #333333;
  }
  body {
    min-width: 780px;
  }
  #wrapper {
    text-align: left;
    width: 780px;
    margin-left: auto;
    margin-right: auto;
  }
  #titlebar {
    display: inline-block;
    width: 100%;
    padding: 2%;
  }
  #content {
    display: inline-block;
    padding: 2%;
    vertical-align: top;
    width: 63%;
  }
  #sidebar {
    display: inline-block;
    padding-left: 2%;
    vertical-align: top;
    border-left: 1px solid #666666;
    width: 30%;
  }
  #latin {
    width: 100%;
  }
  #outputcontainer {
    /* font-family: Aristarcoj, Arial, Helvetica, sans-serif; */
    font-family: Arial, Helvetica, sans-serif;
    font-size: 20px;
    border: 1px solid #999999;
    padding: 2px;
  }

</style>
</head>
<body>
<div id="wrapper">
<div id="titlebar">
<h1>Greek Transliterator</h1>
<p>Convert from Latin alphabet to Greek alphabet</p>
</div>
<div id="content">
<?php
include_once('./lib_tlg.php');
 
if ($_POST['latin']) {
  $latin = $_POST['latin'];
  $greek = TLG_Latin2Utf($latin);
}

?>
<form action="<?= getenv('SCRIPT_NAME'); ?>" method="post" accept-charset="utf-8">
<input type="hidden" name="enforceutf8" value="&#307;">
<p>
<input name="eta" type="button" id="eta" value="ê" style="font-weight: bold; width: 25px;" onClick="inserttextatcursor(document.getElementById('latin'), 'ê');"> 
<input name="capeta" type="button" id="capeta" value="Ê" style="font-weight: bold; width: 25px;" onClick="inserttextatcursor(document.getElementById('latin'), 'Ê');"> 
<input name="omega" type="button" id="omega" value="ô" style="font-weight: bold; width: 25px;" onClick="inserttextatcursor(document.getElementById('latin'), 'ô');"> 
<input name="capomega" type="button" id="capomega" value="Ô" style="font-weight: bold; width: 25px;" onClick="inserttextatcursor(document.getElementById('latin'), 'Ô');"> 
</p>
<textarea name="latin" id="latin" rows="10" cols="60">
<?= htmlspecialchars(@$latin); ?>
</textarea>
<p style="text-align: right"><input type="submit" value="Convert" /></p>
</form>
<?php
  if ($greek) {
    echo '<p><b>Result:</b></p><div id="outputcontainer">' .
         TLG_Text2Html(@$greek) .
         '</div>';
  }
?>  
</div>
<div id="sidebar">
<p><b>Example input:</b>

<p>ploion - fulakê - artos - stauros - prosôpon - adelfos - probaton - nefelê</p>

<p>alpha - bêta - gamma - delta - epsilon - zêta - êta - thêta - iôta - kappa - lambda - mu - nu - xi - omikron - pi - rho - sigma - tau - upsilon - phi - chi - psi - ômega</p>

<p>ALPHA - BÊTA - GAMMA - DELTA - EPSILON - ZÊTA - ÊTA - THÊTA - IÔTA - KAPPA - LAMBDA - MU - NU - XI - OMIKRON - PI - RHO - SIGMA - TAU - UPSILON - PHI - CHI - PSI - ÔMEGA</p>

<p>EN ARCHÊ ên ho logos, kai ho logos ên pros ton theon, kai theos ên ho logos. 
Houtos ên en archêi pros ton theon. 
panta di' autou egeneto, kai chôris autou egeneto oude hen. 
ho gegonen en autôi zôê ên, kai hê zôê
ên to phôs tôn anthrôpôn: kai to phôs en têi skotiai phainei, kai hê skotia auto ou katelaben.
</p>
</div>
</div>
</body>
</html>