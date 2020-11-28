<?php

// Greek character names and their unicode values
$TLG_TABLE = array(
  'alpha' => 945,
  'beta' => 946,
  'gamma' => 947,
  'delta' => 948,
  'epsilon' => 949,
  'zeta' => 950,
  'eta' => 951,
  'theta' => 952,
  'iota' => 953,
  'kappa' => 954,
  'lambda' => 955,
  'mu' => 956,
  'nu' => 957,
  'xi' => 958,
  'omicron' => 959,
  'pi' => 960,
  'rho' => 961,
  'sigmaf' => 962,
  'sigma' => 963,
  'tau' => 964,
  'upsilon' => 965,
  'phi' => 966,
  'chi' => 967,
  'psi' => 968,
  'omega' => 969,
);

// Left: unicode value of plain, unadorned vowel (lower or upper)
// Right: unicode value of same vowel with smooth accent.
$TLG_MODIFIED_TABLE = array(
  $TLG_TABLE['alpha'] => 7936,
  $TLG_TABLE['epsilon'] => 7952,
  $TLG_TABLE['eta'] => 7968,
  $TLG_TABLE['iota'] => 7984,
  $TLG_TABLE['omicron'] => 8000,
  $TLG_TABLE['upsilon'] => 8016,
  $TLG_TABLE['omega'] => 8032,
);

$TLG_MODIFIERS_TABLE = array(
  'smooth' => 8125,
  'rough' => 8190,
  'iota' => 8126,
);

