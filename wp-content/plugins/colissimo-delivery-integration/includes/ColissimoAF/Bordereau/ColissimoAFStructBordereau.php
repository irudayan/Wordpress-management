<?php
/**
 * File for class ColissimoAFStructBordereau
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoAFStructBordereau originally named bordereau
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl}
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFStructBordereau extends ColissimoAFWsdlClass
{
    /**
     * The bordereauDataHandler
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var base64Binary
     */
    public $bordereauDataHandler;
    /**
     * The bordereauHeader
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var ColissimoAFStructBordereauHeader
     */
    public $bordereauHeader;
    /**
     * Constructor method for bordereau
     * @see parent::__construct()
     * @param base64Binary $_bordereauDataHandler
     * @param ColissimoAFStructBordereauHeader $_bordereauHeader
     * @return ColissimoAFStructBordereau
     */
    public function __construct($_bordereauDataHandler = NULL,$_bordereauHeader = NULL)
    {
        parent::__construct(array('bordereauDataHandler'=>$_bordereauDataHandler,'bordereauHeader'=>$_bordereauHeader),false);
    }
    /**
     * Get bordereauDataHandler value
     * @return base64Binary|null
     */
    public function getBordereauDataHandler()
    {
        return $this->bordereauDataHandler;
    }
    /**
     * Set bordereauDataHandler value
     * @param base64Binary $_bordereauDataHandler the bordereauDataHandler
     * @return base64Binary
     */
    public function setBordereauDataHandler($_bordereauDataHandler)
    {
        return ($this->bordereauDataHandler = $_bordereauDataHandler);
    }
    /**
     * Get bordereauHeader value
     * @return ColissimoAFStructBordereauHeader|null
     */
    public function getBordereauHeader()
    {
        return $this->bordereauHeader;
    }
    /**
     * Set bordereauHeader value
     * @param ColissimoAFStructBordereauHeader $_bordereauHeader the bordereauHeader
     * @return ColissimoAFStructBordereauHeader
     */
    public function setBordereauHeader($_bordereauHeader)
    {
        return ($this->bordereauHeader = $_bordereauHeader);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see ColissimoAFWsdlClass::__set_state()
     * @uses ColissimoAFWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return ColissimoAFStructBordereau
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
