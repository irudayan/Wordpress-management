<?php
/**
 * File for the class which returns the class map definition
 * @package ColissimoAF
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * Class which returns the class map definition by the static method ColissimoAFClassMap::classMap()
 * @package ColissimoAF
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFClassMap
{
    /**
     * This method returns the array containing the mapping between WSDL structs and generated classes
     * This array is sent to the SoapClient when calling the WS
     * @return array
     */
    final public static function classMap()
    {
        return array (
  'CheckGenerateLabelRequestType' => 'ColissimoAFStructCheckGenerateLabelRequestType',
  'CheckGenerateLabelResponseType' => 'ColissimoAFStructCheckGenerateLabelResponseType',
  'GenerateLabelRequestType' => 'ColissimoAFStructGenerateLabelRequestType',
  'GenerateLabelResponseType' => 'ColissimoAFStructGenerateLabelResponseType',
  'GetListMailBoxPickingDatesResponseType' => 'ColissimoAFStructGetListMailBoxPickingDatesResponseType',
  'GetListMailBoxPickingDatesRetourRequestType' => 'ColissimoAFStructGetListMailBoxPickingDatesRetourRequestType',
  'GetProductInterRequestType' => 'ColissimoAFStructGetProductInterRequestType',
  'GetProductInterResponseType' => 'ColissimoAFStructGetProductInterResponseType',
  'Message' => 'ColissimoAFStructMessage',
  'address' => 'ColissimoAFStructAddress',
  'addressPCH' => 'ColissimoAFStructAddressPCH',
  'addressPickupLocation' => 'ColissimoAFStructAddressPickupLocation',
  'addressee' => 'ColissimoAFStructAddressee',
  'article' => 'ColissimoAFStructArticle',
  'baseResponse' => 'ColissimoAFStructBaseResponse',
  'belgiumLabel' => 'ColissimoAFStructBelgiumLabel',
  'bordereau' => 'ColissimoAFStructBordereau',
  'bordereauHeader' => 'ColissimoAFStructBordereauHeader',
  'bordereauResponse' => 'ColissimoAFStructBordereauResponse',
  'category' => 'ColissimoAFStructCategory',
  'checkGenerateLabel' => 'ColissimoAFStructCheckGenerateLabel',
  'checkGenerateLabelRequest' => 'ColissimoAFStructCheckGenerateLabelRequest',
  'checkGenerateLabelResponse' => 'ColissimoAFStructCheckGenerateLabelResponse',
  'codSenderAddress' => 'ColissimoAFStructCodSenderAddress',
  'codeVAS' => 'ColissimoAFStructCodeVAS',
  'contents' => 'ColissimoAFStructContents',
  'customsDeclarations' => 'ColissimoAFStructCustomsDeclarations',
  'elementVisual' => 'ColissimoAFStructElementVisual',
  'field' => 'ColissimoAFStructField',
  'fields' => 'ColissimoAFStructFields',
  'generateBordereauByParcelsNumbers' => 'ColissimoAFStructGenerateBordereauByParcelsNumbers',
  'generateBordereauByParcelsNumbersResponse' => 'ColissimoAFStructGenerateBordereauByParcelsNumbersResponse',
  'generateBordereauParcelNumberList' => 'ColissimoAFStructGenerateBordereauParcelNumberList',
  'generateLabel' => 'ColissimoAFStructGenerateLabel',
  'generateLabelRequest' => 'ColissimoAFStructGenerateLabelRequest',
  'generateLabelResponse' => 'ColissimoAFStructGenerateLabelResponse',
  'getBordereauByNumber' => 'ColissimoAFStructGetBordereauByNumber',
  'getBordereauByNumberResponse' => 'ColissimoAFStructGetBordereauByNumberResponse',
  'getListMailBoxPickingDates' => 'ColissimoAFStructGetListMailBoxPickingDates',
  'getListMailBoxPickingDatesResponse' => 'ColissimoAFStructGetListMailBoxPickingDatesResponse',
  'getListMailBoxPickingDatesRetourRequest' => 'ColissimoAFStructGetListMailBoxPickingDatesRetourRequest',
  'getProductInter' => 'ColissimoAFStructGetProductInter',
  'getProductInterRequest' => 'ColissimoAFStructGetProductInterRequest',
  'getProductInterResponse' => 'ColissimoAFStructGetProductInterResponse',
  'importerAddress' => 'ColissimoAFStructImporterAddress',
  'labelResponse' => 'ColissimoAFStructLabelResponse',
  'letter' => 'ColissimoAFStructLetter',
  'original' => 'ColissimoAFStructOriginal',
  'outputFormat' => 'ColissimoAFStructOutputFormat',
  'parcel' => 'ColissimoAFStructParcel',
  'pickupLocation' => 'ColissimoAFStructPickupLocation',
  'planPickup' => 'ColissimoAFStructPlanPickup',
  'planPickupRequest' => 'ColissimoAFStructPlanPickupRequest',
  'planPickupRequestType' => 'ColissimoAFStructPlanPickupRequestType',
  'planPickupResponse' => 'ColissimoAFStructPlanPickupResponse',
  'planPickupResponseType' => 'ColissimoAFStructPlanPickupResponseType',
  'returnAddressBelgium' => 'ColissimoAFStructReturnAddressBelgium',
  'routing' => 'ColissimoAFStructRouting',
  'sender' => 'ColissimoAFStructSender',
  'service' => 'ColissimoAFStructService',
  'site' => 'ColissimoAFStructSite',
  'swissLabel' => 'ColissimoAFStructSwissLabel',
  'xmlResponse' => 'ColissimoAFStructXmlResponse',
  'zoneCABRoutage' => 'ColissimoAFStructZoneCABRoutage',
  'zoneInfosRoutage' => 'ColissimoAFStructZoneInfosRoutage',
  'zoneRouting' => 'ColissimoAFStructZoneRouting',
);
    }
}
