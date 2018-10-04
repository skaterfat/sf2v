<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if (!empty($arResult['ERROR'])):?>
    <div class="alert alert-danger"><?=$arResult['ERROR']?></div>
<?else:?>
    <div class="row">
        <div class="col-sm-3">
            <?
            //  МЕНЮ КАТАЛОГА
            \Sf\Base::App()->IncludeComponent(
                'sf2v:menu.catalog',
                '',
                [
                    'ID' => $_REQUEST['ID']
                ])
            ?>
        </div>
        <?
        $sPhoto = '/static/images/no_photo.png';

        if (!empty($arResult['PICTURE'])) {
            $sPhoto = CFile::GetPath($arResult['PICTURE']);
        }
        ?>
        <div class="col-sm-9 productDetail wrapper-wides">
            <div class="row">
                <h1 class="header"><?=$arResult['NAME']?></h1>
                <div class="col-sm-5">
                    <div
                            class="productDetail-sketch"
                            style="background: url(<?=$sPhoto?>) center center no-repeat; background-size: 100%;">
                    </div>
                </div>
                <div class="col-sm-7">
                    <?=$arResult['TEXT']?>
                    <div class="min-max-prices">
                        <?if (!empty($arResult['MIN_PRICE']) && !empty($arResult['MAX_PRICE'])):?>
                            <?=$arResult['MIN_PRICE']?> -  <?=$arResult['MAX_PRICE']?> р.
                        <?endif;?>
                    </div>
                </div>
            </div>
            <div class="row characteristics">
                <table class="table">
                    <?foreach ($arResult['PROPERTIES_FORMAT'] as $arProp):?>
                        <tr>
                            <td class="ch-name"><?=$arProp['NAME']?></td>
                            <td class="ch-value">
                                <?if (!empty($arProp['VALUES'])):?>
                                    <?if (count($arProp['VALUES']) == 1):?>
                                        <?=reset($arProp['VALUES'])?>
                                    <?elseif (min($arProp['VALUES']) == max($arProp['VALUES'])):?>
                                        <?=reset($arProp['VALUES'])?>
                                    <?else:?>
                                        <?=min($arProp['VALUES'])?> - <?=max($arProp['VALUES'])?>
                                    <?endif;?>
                                <?endif;?>
                            </td>
                        </tr>
                    <?endforeach;?>
                </table>
            </div>
            <div class="row">
                <div class="col-sm-12 wides-items">
                    <?

                    global $arWidesFilter;

                    $arItemIds = array_column($arResult['ITEMS'], 'ID');

                    if ($arItemIds) {
                        $arWidesFilter = [
                            'IBLOCK_ID'     => \Sf\Helper::IBLOCK_CATALOG_ID,
                            'ID'            => $arItemIds,
                            '!SECTION_ID'   => 9999999,
                            '>CATALOG_QUANTITY'     => 0,
                        ];

                        $intSectionID = \Sf\Base::App()->IncludeComponent(
                            "bitrix:catalog.section",
                            "",
                            array(
                                "SEO_TEXT"  => 'N', // Показывать ли сео текст в категории
                                "ITEM_LIST_SEO"  => 'Y', // Устанавливать ли заголовки из ITEMLIST
                                "ITEM_LIST"      => $arResult, // Передаем широкое соответствие в компонент

                                "SET_META_KEYWORDS"  => 'N',
                                "SET_META_DESCRIPTION"  => 'N',
                                "IBLOCK_ID" => \Sf\Helper::IBLOCK_CATALOG_ID,
                                "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                                "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                                "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                                "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                                "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                                "PROPERTY_CODE_MOBILE" => $arParams["LIST_PROPERTY_CODE_MOBILE"],
                                "META_KEYWORDS" => "N",
                                "META_DESCRIPTION" => "N",
                                "BROWSER_TITLE" => 'N',
                                "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                                "INCLUDE_SUBSECTIONS" => 'Y',
                                "BASKET_URL" => $arParams["BASKET_URL"],
                                "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                                "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                                "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                                "FILTER_NAME" => 'arWidesFilter',
                                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                "CACHE_TIME" => $arParams["CACHE_TIME"],
                                "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                "SET_TITLE" => 'N',
                                "MESSAGE_404" => 'N',
                                "SET_STATUS_404" => 'N',
                                "SHOW_404" => 'N',
                                "FILE_404" => '',
                                "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                                "PAGE_ELEMENT_COUNT" => 200,
                                "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                                "PRICE_CODE" => [
                                    0 => \Sf\PriceTable::getCurrentPriceType($_REQUEST)['NAME'],
                                ],
                                "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                                "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                                "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                                "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                                "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],


                                "SECTION_ID" => $arResult['IBLOCK_SECTION_ID'],
                                "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                                "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                                "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                                'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                                'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                                'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                                'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],

                                'LABEL_PROP' => $arParams['LABEL_PROP'],
                                'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
                                'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],
                                'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                                'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
                                'PRODUCT_BLOCKS_ORDER' => $arParams['LIST_PRODUCT_BLOCKS_ORDER'],
                                'PRODUCT_ROW_VARIANTS' => $arParams['LIST_PRODUCT_ROW_VARIANTS'],
                                'ENLARGE_PRODUCT' => $arParams['LIST_ENLARGE_PRODUCT'],
                                'ENLARGE_PROP' => isset($arParams['LIST_ENLARGE_PROP']) ? $arParams['LIST_ENLARGE_PROP'] : '',
                                'SHOW_SLIDER' => $arParams['LIST_SHOW_SLIDER'],
                                'SLIDER_INTERVAL' => isset($arParams['LIST_SLIDER_INTERVAL']) ? $arParams['LIST_SLIDER_INTERVAL'] : '',
                                'SLIDER_PROGRESS' => isset($arParams['LIST_SLIDER_PROGRESS']) ? $arParams['LIST_SLIDER_PROGRESS'] : '',

                                'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                                'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                                'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                                'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                                'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
                                'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                                'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
                                'MESS_SHOW_MAX_QUANTITY' => (isset($arParams['~MESS_SHOW_MAX_QUANTITY']) ? $arParams['~MESS_SHOW_MAX_QUANTITY'] : ''),
                                'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),
                                'MESS_RELATIVE_QUANTITY_MANY' => (isset($arParams['~MESS_RELATIVE_QUANTITY_MANY']) ? $arParams['~MESS_RELATIVE_QUANTITY_MANY'] : ''),
                                'MESS_RELATIVE_QUANTITY_FEW' => (isset($arParams['~MESS_RELATIVE_QUANTITY_FEW']) ? $arParams['~MESS_RELATIVE_QUANTITY_FEW'] : ''),
                                'MESS_BTN_BUY' => (isset($arParams['~MESS_BTN_BUY']) ? $arParams['~MESS_BTN_BUY'] : ''),
                                'MESS_BTN_ADD_TO_BASKET' => (isset($arParams['~MESS_BTN_ADD_TO_BASKET']) ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : ''),
                                'MESS_BTN_SUBSCRIBE' => (isset($arParams['~MESS_BTN_SUBSCRIBE']) ? $arParams['~MESS_BTN_SUBSCRIBE'] : ''),
                                'MESS_BTN_DETAIL' => (isset($arParams['~MESS_BTN_DETAIL']) ? $arParams['~MESS_BTN_DETAIL'] : ''),
                                'MESS_NOT_AVAILABLE' => (isset($arParams['~MESS_NOT_AVAILABLE']) ? $arParams['~MESS_NOT_AVAILABLE'] : ''),
                                'MESS_BTN_COMPARE' => (isset($arParams['~MESS_BTN_COMPARE']) ? $arParams['~MESS_BTN_COMPARE'] : ''),

                                'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
                                'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),
                                'BRAND_PROPERTY' => (isset($arParams['BRAND_PROPERTY']) ? $arParams['BRAND_PROPERTY'] : ''),

                                'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                                "ADD_SECTIONS_CHAIN" => "N",
                                'ADD_TO_BASKET_ACTION' => $basketAction,
                                'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                                'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
                                'COMPARE_NAME' => $arParams['COMPARE_NAME'],
                                'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                                'COMPATIBLE_MODE' => (isset($arParams['COMPATIBLE_MODE']) ? $arParams['COMPATIBLE_MODE'] : ''),
                                'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : '')
                            ),
                            $component
                        );
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
<?endif;?>
