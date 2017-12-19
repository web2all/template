<?php
// requires third part class "Savant2"
Web2All_Manager_ClassInclude::loadClassname('Savant2','PEAR','Savant2');

/**
 * @name Web2All_Template_Savant
 * This class is a wrapper for the Savant template system
 * http://phpsavant.com/yawiki/index.php?area=Savant2&page=HomePage
 * 
 * Requires externals:
 * include  Savant2 svn://subversion.intra.web2all.nl/web2all_std/trunk/include/Savant2
 * 
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2007-2010 Web2All B.V.
 * @version 0.1
 * @since 2007-07-03
 */
class Web2All_Template_Engine_Savant extends Savant2 { // implements Web2All_Manager_Plugin
  /**
   * @var Web2All_Manager_Main
   */
  protected $Web2All;
  
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
      'modifiermapping' => array()
    );
    
    $this->config=$this->Web2All->Config->makeConfig('Web2All_Template_Engine_Savant',$defaultconfig);
    
    $this->addPath('template', $include_path.$this->config['template_path']);
    
    $this->setEscape(array('Web2All_Template_Engine_Savant','htmlspecialcharsUTF'),'nl2br');
    
    // default to exceptions error handling
    $this->setError('exception');

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
    // First check if a custom modifier exists.
    if (array_key_exists($modifier, $this->config['modifiermapping'])) {
      // Ok, we found a custom modifier. So load this one instead.
      return $this->Web2All->Plugin->{$this->config['modifiermapping'][$modifier]}();
    }
    // Otherwise load the default modifier.
    return $this->Web2All->Plugin->{'Web2All_Template_Modifiers_'.$modifier.'_Modifier'}();
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
  
  
}

?>