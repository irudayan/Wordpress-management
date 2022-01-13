<?php
/**
 * File for class ColissimoAFStructBordereauResponse
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoAFStructBordereauResponse originally named bordereauResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl}
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFStructBordereauResponse extends ColissimoAFStructBaseResponse
{
    /**
     * The bordereau
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var ColissimoAFStructBordereau
     */
    public $bordereau;
    /**
     * Constructor method for bordereauResponse
     * @see parent::__construct()
     * @param ColissimoAFStructBordereau $_bordereau
     * @return ColissimoAFStructBordereauResponse
     */
    public function __construct($_bordereau = NULL)
    {
        ColissimoAFWsdlClass::__construct(array('bordereau'=>$_bordereau),false);
    }
    /**
     * Get bordereau value
     * @return ColissimoAFStructBordereau|null
     */
    public function getBordereau()
    {
        return $this->bordereau;
    }
    /**
     * Set bordereau value
     * @param ColissimoAFStructBordereau $_bordereau the bordereau
     * @return ColissimoAFStructBordereau
     */
    public function setBordereau($_bordereau)
    {
        return ($this->bordereau = $_bordereau);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see ColissimoAFWsdlClass::__set_state()
     * @uses ColissimoAFWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return ColissimoAFStructBordereauResponse
     */
    public static function __set_state(array $_array,$_className = __CLASS__)
    {
        return parent::__set_state($_array,$_className);
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
