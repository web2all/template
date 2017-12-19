<?php
// requires third part class "Savant2"
Web2All_Manager_ClassInclude::loadClassname('Savant3','PEAR','Savant3');

/**
 * @name Web2All_Template_Savant3
 * This class is a wrapper for the Savant3 template system
 * http://phpsavant.com/
 * 
 * Requires externals:
 * include  Savant3 svn://subversion.intra.web2all.nl/web2all_std/trunk/include/Savant3
 * 
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2011 Web2All B.V.
 * @since 2011-03-17
 */
class Web2All_Template_Engine_Savant3 extends Savant3 { // implements Web2All_Manager_Plugin
  /**
   * @var Web2All_Manager_Main
   */
  protected $Web2All;
  
  /**
   * Cached array of modifier objects
   * The key is the modifier shortname
   * 
   * @var array
   */
  protected $modifier_cache=array();
  
  /**
   * @var Array
   */
  protected $config;

  public function __construct(Web2All_Manager_Main $web2all) {
    parent::__construct();
    $this->Web2All = $web2all;
    
    $include_path=dirname(__FILE__).'/../../../';
    
    // now do some default configuring of Savant
    $defaultconfig=array(
      'template_path' => '../htdocs/templates/',
      'relaxed_templatedirs' => false, // set true when templates may be loaded from outside of the defined template paths
      'modifiermapping' => array()
    );
    
    $this->config=$this->Web2All->Config->makeConfig('Web2All_Template_Engine_Savant',$defaultconfig);
    
    $this->addPath('template', $include_path.$this->config['template_path']);
    
    $this->setEscape(array('Web2All_Template_Engine_Savant3','htmlspecialcharsUTF'),'nl2br');
    
    // default to exceptions error handling
    $this->setExceptions(true);

    $this->setRelaxedTemplateLocation($this->config['relaxed_templatedirs']);
  }

  /**
   * Function to escape and print userinput value for a textarea 
   *
   * Executes the following method over $value Web2All_Template_Engine_Savant3->htmlspecialcharsUTF()
   * 
   * @access public
   * 
   * @param $value The value to be escaped
   * @return void (prints result)
   */
  public function eTextarea($value){
    $this->eprint($value, array('Web2All_Template_Engine_Savant3','htmlspecialcharsUTF'));
  }
  
  /**
   * load and parse a keyword list in the given template and 
   * return them as key-value pairs in an assoc array.
   *
   * @param string $tpl  template name
   * @return array  assoc array containing the key/values
   */
  public function loadKeywords($tpl)
  {
    $keywordfile=$this->loadTemplate($tpl);
    
    return $this->parseKeywords(file_get_contents($keywordfile));
  }
  
  /**
   * parse a keyword string into an array
   * 
   * used internally by loadKeywords()
   *
   * @param string $keywordtext
   * @return array
   */
  public function parseKeywords($keywordtext)
  {
    $keywords=array();
    
    $lines=explode("\n",$keywordtext);
    foreach ($lines as $line) {
      $parts=explode('=',$line,2);
      if (count($parts)==2) {
        $keywords[trim($parts[0])]=trim($parts[1]);
      }
      
    }
    return $keywords;
  }
  
  
  /**
   * Convienience method to start a modifier object easely.
   * 
   * It is possible to load custom modifiers by adding a new modifier,
   * to the config:
   *   $Web2All_Template_Engine_Savant = array(
   *     'modifiermapping' => array(
   *       "short name" => "class name"
   *     )
   *   )
   * This way is it also possible to override default modifiers.
   *
   * @param string $modifier  string with the indentifying part of the modifier classname
   * @return mixed  modifier object
   */
  public function modifier($modifier)
  {
    // check if modifier already loaded
    if(!array_key_exists($modifier, $this->modifier_cache)){
      // its not loaded yet, lets add it to the cache
      // check if a custom modifier exists.
      if (array_key_exists($modifier, $this->config['modifiermapping'])) {
        // Ok, we found a custom modifier. So load this one instead.
        $this->modifier_cache[$modifier] = $this->Web2All->Plugin->{$this->config['modifiermapping'][$modifier]}();
      }else{
        // Otherwise load the default modifier.
        $this->modifier_cache[$modifier] = $this->Web2All->Plugin->{'Web2All_Template_Modifiers_'.$modifier.'_Modifier'}();
      }
    }
    // return cached modifier
    return $this->modifier_cache[$modifier];
  }

  /**
   * Wraps the parent assign method, because if error suppression is not on, the parent assign method triggers errors. 
   *
   * @param null $arg1
   * @param null $arg2
   * @return bool
   */
  public function assign($arg1 = null, $arg2 = null) {
    return parent::assign($arg1, $arg2);
  }

  /**
   * wrapper for htmlspecialchars in UTF-8 mode
   *
   * @param string $value
   * @return string  escaped inputstring
   */
  public static function htmlspecialcharsUTF($value)
  {
    return htmlspecialchars($value,ENT_COMPAT,'UTF-8');
  }
  
  /**
   * wrapper for htmlentities in UTF-8 mode
   *
   * @param string $value
   * @return string  escaped inputstring
   */
  public static function htmlentitiesUTF($value)
  {
    return htmlentities($value,ENT_COMPAT,'UTF-8');
  }
  
  // backwards compatibility methods for Savant2 below here
  
  /**
  *
  * Alias to eprint()
  * 
  * @access public
  * 
  * @param mixed $value The value to be escaped and printed.
  * 
  * @return void
  *
  */
  public function _($value)
  {
    $num = func_num_args();
    if ($num == 1) {
      echo $this->eprint($value);
    } else {
      $args = func_get_args();
      echo call_user_func_array(
        array($this, 'eprint'),
        $args
      );
    }
  }
  
  function loadTemplate($tpl = null, $setScript = false)
  {
    return $this->template($tpl);
  }
  
  /**
  * (copied from Savant2)
  * Gets the current value of one, many, or all assigned variables.
  * 
  * Never returns variables starting with an underscore; these are
  * reserved for internal Savant2 use.
  * 
  * @access public
  * 
  * @param mixed $key If null, returns a copy of all variables and
  * their values; if an array, returns an only those variables named
  * in the array; if a string, returns only that variable.
  * 
  * @return mixed If multiple variables were reqested, returns an
  * associative array where the key is the variable name and the 
  * value is the variable value; if one variable was requested,
  * returns the variable value only.
  * 
  */
  
  function getVars($key = null)
  {
    if (is_null($key)) {
      $key = array_keys(get_object_vars($this));
    }
    
    if (is_array($key)) {
      // return a series of vars
      $tmp = array();
      foreach ($key as $var) {
        if (substr($var, 0, 1) != '_' && isset($this->$var)) {
          $tmp[$var] = $this->$var;
        }
      }
      return $tmp;
    } else {
      // return a single var
      if (substr($key, 0, 1) != '_' && isset($this->$key)) {
        return $this->$key;
      }
    }
  }
  
}

?>