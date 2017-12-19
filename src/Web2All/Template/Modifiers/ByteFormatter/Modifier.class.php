<?php

/**
 * ByteFormatter class for use in Template
 * 
 *
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2007 Web2All B.V.
 * @version 0.1
 * @since 2007-07-30
 */
class Web2All_Template_Modifiers_ByteFormatter_Modifier extends Web2All_Manager_Plugin {
  
  /**
   * Formats the given size (bytes) into the 
   * correct quantifier.
   *
   * @param int $size
   * @param string $separator  [optional]
   * @param int $decimals  [optional]
   * @param string $decimal_sep  [optional]
   * @param string $thousands_sep  [optional]
   * @return string
   */
  public static function formatFilesize($size,$separator='',$decimals=1,$decimal_sep=',',$thousands_sep=' ') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $size > 1024; $i++) { $size /= 1024; }
    return number_format($size, $decimals, $decimal_sep, $thousands_sep).$separator.$units[$i];
  } 
}
?>