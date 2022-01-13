<?php
/**
 * File for class ColissimoAFStructSwissLabel
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoAFStructSwissLabel originally named swissLabel
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl}
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFStructSwissLabel extends ColissimoAFWsdlClass
{
    /**
     * The injectionSite
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $injectionSite;
    /**
     * The signatureOption
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $signatureOption;
    /**
     * The codeSwissPost
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $codeSwissPost;
    /**
     * The swissParcelNumber
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $swissParcelNumber;
    /**
     * Constructor method for swissLabel
     * @see parent::__construct()
     * @param string $_injectionSite
     * @param boolean $_signatureOption
     * @param string $_codeSwissPost
     * @param string $_swissParcelNumber
     * @return ColissimoAFStructSwissLabel
     */
    public function __construct($_injectionSite = NULL,$_signatureOption = NULL,$_codeSwissPost = NULL,$_swissParcelNumber = NULL)
    {
        parent::__construct(array('injectionSite'=>$_injectionSite,'signatureOption'=>$_signatureOption,'codeSwissPost'=>$_codeSwissPost,'swissParcelNumber'=>$_swissParcelNumber),false);
    }
    /**
     * Get injectionSite value
     * @return string|null
     */
    public function getInjectionSite()
    {
        return $this->injectionSite;
    }
    /**
     * Set injectionSite value
     * @param string $_injectionSite the injectionSite
     * @return string
     */
    public function setInjectionSite($_injectionSite)
    {
        return ($this->injectionSite = $_injectionSite);
    }
    /**
     * Get signatureOption value
     * @return boolean|null
     */
    public function getSignatureOption()
    {
        return $this->signatureOption;
    }
    /**
     * Set signatureOption value
     * @param boolean $_signatureOption the signatureOption
     * @return boolean
     */
    public function setSignatureOption($_signatureOption)
    {
        return ($this->signatureOption = $_signatureOption);
    }
    /**
     * Get codeSwissPost value
     * @return string|null
     */
    public function getCodeSwissPost()
    {
        return $this->codeSwissPost;
    }
    /**
     * Set codeSwissPost value
     * @param string $_codeSwissPost the codeSwissPost
     * @return string
     */
    public function setCodeSwissPost($_codeSwissPost)
    {
        return ($this->codeSwissPost = $_codeSwissPost);
    }
    /**
     * Get swissParcelNumber value
     * @return string|null
     */
    public function getSwissParcelNumber()
    {
        return $this->swissParcelNumber;
    }
    /**
     * Set swissParcelNumber value
     * @param string $_swissParcelNumber the swissParcelNumber
     * @return string
     */
    public function setSwissParcelNumber($_swissParcelNumber)
    {
        return ($this->swissParcelNumber = $_swissParcelNumber);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see ColissimoAFWsdlClass::__set_state()
     * @uses ColissimoAFWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return ColissimoAFStructSwissLabel
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