function TLG_Latin2Utf($latin) {
  global $TLG_TABLE, $TLG_ISO_INPUT;
  
  $pos = 0;
  $length = strlen($latin);
  $greek = '';
  
  while ($pos < $length) {
    $char = $latin[$pos];
    $charcode = ord($char);
    
    if ($char == '&' && $latin[$pos+1] == '#')  {
      $charendpos = strpos($latin, ';', $pos);
      $char = substr($latin, $pos, $charendpos - $pos + 1);
      $charcode = intval(substr($latin, $pos + 2, $charendpos - $pos - 2));
      $pos = $charendpos;
    }

    $nextchar = $latin[$pos + 1];

    if (TLG_InputIsLetter($char)) {
      if ($cap) $char = strtoupper($char);
      $ucp = ""; // unicode code point
      if ($char == "s" && ($nextchar == "" || $nextchar == " " || $nextchar == "\n" || $nextchar == "\r" || $nextchar == "\t")) { 
        // final sigma
        $ucp = $TLG_TABLE['sigmaf'];
      } elseif ((strtolower($char) == "d") && (strtolower($nextchar) == "z")) {
        // dzeta -> zeta 
        // skip the d, do nothing
      } elseif ((strtolower($char) == "t") && (strtolower($nextchar) == "h")) {
        $ucp = $TLG_TABLE['theta'];
        $pos++;
      } elseif ((strtolower($char) == "k") && (strtolower($nextchar) == "s")) {
        $ucp = $TLG_TABLE['xi'];
        $pos++;
      } elseif ((strtolower($char) == "p") && (strtolower($nextchar) == "h")) {
        $ucp = $TLG_TABLE['phi'];
        $pos++;
      } elseif ((strtolower($char == "k") || (strtolower($char) == "c")) && (strtolower($nextchar) == "h")) {
        $ucp = $TLG_TABLE['chi'];
        $pos++;
      } elseif ((strtolower($char) == "p") && (strtolower($nextchar) == "s")) {
        $ucp = $TLG_TABLE['psi'];
        $pos++;
      } elseif (($alphapos = @stripos('abgdez'.$TLG_ISO_INPUT['ecirc'].'#iklmnxopr#stuf##'.$TLG_ISO_INPUT['ocirc'], $char)) !== false) {
        // most of the alphabet (the part that can be transliterated with a single letter)
        $ucp = $alphapos + $TLG_TABLE['alpha'];
      } elseif (strtolower($char) == "c") {
        $ucp = $TLG_TABLE['kappa'];
	  }
      
      if ($ucp) {
        $ucp = TLG_MatchCase($ucp, $char);

        if ((strtolower($char) == 'r') && ($prevchar == "" || $prevchar == " " || $prevchar == "\n" || $prevchar == "\r" || $prevchar == "\t")) $rough = true;
        if (TLG_IsVowel($ucp) && !$rough && ($prevchar == "" || $prevchar == " " || $prevchar == "\n" || $prevchar == "\r" || $prevchar == "\t")) $smooth = true;
        if ((strtolower($char) == $TLG_ISO_INPUT['ecirc'] || strtolower($char) == $TLG_ISO_INPUT['ocirc']) && (strtolower($nextchar) == 'i')) {
  // alpha omitted because it can be both with adscript (kai) and with subscript...
  $iota = true;
  $pos++; 
}
        if (!$iota && (strtolower($char) == 'e' || strtolower($char) == 'a' || strtolower($char) == 'e' || strtolower($char) == 'o')  && (($nextalphapos = @strpos('a###e###i#####o#####u####', $nextchar)) !== false)) { 
          // TODO: get a good list of which combinations are diphtongues and which are two syllables
          // Two vowels: move accents to the next vowel.
          // NB: strpos, not stripos. Don't move accents if the next letter is uppercase.
          $greek .= TLG_MakeGlyph($ucp) . TLG_MakeGlyph($nextalphapos + $TLG_TABLE['alpha'], array('smooth' => $smooth, 'rough' => $rough));
          $pos++;
        } else {
          $greek .= TLG_MakeGlyph($ucp, array('smooth' => $smooth, 'rough' => $rough, 'iota' => $iota));
        }
      }  
      
    }
    
    if (TLG_InputIsModifier($char)) {
      if ((strtolower($char) == 'h') && ($prevchar == "" || $prevchar == " " || $prevchar == "\n" || $prevchar == "\r" || $prevchar == "\t")) { // Should only happen at beginning of word! (sanhedrin -> no breathing)
        $rough = true;
        if ($char == 'H') $cap = true;
      }      
    } else {
      // Clear old modifiers
      // If there is a succession of modifiers without letters in between, modifiers are not cleared
      $rough = false;
      $cap = false;
      $smooth = false;
      $iota = false;
    }

    if (!TLG_InputIsLetter($char) && !TLG_InputIsModifier($char)) {
      $greek .= $char;
    }
    
    $pos++;
    $prevchar = $char;
  }  

  // TODOs:
  // Other modifiers / accents
  // "ai". How do I know whether to use iota subscript or adscript?
  
  return $greek;
}

function TLG_InputIsLetter($input) {
  global $TLG_ISO_INPUT;
  if (@stripos('abcdefgiklmnoprstuvwxyz'.$TLG_ISO_INPUT['ecirc'].$TLG_ISO_INPUT['ocirc'], $input) !== false) return true;
  
  return false;
}

function TLG_InputIsModifier($input) {
  if (@stripos('h', $input) !== false) return true;
}

function TLG_IsVowel($ucp) {
  global $TLG_TABLE;
  $ucp = TLG_ToLower($ucp);
  return ($ucp == $TLG_TABLE['alpha'] || $ucp == $TLG_TABLE['epsilon'] || $ucp == $TLG_TABLE['eta'] || 
          $ucp == $TLG_TABLE['iota'] || $ucp == $TLG_TABLE['omicron'] || $ucp == $TLG_TABLE['upsilon'] ||
          $ucp == $TLG_TABLE['omega']);
}

function TLG_ToUpper($ucp) {
  global $TLG_TABLE;
  if (($ucp >= $TLG_TABLE['alpha']) && ($ucp <= $TLG_TABLE['omega'])) {
    if ($ucp == $TLG_TABLE['sigmaf']) {
      return $ucp - 32 + 1;
    } else {
      return $ucp - 32;
    }
  }
  return $ucp;
}

