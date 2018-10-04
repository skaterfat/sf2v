<?php
namespace Sf;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Service\GeoIp;

Loc::loadMessages(__FILE__);

/**
 * Class Table
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> UF_DATE_CREATE datetime optional
 * <li> UF_DATE_UPDATE datetime optional
 * <li> UF_PRODUCT_ID int optional
 * <li> UF_PRICE_ID int optional
 * <li> UF_PRICE double optional
 * </ul>
 *
 * @package Bitrix\
 **/

class PriceTable extends Main\Entity\DataManager
{

    /**
     * Идентификатор базового типа цен
     */
    const PRICE_TYPE_BASE = 1;

    /**
     * Опт 1
     */
    const PRICE_TYPE_OPT_1 = 2;

    /**
     * Опт 2
     */
    const PRICE_TYPE_OPT_2 = 3;

    /**
     * Опт 3
     */
    const PRICE_TYPE_OPT_3 = 4;

    /**
     * Опт 4
     */
    const PRICE_TYPE_OPT_4 = 5;

    /**
     * Опт 5
     */
    const PRICE_TYPE_OPT_5 = 6;

    /**
     * Опт 6
     */
    const PRICE_TYPE_OPT_6 = 7;

    /**
     * Цена для АВК
     */
    const PRICE_TYPE_OPT_AVK = 8;

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */

    static $MULTIPRICE = false;

    static $RATING_PRICES = Array();

    public static function getTableName()
    {
        return 'prices';
    }

