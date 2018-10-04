<?
global $DB, $MESS, $APPLICATION;

use Bitrix\Main;
use Bitrix\Main\Entity;

CModule::IncludeModule('iblock');
CModule::IncludeModule('sale');
CModule::IncludeModule('catalog');

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

/*if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
    $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $redirect");
}*/

$change='';

if(strpos($_SERVER['REQUEST_URI'],'index.php')){
    $_SERVER['REQUEST_URI']=str_replace("index.php/","",$_SERVER['REQUEST_URI']);
    $change='1';
}
if(substr($_SERVER['REQUEST_URI'],0,2)=='//'){
    $_SERVER['REQUEST_URI']=str_replace("//","/",$_SERVER['REQUEST_URI']);
    $change='1';
}
if(strpos($_SERVER['REQUEST_URI'],'//')){
    $_SERVER['REQUEST_URI']=str_replace("//","/",$_SERVER['REQUEST_URI']);
    $change='1';
}
if(substr($_SERVER['REQUEST_URI'],0,2)=='//'){
    $_SERVER['REQUEST_URI']=substr($_SERVER['REQUEST_URI'],1);
    $change='1';
}
if(strpos($_SERVER['REQUEST_URI'],'//')){
    $_SERVER['REQUEST_URI']=str_replace("//","/",$_SERVER['REQUEST_URI']);
    $change='1';
}
if($change=='1'){
    if(strpos($_SERVER['REQUEST_URI'],'index.php')){
        $_SERVER['REQUEST_URI']=str_replace("index.php","",$_SERVER['REQUEST_URI']);
    }
    header("Location: https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],TRUE,301);
}

// Пользовательские переменные
\Bitrix\Main\Loader::includeModule('ceteralabs.uservars');

Bitrix\Main\Loader::registerAutoLoadClasses(null, [
    'Sf\\Base'                      => '/local/classes/Base.php',
    'Sf\\User'                      => '/local/classes/User.php',
    'Sf\\Favorites'                 => '/local/classes/Favorites.php',
    'Sf\\Helper'                    => '/local/classes/Helper.php',
    'Sf\\ReferenceBookGroupTable'   => '/local/classes/ReferenceBookGroupTable.php',
    'Sf\\ReferenceBookDataTable'    => '/local/classes/ReferenceBookDataTable.php',
    'Sf\\StockTable'                => '/local/classes/StockTable.php',
    'Sf\\PriceTable'                => '/local/classes/PriceTable.php',
    'Sf\\CFile'                     => '/local/classes/CFile.php',
    'Sf\\SampleTable'               => '/local/classes/SampleTable.php',
    'Sf\\Handler'                   => '/local/classes/Handler.php',
]);

/**
 * Обработка ошибок на front
 */
AddEventHandler("main", "OnEpilog", "handler404");
function handler404() {

    if (defined('ERROR_404') && ERROR_404 == 'Y' && !defined('ADMIN_SECTION')) {

        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        CHTTP::SetStatus("404 Not Found");

        include($_SERVER["DOCUMENT_ROOT"]."/local/templates/sf2v/header.php");

        if (file_exists($_SERVER['DOCUMENT_ROOT'].'/404.php')) {
            include $_SERVER['DOCUMENT_ROOT'].'/404.php';
        }

        include($_SERVER["DOCUMENT_ROOT"]."/local/templates/sf2v/footer.php");
    }
}

/*
 * Дополнительные свойства в письме
 */
AddEventHandler("sale", "OnOrderNewSendEmail", "OnOrderNewSendEmailHandler");
function OnOrderNewSendEmailHandler($iOrderId, &$eventName, &$arFields)
{
    $arOrder = (new CSaleOrder)->GetByID($iOrderId);

    if ($arOrder['DELIVERY_ID'] == 3) {
        $arFields['DELIVERY_INFO'] = 'Информация по доставке : ' . $arOrder['USER_DESCRIPTION'];
    }
}

$eventManager = Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('catalog', 'OnGetOptimalPrice', function($productID, $quantity = 1, $arUserGroups = array(), $renewal = "N", $arPrices = array(), $siteID = "s2", $arDiscountCoupons = false){

    $arTypePrice = \Sf\PriceTable::getCurrentPriceType($_REQUEST);

    $arFilter = [
        "PRODUCT_ID"        => $productID,
        "CATALOG_GROUP_ID"  => $arTypePrice['ID']
    ];

    $db_res = CPrice::GetList([], $arFilter);
    if ($ar_res = $db_res->Fetch()) {

        $price = $ar_res['PRICE'];
        $currency = $ar_res['CURRENCY'];
        $arResult = array(
            'PRICE' => array(
                'PRICE' => $price,
                'CURRENCY' => $currency,
            )
        );
        $arDiscounts = CCatalogDiscount::GetDiscount($productID, 2);
        // ID Инфоблока с торговыми предложениями (в данном случае)
        if ($arDiscounts) {
            foreach ($arDiscounts as $arDiscount) {
                $arResult['DISCOUNT_LIST'][] = array(
                    'VALUE_TYPE' => $arDiscount['VALUE_TYPE'],
                    'VALUE' => $arDiscount['VALUE'],
                    'CURRENCY' => $arDiscount['CURRENCY']
                );
            }
        }
    } else {
        return true;
    }
    return $arResult;
});

$eventManager->addEventHandler("sale", "OnSaleStatusOrder", array("\Sf\Handler", "OnSaleStatusOrder"));

$eventManager->addEventHandler('main', 'OnPageStart', function() {

    global $APPLICATION;

    if (strpos($APPLICATION->GetCurDir(), '/devel/') !== false) {
        return true;
    }

    if (strpos($APPLICATION->GetCurDir(), 'm=Y') !== false) {
        return true;
    }

    $sOldVersion    = $APPLICATION->get_cookie('SITE_VERSION');
    $sVersion       = $_REQUEST['VERSION'];

    if (!empty($sVersion) && in_array($sVersion, ['MOBILE', 'DESKTOP'])) {
        $APPLICATION->set_cookie('SITE_VERSION', $sVersion);
    } elseif (empty($APPLICATION->get_cookie('SITE_VERSION'))) {
        if (\Sf\Base::isMobileVersion()) {
            $APPLICATION->set_cookie('SITE_VERSION', 'MOBILE');
        } else {
            $APPLICATION->set_cookie('SITE_VERSION', 'DESKTOP');
        }

        if ($APPLICATION->get_cookie('SITE_VERSION')) {
            LocalRedirect($APPLICATION->GetCurPage() . '?m=Y');
        }
    }

    if (!empty($sVersion) && $sVersion != $sOldVersion) {
        LocalRedirect($APPLICATION->GetCurPage());
    }
});