function TLG_ToLower($ucp) {
  global $TLG_TABLE;
  if (($ucp >= $TLG_TABLE['alpha'] - 32) && ($ucp <= $TLG_TABLE['omega'] - 32)) {
    return $ucp + 32;
  }
  return $ucp;
}

function TLG_MatchCase($ucp, $char) {
  if ($char == strtoupper($char)) {
    return TLG_ToUpper($ucp);
  } else {
    return $ucp;
  }
}

function TLG_MakeGlyph($ucp, $modifiers = array()) {
  if ($modifiers) {
    global $TLG_TABLE, $TLG_MODIFIED_TABLE, $TLG_MODIFIERS_TABLE;
    if ($TLG_MODIFIED_TABLE[$ucp]) {
      $iota = 0;
      if ($modifiers['iota']) {
        if ($ucp == $TLG_TABLE['alpha']) $iota = 128;
        if ($ucp == $TLG_TABLE['eta']) $iota = 112;
        if ($ucp == $TLG_TABLE['eta']) $iota = 112;
      }
      if ($modifiers['smooth']) {
        return TLG_Ucp2utf8($TLG_MODIFIED_TABLE[$ucp]);
      } elseif ($modifiers['rough']) {
        return TLG_Ucp2utf8($TLG_MODIFIED_TABLE[$ucp] + 1);
      } elseif ($modifiers['iota']) {
        if ($ucp == $TLG_TABLE['alpha']) $ucp = 8115;
        if ($ucp == $TLG_TABLE['eta']) $ucp = 8131;
        if ($ucp == $TLG_TABLE['omega']) $ucp = 8179;
        return TLG_Ucp2utf8($ucp);
      } 
      // todo: combina iota with breathing etc.
    } elseif ($modifiers['rough'] && $ucp == $TLG_TABLE['rho']){
      return TLG_Ucp2utf8(8165);
    } else {
      if ($modifiers['smooth']) {
        return TLG_Ucp2utf8($TLG_MODIFIERS_TABLE['smooth']) . TLG_Ucp2utf8($ucp);
      } elseif ($modifiers['rough']) {
        return TLG_Ucp2utf8($TLG_MODIFIERS_TABLE['rough']) . TLG_Ucp2utf8($ucp);
      } elseif ($modifiers['iota']) {
        return TLG_Ucp2utf8($ucp) . TLG_Ucp2utf8($TLG_MODIFIERS_TABLE['iota']);      
      }
    }
  } 
  
  return TLG_Ucp2utf8($ucp);
}

function TLG_Ucp2utf8($num)
{
    if($num<=0x7F)       return chr($num);
    if($num<=0x7FF)      return chr(($num>>6)+192).chr(($num&63)+128);
    if($num<=0xFFFF)     return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
    if($num<=0x1FFFFF)   return chr(($num>>18)+240).chr((($num>>12)&63)+128).chr((($num>>6)&63)+128).chr(($num&63)+128);
    return '';
}

function TLG_Utf2ucp($c)
{
    $ord0 = ord($c{0}); if ($ord0>=0   && $ord0<=127) return $ord0;
    $ord1 = ord($c{1}); if ($ord0>=192 && $ord0<=223) return ($ord0-192)*64 + ($ord1-128);
    $ord2 = ord($c{2}); if ($ord0>=224 && $ord0<=239) return ($ord0-224)*4096 + ($ord1-128)*64 + ($ord2-128);
    $ord3 = ord($c{3}); if ($ord0>=240 && $ord0<=247) return ($ord0-240)*262144 + ($ord1-128)*4096 + ($ord2-128)*64 + ($ord3-128);
    return false;
}

function TLG_Text2Html($text) {
  $html = htmlspecialchars($text);

  $html = str_replace (chr(13).chr(10), "<br />", $html);
  $html = str_replace (chr(10), "<br />", $html);
  $html = str_replace (chr(13), "<br />", $html);
  $html = str_replace ("  ", " &nbsp;", $html);
  
  return $html;
}

?>