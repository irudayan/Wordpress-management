<?php
/**
 * File for class ColissimoAFStructGenerateBordereauParcelNumberList
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoAFStructGenerateBordereauParcelNumberList originally named generateBordereauParcelNumberList
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl}
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFStructGenerateBordereauParcelNumberList extends ColissimoAFWsdlClass
{
    /**
     * The parcelsNumbers
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * @var string
     */
    public $parcelsNumbers;
    /**
     * Constructor method for generateBordereauParcelNumberList
     * @see parent::__construct()
     * @param string $_parcelsNumbers
     * @return ColissimoAFStructGenerateBordereauParcelNumberList
     */
    public function __construct($_parcelsNumbers = NULL)
    {
        parent::__construct(array('parcelsNumbers'=>$_parcelsNumbers),false);
    }
    /**
     * Get parcelsNumbers value
     * @return string|null
     */
    public function getParcelsNumbers()
    {
        return $this->parcelsNumbers;
    }
    /**
     * Set parcelsNumbers value
     * @param string $_parcelsNumbers the parcelsNumbers
     * @return string
     */
    public function setParcelsNumbers($_parcelsNumbers)
    {
        return ($this->parcelsNumbers = $_parcelsNumbers);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see ColissimoAFWsdlClass::__set_state()
     * @uses ColissimoAFWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return ColissimoAFStructGenerateBordereauParcelNumberList
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
