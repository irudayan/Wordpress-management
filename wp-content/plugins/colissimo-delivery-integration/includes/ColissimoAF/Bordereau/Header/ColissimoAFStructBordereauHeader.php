<?php
/**
 * File for class ColissimoAFStructBordereauHeader
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoAFStructBordereauHeader originally named bordereauHeader
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl}
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFStructBordereauHeader extends ColissimoAFWsdlClass
{
    /**
     * The address
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $address;
    /**
     * The bordereauNumber
     * @var long
     */
    public $bordereauNumber;
    /**
     * The clientNumber
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $clientNumber;
    /**
     * The codeSitePCH
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var int
     */
    public $codeSitePCH;
    /**
     * The company
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $company;
    /**
     * The nameSitePCH
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $nameSitePCH;
    /**
     * The numberOfParcels
     * @var long
     */
    public $numberOfParcels;
    /**
     * The publishingDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var dateTime
     */
    public $publishingDate;
    /**
     * Constructor method for bordereauHeader
     * @see parent::__construct()
     * @param string $_address
     * @param long $_bordereauNumber
     * @param string $_clientNumber
     * @param int $_codeSitePCH
     * @param string $_company
     * @param string $_nameSitePCH
     * @param long $_numberOfParcels
     * @param dateTime $_publishingDate
     * @return ColissimoAFStructBordereauHeader
     */
    public function __construct($_address = NULL,$_bordereauNumber = NULL,$_clientNumber = NULL,$_codeSitePCH = NULL,$_company = NULL,$_nameSitePCH = NULL,$_numberOfParcels = NULL,$_publishingDate = NULL)
    {
        parent::__construct(array('address'=>$_address,'bordereauNumber'=>$_bordereauNumber,'clientNumber'=>$_clientNumber,'codeSitePCH'=>$_codeSitePCH,'company'=>$_company,'nameSitePCH'=>$_nameSitePCH,'numberOfParcels'=>$_numberOfParcels,'publishingDate'=>$_publishingDate),false);
    }
    /**
     * Get address value
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }
    /**
     * Set address value
     * @param string $_address the address
     * @return string
     */
    public function setAddress($_address)
    {
        return ($this->address = $_address);
    }
    /**
     * Get bordereauNumber value
     * @return long|null
     */
    public function getBordereauNumber()
    {
        return $this->bordereauNumber;
    }
    /**
     * Set bordereauNumber value
     * @param long $_bordereauNumber the bordereauNumber
     * @return long
     */
    public function setBordereauNumber($_bordereauNumber)
    {
        return ($this->bordereauNumber = $_bordereauNumber);
    }
    /**
     * Get clientNumber value
     * @return string|null
     */
    public function getClientNumber()
    {
        return $this->clientNumber;
    }
    /**
     * Set clientNumber value
     * @param string $_clientNumber the clientNumber
     * @return string
     */
    public function setClientNumber($_clientNumber)
    {
        return ($this->clientNumber = $_clientNumber);
    }
    /**
     * Get codeSitePCH value
     * @return int|null
     */
    public function getCodeSitePCH()
    {
        return $this->codeSitePCH;
    }
    /**
     * Set codeSitePCH value
     * @param int $_codeSitePCH the codeSitePCH
     * @return int
     */
    public function setCodeSitePCH($_codeSitePCH)
    {
        return ($this->codeSitePCH = $_codeSitePCH);
    }
    /**
     * Get company value
     * @return string|null
     */
    public function getCompany()
    {
        return $this->company;
    }
    /**
     * Set company value
     * @param string $_company the company
     * @return string
     */
    public function setCompany($_company)
    {
        return ($this->company = $_company);
    }
    /**
     * Get nameSitePCH value
     * @return string|null
     */
    public function getNameSitePCH()
    {
        return $this->nameSitePCH;
    }
    /**
     * Set nameSitePCH value
     * @param string $_nameSitePCH the nameSitePCH
     * @return string
     */
    public function setNameSitePCH($_nameSitePCH)
    {
        return ($this->nameSitePCH = $_nameSitePCH);
    }
    /**
     * Get numberOfParcels value
     * @return long|null
     */
    public function getNumberOfParcels()
    {
        return $this->numberOfParcels;
    }
    /**
     * Set numberOfParcels value
     * @param long $_numberOfParcels the numberOfParcels
     * @return long
     */
    public function setNumberOfParcels($_numberOfParcels)
    {
        return ($this->numberOfParcels = $_numberOfParcels);
    }
    /**
     * Get publishingDate value
     * @return dateTime|null
     */
    public function getPublishingDate()
    {
        return $this->publishingDate;
    }
    /**
     * Set publishingDate value
     * @param dateTime $_publishingDate the publishingDate
     * @return dateTime
     */
    public function setPublishingDate($_publishingDate)
    {
        return ($this->publishingDate = $_publishingDate);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see ColissimoAFWsdlClass::__set_state()
     * @uses ColissimoAFWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return ColissimoAFStructBordereauHeader
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
