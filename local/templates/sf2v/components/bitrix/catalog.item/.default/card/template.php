<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;


if (!empty($item['PROPERTIES']['PRODUCT_SUBTYPE']['VALUE'])) {

    $obProductSubtype = CIBlockElement::GetList([], [
            'IBLOCK_ID' => \Sf\Helper::IBLOCK_ITEM_SUBTYPES_ID,
            'ID' => $item['PROPERTIES']['PRODUCT_SUBTYPE']['VALUE']]
    );

    if ($obProductSubtype->SelectedRowsCount()) {
        $arProductSubtype = $obProductSubtype->Fetch();
    }
}

$arResult['SECTIONS'] = \Sf\Helper::getCatalogSections();

if (!empty($arResult['SECTIONS'][$item['IBLOCK_SECTION_ID']]['UF_SINGULAR'])) {
    $item['NAME'] = $arResult['SECTIONS'][$item['IBLOCK_SECTION_ID']]['UF_SINGULAR'] . ' ' . $item['NAME'];
}

$item['DETAIL_PAGE_URL'] = str_replace('#SECTION#', $arResult['SECTIONS'][$item['IBLOCK_SECTION_ID']]['CODE'], $item['DETAIL_PAGE_URL']);

?>

<tr>
    <td class="catalog-sketch">
        <a style="background: url(<?=CFile::GetPath($arProductSubtype['PREVIEW_PICTURE'])?>) center center no-repeat; background-size: 100%;" href="#"></a>
    </td>
    <td class="catalog-preview">
        <a
                href="<?=$item['DETAIL_PAGE_URL']?>"
                style="background: url(<?=$item['PREVIEW_PICTURE']['SRC']?>) center center no-repeat;"></a>
    </td>
    <td class="catalog-name right-line">
        <a href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a>
        <div class="stars catalog-stars">
            <select class="section-main-page-rating-stars" name="rating" autocomplete="off">
                <option <?if($item['PROPERTIES']['RATING']['VALUE']==1):?>selected<?endif;?> value="1">1</option>
                <option <?if($item['PROPERTIES']['RATING']['VALUE']==2):?>selected<?endif;?> value="2">2</option>
                <option <?if($item['PROPERTIES']['RATING']['VALUE']==3):?>selected<?endif;?> value="3">3</option>
                <option <?if($item['PROPERTIES']['RATING']['VALUE']==4):?>selected<?endif;?> value="4">4</option>
                <option <?if($item['PROPERTIES']['RATING']['VALUE']==5):?>selected<?endif;?> value="5">5</option>
            </select>
        </div>
        <div class="catalog-preview-text">
            <?
            if ($item['PREVIEW_TEXT']) {
                $sPreviewShort = substr($item['PREVIEW_TEXT'], 0, 40);
                $sPreviewFull = substr($item['PREVIEW_TEXT'], 40, 200);
                ?>
                <?=$sPreviewShort?><a class="catalog-preview-text-more" href="#"> ...</a>
                <span><?=$sPreviewFull?></span>
                <?
            }
            ?>
        </div>
    </td>
    <td class="catalog-quantity right-line">
        <?
        $iCountStars = \Sf\Helper::getCountStarsByQuantity($actualItem['CATALOG_QUANTITY']);
        ?>

        <?if ($iCountStars > 0):?>
            <select class="catalog-many star<?=$iCountStars?>" name="rating" autocomplete="off">
                <option <?if($iCountStars==1):?>selected<?endif;?> value="1">1</option>
                <option <?if($iCountStars==2):?>selected<?endif;?> value="2">2</option>
                <option <?if($iCountStars==3):?>selected<?endif;?> value="3">3</option>
                <option <?if($iCountStars==4):?>selected<?endif;?> value="4">4</option>
                <option <?if($iCountStars==5):?>selected<?endif;?> value="5">5</option>
            </select>
        <?else:?>
            под заказ
        <?endif;?>

    </td>
    <?
    $sSectionCode = $arParams['SECTION_CODE']

    ?><?if ($sSectionCode == 'podshipnik' || $sSectionCode == 'podshipnik' || $sSectionCode == 'manzheta'):?>
        <td class="catalog-ch right-line"><?=$item['PROPERTIES']['vnutrenniydiametrdmm']['VALUE'] ? $item['PROPERTIES']['vnutrenniydiametrdmm']['VALUE'] : '-'?></td>
        <td class="catalog-ch right-line"><?=$item['PROPERTIES']['naruzhnyydiametrdmm']['VALUE'] ? $item['PROPERTIES']['naruzhnyydiametrdmm']['VALUE'] : '-'?></td>
        <td class="catalog-ch right-line"><?=$item['PROPERTIES']['shirinabmm']['VALUE'] ? $item['PROPERTIES']['shirinabmm']['VALUE'] : '-'?></td>
    <?elseif ($sSectionCode == 'remen'):?>
        <td class="catalog-ch right-line"><?=$item['PROPERTIES']['vnutrennyayadlinali']['VALUE'] ? $item['PROPERTIES']['vnutrennyayadlinali']['VALUE'] : '-'?></td>
        <td class="catalog-ch right-line"><?=$item['PROPERTIES']['vneshnyayadlinalo']['VALUE'] ? $item['PROPERTIES']['vneshnyayadlinalo']['VALUE'] : '-'?></td>
        <td class="catalog-ch right-line"><?=$item['PROPERTIES']['rabochayaraschyetnayadlinalwlp']['VALUE'] ? $item['PROPERTIES']['rabochayaraschyetnayadlinalwlp']['VALUE'] : '-'?></td>
    <?endif;?>
    <td class="catalog-price right-line">
        <?
        if (!empty($price))
        {
            if ($arParams['PRODUCT_DISPLAY_MODE'] === 'N' && $haveOffers)
            {
                echo Loc::getMessage(
                    'CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE',
                    array(
                        '#PRICE#' => $price['PRINT_RATIO_PRICE'],
                        '#VALUE#' => $measureRatio,
                        '#UNIT#' => $minOffer['ITEM_MEASURE']['TITLE']
                    )
                );
            }
            else
            {
                if ($price['BASE_PRICE'] < 1) {
                    echo 'уточняйте';
                } else {
                    echo $price['PRINT_RATIO_PRICE'];
                }
            }
        }
        ?>
    </td>
    <td class="catalog-quantity right-line">
        <input type="text" readonly name="quantity" value="<?=$item['PROPERTIES']['PACKAGED']['VALUE'] ? $item['PROPERTIES']['PACKAGED']['VALUE'] : 1?>"></td>

    <td class="catalog-compare">
        <a
                data-product-id="<?=$item['ID']?>"
                class="catalog-compareLink <?if (in_array($item['ID'], (new \Sf\Base())->getCompareIds())):?>active<?endif;?>"
                title="Добавить в сравнение"
                href="#">

        </a>
        <a
                data-product-id="<?=$item['ID']?>"
                class="catalog-favoritesLink <?if (in_array($item['ID'], (new \Sf\Favorites)->getIds())):?>active<?endif;?>"
                title="Добавить в избранное"
                href="#">

        </a>
        <a
                data-product-id="<?=$item['ID']?>"
                data-quantity="1"
                class="addToCart"
                title="Добавить в корзину"
                href="#">
            <span></span>
        </a>
    </td>
</tr>
