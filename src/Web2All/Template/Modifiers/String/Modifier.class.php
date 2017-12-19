<?php

/**
 * String modifier class for use in Template.
 * 
 * Contains string related modifiers
 *
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2007 Web2All B.V.
 * @version 0.1
 * @since 2008-08-13
 * 
 * example usage:
 * <?php $this->_($this->modifier('String')->summary('very long text',400))?>
 */
class Web2All_Template_Modifiers_String_Modifier extends Web2All_Manager_Plugin {
  
  /**
   * generate s summary/short text of a long text
   * 
   * TODO: prevent breakage of HTML when $keephtml is true
   *
   * @param string $string  the text which needs shortening
   * @param int $length  what is the maximum length of the summary
   * @param boolean $keephtml  should HTML tags/entities be kept
   * @param boolean $compress  should we compress whitespace/newlines
   * @param unknown_type $tail  when the text is shortened, what should we append?
   * @return string  the summary
   */
  public function summary($string,$length,$keephtml=false,$compress=false,$tail=' ...')
  {
    if (!$keephtml) {
      $string=html_entity_decode($string,ENT_COMPAT,'UTF-8');
      $string=strip_tags($string);
    }
    if ($compress) {
      $string=preg_replace('/\s+/',' ',$string);
    }
    if (strlen($string) > $length) { 
      $string = substr($string, 0, $length); 
      $last_space = strrpos($string, ' '); 
      $string = substr($string, 0, $last_space);  
      $string .= $tail; 
    } 
    return $string;
  }

  /**
   * Convert a text to a key for html like an ID or a class. 
   * 
   * This function strips any other sign as a-z A-Z 0-9 and a space. All spaces are converted to underscores. 
   *
   * @param string $key
   * @return string
   */
  public function makeHTMLKeySafe($key){
    return str_replace(' ', '_', strtolower(preg_replace("/[^A-Za-z0-9 ]/", '', $key)));
  }
  
}
?>