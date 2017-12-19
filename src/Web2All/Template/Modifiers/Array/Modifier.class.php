<?php

/**
 * Array class for use in Template.
 * 
 * Contains array related modifiers
 *
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2007 Web2All B.V.
 * @version 0.1
 * @since 2007-08-09
 */
class Web2All_Template_Modifiers_Array_Modifier extends Web2All_Manager_Plugin {
  
  /**
   * Translate the given value by getting the translation
   * from the translation_array where the value is the key.
   * 
   * return the value itself if no translation found (or the default value if
   * given.
   *
   * @param array $translation_array
   * @param string $value
   * @param string $default  [optional default value to return if not found]
   * @return string
   */
  public function translateValue($translation_array,$value,$default=null)
  {
    if (array_key_exists($value,$translation_array)) {
      return $translation_array[$value];
    }else{
      if (is_null($default)) {
        return $value;
      }else{
        return $default;
      }
    }
  }
  
  
}
?>