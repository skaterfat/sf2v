<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arPriceCode = \Sf\PriceTable::getCurrentPriceType($_REQUEST);
$PRICE_CODE = isset($arPriceCode['NAME']) ? Array($arPriceCode['NAME']) : $arPriceCode;

if(\Sf\PriceTable::$MULTIPRICE && $arResult['ITEMS']) {

	$arResult['PRICES'] = Array();

	foreach($arResult['ITEMS'] as &$arItem) {

		$arPrices = GetCatalogProductPriceList($arItem['ID'], "SORT", "ASC");

		foreach($arPrices as $arP)
			if(\Sf\PriceTable::$RATING_PRICES[$arItem['PROPERTIES']['RATING']['VALUE']] == $arP['CATALOG_GROUP_ID'])
				$arResult['PRICES'][] = $arP['PRICE'];

	}

	if (!empty($arResult['PRICES'])) {

        if (count($arResult['PRICES']) > 0) {
            $arResult['MIN_PRICE'] = ceil(min($arResult['PRICES']));
            $arResult['MAX_PRICE'] = ceil(max($arResult['PRICES']));
        }

    }

}