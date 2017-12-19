<?php

/**
 * SecondsFormatter class for use in Template
 * 
 *
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2007 Web2All B.V.
 * @version 0.1
 * @since 2008-06-27
 */
class Web2All_Template_Modifiers_SecondsFormatter_Modifier extends Web2All_Manager_Plugin {
  
  public static $translations = array(
    'UK' => array (
      's' => array(' second',' seconds'),
      'm' => array(' minute',' minutes'),
      'h' => array(' hour',' hours'),
      'd' => array(' day',' days'),
      'y' => array(' year',' years'),
      'c' => array(' century',' centuries')
    ),
    'NL' => array (
      's' => array(' seconde',' seconden'),
      'm' => array(' minuut',' minuten'),
      'h' => array(' uur',' uren'),
      'd' => array(' dag',' dagen'),
      'y' => array(' jaar',' jaren'),
      'c' => array(' eeuw',' eeuwen')
    )
  );
  
  /**
   * Formats the given seconds into a 
   * better (human) readable format.
   *
   * @param int $seconds
   * @param string $translation  [optional] which translation scheme to use
   * @param string $separator  [optional]
   * @return string
   */
  public static function formatSeconds($seconds,$translation='UK',$separator=' ') {
    $units = array('s' => 60, 'm' => 60, 'h' => 24, 'd' => 365, 'y' => 100, 'c' => 99999);
    
    if (!array_key_exists($translation,self::$translations)) {
      $translation='UK';
    }
    
    $neg=false;
    if ($seconds<0) {
      $neg=true;
      $seconds=abs($seconds);
    }
    
    $formatted_parts=array();
    
    $nextstage_amount=$seconds;
    foreach ($units as $code => $unit) {
      $current_amount=$nextstage_amount;
      
      $nextstage_amount=floor($current_amount/$unit);
      $leftover=$current_amount % $unit;
      
      if($leftover){
        array_unshift($formatted_parts,($neg ? '-' : '').$leftover.self::$translations[$translation][$code][($leftover==1 ? 0 : 1)]);
      }
      
      if ($nextstage_amount==0) {
        break;
      }
      
    }
    
    return implode($separator,$formatted_parts);
  } 
}
?>