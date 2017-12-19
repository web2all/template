<?php

/**
 * DateTime class for use in Template.
 * 
 * Contains date time related modifiers
 *
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2007 Web2All B.V.
 * @version 0.1
 * @since 2007-07-31
 */
class Web2All_Template_Modifiers_DateTime_Modifier extends Web2All_Manager_Plugin {
  
  /**
   * Format a ISO date into the specified format
   *
   * @param string $isodate
   * @param string $format  format as in the PEAR Date package
   * @return string
   */
  public function dateFormat($isodate,$format='%Y-%m-%d %T')
  {
    $date=$this->Web2All->Plugin->Web2All_DateTime_DateTime($isodate);
    return $date->format($format);
  }
  
  /**
   * Get current date in ISo format (or optionally
   * another format)
   *
   * @param string $format
   * @return string
   */
  public function currentDate($format='%Y-%m-%d %T')
  {
    $date=$this->Web2All->Plugin->Web2All_DateTime_DateTime();
    return $date->format($format);
  }
  
}
?>