<html>
<head>
<title>Latin (transliterated) Greek to Unicode Greek Converter</title>
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
    *display: inline;  
  }
  #sidebar {
    display: inline-block;
    padding-left: 2%;
    vertical-align: top;
    border-left: 1px solid #666666;
    width: 30%;
    *display: inline;
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
<p><big>Convert from Latin alphabet to Greek alphabet</big></p>
</div>
<div id="content">
<?php
include_once(getenv('DOCUMENT_ROOT') . '/phplib/' . 'lib_general.php');
include_once(getenv('DOCUMENT_ROOT') . '/phplib/' . 'lib_tlg.php');
 
if ($_POST['latin']) {
  $latin = LF_Gpc2Internal($_POST['latin']);
  $greek = TLG_Latin2Unicode($latin);
}

?>
<form action="<?= getenv('SCRIPT_NAME'); ?>" method="post" accept-charset="utf-8">
<input type="hidden" name="enforceutf8" value="&#307;">
<p>
<input name="eta" type="button" id="eta" value="�" style="font-weight: bold; width: 25px;" onClick="inserttextatcursor(document.getElementById('latin'), '�');"> 
<input name="capeta" type="button" id="capeta" value="�" style="font-weight: bold; width: 25px;" onClick="inserttextatcursor(document.getElementById('latin'), '�');"> 
<input name="omega" type="button" id="omega" value="�" style="font-weight: bold; width: 25px;" onClick="inserttextatcursor(document.getElementById('latin'), '�');"> 
<input name="capomega" type="button" id="capomega" value="�" style="font-weight: bold; width: 25px;" onClick="inserttextatcursor(document.getElementById('latin'), '�');"> 
</p>
<textarea name="latin" id="latin" rows="10" cols="60">
<?= LF_HtmlSpecialChars($latin); ?>
</textarea>
<p style="text-align: right"><input type="submit" value="Convert" /></p>
</form>
<?
  if ($greek) {
    echo '<p><b>Result:</b></p><div id="outputcontainer">' .
         LF_HtmlSpecialChars($greek, false, true) .
         '</div>';
  }
?>  
</div>
<div id="sidebar">
<p><b>Example input:</b>

<p>ploion - fulak� - artos - stauros - pros�pon - adelfos - probaton - nefel�</p>

<p>alpha - b�ta - gamma - delta - epsilon - z�ta - �ta - th�ta - i�ta - kappa - lambda - mu - nu - xi - omikron - pi - rho - sigma - tau - upsilon - phi - chi - psi - �mega</p>

<p>ALPHA - B�TA - GAMMA - DELTA - EPSILON - Z�TA - �TA - TH�TA - I�TA - KAPPA - LAMBDA - MU - NU - XI - OMIKRON - PI - RHO - SIGMA - TAU - UPSILON - PHI - CHI - PSI - �MEGA</p>

<p>EN ARCH� �n ho logos, kai ho logos �n pros ton theon, kai theos �n ho logos. 
Houtos �n en arch�i pros ton theon. 
panta di' autou egeneto, kai ch�ris autou egeneto oude hen. 
ho gegonen en aut�i z�� �n, kai h� z��
�n to ph�s t�n anthr�p�n: kai to ph�s en t�i skotiai phainei, kai h� skotia auto ou katelaben.
</p>
</div>
</div>
</body>
</html>