    /**
     * @var array
     */
    public static $arPriceMapping = [
        3276 => self::PRICE_TYPE_BASE,    //Розничная
        3277 => self::PRICE_TYPE_OPT_1,   // Опт 1
        3278 => self::PRICE_TYPE_OPT_2,   // Опт 2
        3279 => self::PRICE_TYPE_OPT_3,   // Опт 3
        3280 => self::PRICE_TYPE_OPT_4,   // Опт 4
        3281 => self::PRICE_TYPE_OPT_5,   // Опт 5
        3282 => self::PRICE_TYPE_OPT_6,   // Опт 6
        3283 => self::PRICE_TYPE_OPT_AVK, // ЦеныдляАВК
    ];

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('_ENTITY_ID_FIELD'),
            ),
            'UF_DATE_CREATE' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('_ENTITY_UF_DATE_CREATE_FIELD'),
            ),
            'UF_DATE_UPDATE' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('_ENTITY_UF_DATE_UPDATE_FIELD'),
            ),
            'UF_PRODUCT_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('_ENTITY_UF_PRODUCT_ID_FIELD'),
            ),
            'UF_PRICE_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('_ENTITY_UF_PRICE_ID_FIELD'),
            ),
            'UF_PRICE' => array(
                'data_type' => 'float',
                'title' => Loc::getMessage('_ENTITY_UF_PRICE_FIELD'),
            ),
        );
    }

    /**
     * Получение цены товарва в зависимости от региона
     * @param $iProductId
     * @return bool
     * @throws Main\ArgumentException
     */
    public static function getProductPriceByRegion($iProductId)
    {
        $arResult = [];

        $arLocation = Base::getCurrentLocation();

        if (empty($arLocation['PRICE_TYPE'])) {
            return false;
        }

        if (empty($iProductId)) {
            return false;
        }

        $arFilter = [
            'filter' => [
                'UF_PRODUCT_ID' => $iProductId
            ]
        ];

        $obCDBResult = self::getList($arFilter);
        while ($arData = $obCDBResult->fetch()) {
            $arResult[$arData['UF_PRICE_ID']] = $arData;
        }

        $arPriceRegion = $arResult[$arLocation['PRICE_TYPE']];

        if (empty($arPriceRegion['UF_PRICE'])) {
            return false;
        }

        return $arPriceRegion['UF_PRICE'];
    }

    /**
     * Установить цену с учетом ТИПА цены
     * @param $iProductId
     * @param $sPrice
     * @param int $iPriceId
     * @return bool
     */
    public function setPriceByType($iProductId, $sPrice, $iPriceId = self::PRICE_TYPE_BASE)
    {
        if (empty($iProductId)) {
            return false;
        }

        if (empty($sPrice)) {
            return false;
        }

        $arFields = [
            "PRODUCT_ID"        => $iProductId,
            "CATALOG_GROUP_ID"  => $iPriceId,
            "PRICE"             => $sPrice,
            "CURRENCY"          => "RUB",
        ];

        $res = \CPrice::GetList(
            [],
            [
                "PRODUCT_ID" => $iProductId,
                "CATALOG_GROUP_ID" => $iPriceId
            ]
        );

        if ($res->SelectedRowsCount()) {

            while ($arr = $res->Fetch()) {
                \CPrice::Update($arr['ID'], $arFields);
            }

        } else {
            \CPrice::Add($arFields);
        }
    }

    /**
     * Получаем тип цины в зависимости от заложенной логики
     * @param array $arParams
     * @return mixed
     */
    public static function getCurrentPriceType($arParams = [])
    {
        $arBasePrice = [];

        $arParams['LOCATION_ID']    = (int)$arParams['LOCATION_ID'];
        $arParams['utm_term']       = htmlspecialcharsEx($arParams['utm_term']);

        // Типы цен (Интернет-магазин)
        $obCDBResult = \CCatalogGroup::GetList([], []);
        while ($arData = $obCDBResult->Fetch()) {

            if ($arData['BASE'] == 'Y') {

                $arBasePrice = [
                    'ID'    => $arData['ID'],
                    'NAME'  => $arData['NAME'],
                ];

            }

            $arResult['PRICES'][$arData['ID']] = $arData['NAME'];
        }

        // Если клиент ранее заходил с рекламной компании Yandex Market - ищем cookie для типа цены
        $sPriceTypeDefault = Base::App()->get_cookie('PRICE_TYPE_DEFAULT');

        if (!empty($sPriceTypeDefault)) {
            if (in_array($sPriceTypeDefault, $arResult['PRICES'])) {
                return [
                    'ID'    => array_flip($arResult['PRICES'])[$sPriceTypeDefault],
                    'NAME'  => $sPriceTypeDefault,
                ];
            }
        }

        // Если "базовый" тип цены отсутствует - выходим
        if (!$arBasePrice) {
            return false;
        }

        // Типы цен (Инфоблок)
        $arSelect = ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_MARKET_COMPANY'];
        $obCDBResult = \CIBlockElement::GetList([], ['IBLOCK_ID' => \Sf\Helper::IBLOCK_PRICES_ID], false, false, $arSelect);
        while ($obData = $obCDBResult->GetNextElement()) {
            $arData = $obData->GetFields();
            $arData['PROP'] = $obData->GetProperties();

            $arResult['PRICE_BLOCK'][$arData['ID']] = [
                'NAME' => $arData['NAME'],
            ];

            if (!empty($arData['PROP']['MARKET_COMPANY']['VALUE'])) {
                $arResult['PRICE_BLOCK'][$arData['ID']] = $arData['PROP']['MARKET_COMPANY']['VALUE'];
            }
        }

        /**
         * Если передан LOCATION_ID и название компании - подставляем необходимый тип цены
         */
        if (!empty($arParams['LOCATION_ID']) && !empty($arParams['utm_term'])) {

            foreach ($arResult['PRICE_BLOCK'] as $iPriceBlockId => $arPriceBlock) {

                if (in_array($arParams['utm_term'] , $arPriceBlock)) {

                    if (self::$arPriceMapping[$iPriceBlockId]
                        && $arResult['PRICES'][self::$arPriceMapping[$iPriceBlockId]]) {

                        // Устанавливаем COOKIE на неделю
                        Base::App()->set_cookie('PRICE_TYPE_DEFAULT',
                            $arResult['PRICES'][self::$arPriceMapping[$iPriceBlockId]], time()+60*60*24*7);

                        return [
                            'ID'    => self::$arPriceMapping[$iPriceBlockId],
                            'NAME'  => $arResult['PRICES'][self::$arPriceMapping[$iPriceBlockId]],
                        ];
                    }
                }
            }
        }

        //$ipAddress = GeoIp\Manager::getRealIp();
        $ipAddress = '93.181.239.35';
        $dbGeoResult = GeoIp\Manager::getDataResult($ipAddress, "en");

        if($dbGeoResult) {

            //global $REGION_YAROSLAVL;
            //$REGION_YAROSLAVL = false;

            if($dbGeoResult->isSuccess()) {

                $GEOLOCATION = $dbGeoResult->getGeoData();

                //echo '<pre>';
                //print_r($GEOLOCATION->regionName);
                //echo '</pre>';

                if($GEOLOCATION->regionName == "Yaroslavskaya Oblast'") {
                    self::$MULTIPRICE = true;
                    self::$RATING_PRICES = Array(
                        5 => 2, //OPT1
                        4 => 3, //OPT2
                        3 => 4, //OPT3
                        2 => 4, //OPT3
                        1 => 4 //OPT3
                    );
                    return ['BASE', 'OPT1', 'OPT2', 'OPT3'];
                }

            }

        }

        /*
         * Если геотаргетинг определил пользователя с Ярославля - показываем ему определенный тип цен
         */
       /* $arIpInfo = Base::geoIpInfo();
        if ($arIpInfo['city'] == 'Ярославль') {
            return [
                'ID'    => PriceTable::PRICE_TYPE_OPT_3,
                'NAME'  => 'OPT3',
            ];
        }*/

        return $arBasePrice;
    }
}