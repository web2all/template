<?php

/**
 * Alternator class for use in Template.
 * 
 * This class can alternate between a list of given strings
 *
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2009 Web2All B.V.
 * @since 2009-02-13
 */
class Web2All_Template_Modifiers_Alternator_Modifier extends Web2All_Manager_Plugin {
  
  protected static $iterator=array();
  
  /**
   * Get the next value from the given strings
   *
   * @param mixed $value1  The first value (or an array with values)
   * @param string $value2  The second value (or null if first value is array)
   * @return string
   */
  public function alternate($value1,$value2=null)
  {
    $valuelist=array();
    if (!is_array($value1)) {
      $valuelist[]=$value1;
      $valuelist[]=$value2;
    }else{
      $valuelist=$value1;
    }
    $alternator_id=implode('#',$valuelist);
    if (!array_key_exists($alternator_id,self::$iterator)) {
      self::$iterator[$alternator_id]=0;
    }
    $ret=$valuelist[self::$iterator[$alternator_id]];
    self::$iterator[$alternator_id]++;
    if (self::$iterator[$alternator_id]>=count($valuelist)) {
      self::$iterator[$alternator_id]=0;
    }
    return $ret;
  }
  
  /**
   * Reset an alternator
   * 
   * You need to provide all values of the alternator which needs to be reset.
   *
   * @param mixed $value1  The first value (or an array with values)
   * @param string $value2  The second value (or null if first value is array)
   */
  public function reset($value1,$value2=null)
  {
    $valuelist=array();
    if (!is_array($value1)) {
      $valuelist[]=$value1;
      $valuelist[]=$value2;
    }else{
      $valuelist=$value1;
    }
    $alternator_id=implode('#',$valuelist);
    unset(self::$iterator[$alternator_id]);
  }
  
  
}
?>