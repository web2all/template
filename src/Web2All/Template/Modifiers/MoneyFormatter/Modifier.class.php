<?php
/**
 * Template modifier for formatting an amount of money,
 * into a nicely formatted String.
 * 
 * There are two methods in this class:
 *  formatMoney: this one is used for Wonen Zuid but is not easy to use more generic
 *               due to param order and language definition
 *  moneyFormat: a newer method which attempts to replace the php money_format
 *               function. If you want to use totally custom formats, extend this class
 *               and override the initFormats() method, its called from constructor.
 * 
 * Example usage from template engine:
 * <?php $this->eprint($this->modifier('MoneyFormatter')->moneyFormat($this->order->totprice,'no_NO'));?>
 * 
 * Format e.g.:
 *   1.000.000,00
 * 
 * @author Mark Dohmen
 * @copyright (c) Copyright 2012-2014 Web2All B.V.
 * @since 2012-12-13
 */
class Web2All_Template_Modifiers_MoneyFormatter_Modifier extends Web2All_Manager_Plugin {
  
  /**
   * Associative array with per language:
   *  - Thousand separator (e.g.: 1.000.000).
   *  - Decimal separator (e.g.: 1.000,00).
   * 
   * @var Array
   */
  protected $languageData = array(
    "NL" => array("thousand_separator" => ".", "decimal_separator" => ","),
    "UK" => array("thousand_separator" => ",", "decimal_separator" => "."),
    "US" => array("thousand_separator" => ",", "decimal_separator" => ".")
  );
  
  /**
   * Associative array with per locale:
   *  - thousand_separator
   *  - decimal_separator
   * 
   * @var Web2All_Template_Modifiers_MoneyFormatter_FormatDefinition[]
   */
  protected $locale_formats;
  
  /**
   * constructor
   *
   * @param Web2All_Manager_Main $web2all
   */
  public function __construct(Web2All_Manager_Main $web2all) {
    parent::__construct($web2all);
    // set up money formatting for the moneyFormat() method.
    // You can override this method if you need something completely custom 
    $this->initFormats();
  }
  
  /**
   * Defines the locale formats for the moneyFormat() method.
   * 
   */
  public function initFormats() {
    // set up unique formats
    $this->locale_formats=array(
      'nl_NL' => new Web2All_Template_Modifiers_MoneyFormatter_FormatDefinition('EUR','€',',','.',true,' '),
      'en_GB' => new Web2All_Template_Modifiers_MoneyFormatter_FormatDefinition('GBP','£','.',',',true,' '),
      'en_US' => new Web2All_Template_Modifiers_MoneyFormatter_FormatDefinition('USD','$','.',',',true,' '),
      'no_NO' => new Web2All_Template_Modifiers_MoneyFormatter_FormatDefinition('NOK','kr',',',' ',true,' ')
    );
    // set up similar formats
    $this->locale_formats['nl']=$this->locale_formats['nl_NL'];
    $this->locale_formats['no']=$this->locale_formats['no_NO'];
    $this->locale_formats['fr_FR']=$this->locale_formats['nl_NL'];
    // ...
    // many more, add as needed
  }
  
  /**
   * Formats an amount of money for the given,
   * language.
   * 
   * @param Mixed $moneyAmount
   * @param int $decimalsAmount
   * @param String $language
   * @return Mixed
   */
  public function formatMoney($moneyAmount, $decimalsAmount = 2, $language = "NL") {
    
    // If the given language doesn't exists in our format,
    // array, set default NL.
    if (!array_key_exists($language, $this->languageData)) {
      $language = "NL";
    }
    
    // Add the formatted number.
    $moneyString = number_format(
      $moneyAmount, 
      $decimalsAmount, 
      $this->languageData[$language]['decimal_separator'], 
      $this->languageData[$language]['thousand_separator']
    );
    
    // And finally return the full money string.
    return $moneyString;
  }
  
  /**
   * Formats an amount of money for the given locale (UTF-8)
   * 
   * This is intended as a replacement for PHP money_format function.
   * The PHP function requires you to se set the locale (not thread safe and
   * can affect more than you want).
   * 
   * @param float $amount  amount of money
   * @param string $locale  in what locale should the currencty be displayed
   * @param string $include_symbol  (no|local|international) include a currency symbol in the output, defaults to 'local'
   * @param int $decimals  how much decimals to show, defaults to 2
   * @return string
   */
  public function moneyFormat($amount, $locale, $include_symbol = 'local', $decimals = 2) {
    
    // check if locale is defined. if not throw exception.
    if (!array_key_exists($locale, $this->locale_formats)) {
      throw new Exception('Web2All_Template_Modifiers_MoneyFormatter_Modifier->moneyFormat: there is no definition for the locale "'.$locale.'" yet!');
    }
    $definition=$this->locale_formats[$locale];
    // Add the formatted number.
    $money_string = number_format(
      $amount, 
      $decimals, 
      $definition->decimal_separator, 
      $definition->thousand_separator
    );
    
    // check if adding currency symbol
    if($include_symbol=='local'){
      if($definition->currency_symbol_front){
        $money_string=$definition->currency_symbol_local.$definition->currency_symbol_separator.$money_string;
      }else{
        $money_string.=$definition->currency_symbol_separator.$definition->currency_symbol_local;
      }
    }elseif($include_symbol=='international'){
      if($definition->currency_symbol_front){
        $money_string=$definition->currency_symbol_international.$definition->currency_symbol_separator.$money_string;
      }else{
        $money_string.=$definition->currency_symbol_separator.$definition->currency_symbol_international;
      }
    }
    
    // And finally return the full money string.
    return $money_string;
  }
}

/**
 * Class for storing format definitions
 * 
 * Only used for the Web2All_Template_Modifiers_MoneyFormatter_Modifier
 * 
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2014 Web2All B.V.
 * @since 2014-05-19
 */
class Web2All_Template_Modifiers_MoneyFormatter_FormatDefinition {
  
  /**
   * Character(s) to use as decimal separator
   * usually the . or ,
   * 
   * @var string
   */
  public $decimal_separator;
  
  /**
   * Character(s) to use as thousand separator
   * usually the . or ,
   * 
   * @var string
   */
  public $thousand_separator;
  
  /**
   * The currency symbol which is used in the country
   * eg. the uro sign or 'kr' in norway
   * 
   * @var string
   */
  public $currency_symbol_local;
  
  /**
   * The international currency code
   * This is used when multiple currencies are used on one page.
   * eg. EUR or NOK
   * 
   * @var string
   */
  public $currency_symbol_international;
  
  /**
   * Should the currency symbol be in front of the money.
   * If false then symbol is prepended.
   * 
   * @var boolean
   */
  public $currency_symbol_front;
  
  /**
   * Should there be a separator between the currency symbol and the money
   * And if so, what should it be.
   * usually a space
   * 
   * @var string
   */
  public $currency_symbol_separator;
  
  /**
   * constructor
   *
   * @param string $currency_symbol_international
   * @param string $currency_symbol_local
   * @param string $decimal_separator
   * @param string $thousand_separator
   * @param boolean $currency_symbol_front
   * @param string $currency_symbol_separator
   */
  public function __construct($currency_symbol_international,$currency_symbol_local,$decimal_separator,$thousand_separator,$currency_symbol_front,$currency_symbol_separator) {
    $this->decimal_separator=$decimal_separator;
    $this->thousand_separator=$thousand_separator;
    $this->currency_symbol_local=$currency_symbol_local;
    $this->currency_symbol_international=$currency_symbol_international;
    $this->currency_symbol_front=$currency_symbol_front;
    $this->currency_symbol_separator=$currency_symbol_separator;
  }
  
}
?>