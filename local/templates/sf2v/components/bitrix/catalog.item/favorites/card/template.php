<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

if (!empty($item['IBLOCK_SECTION_ID'])) {
    $arSection = CIBlockSection::GetList([], ['ID' => $item['IBLOCK_SECTION_ID']])->Fetch();

   if (!empty($arSection['CODE'])) {
       $item['DETAIL_PAGE_URL'] = '/catalog/' . $arSection['CODE'] . '/' . $item['CODE'] . ' .html';
   }
}

?>

<tr>
    <td class="catalog-sketch">
        <a href="#"></a>
    </td>
    <td class="catalog-preview">
        <a href="<?=$item['DETAIL_PAGE_URL']?>" style="background: url(<?=$item['PREVIEW_PICTURE']['SRC']?>) center center no-repeat;"></a>
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
    <td class="catalog-chs right-line">
       -
    </td>
    <td class="catalog-quantity right-line">
        <?
        echo $actualItem['CATALOG_QUANTITY'];
        ?>
    </td>
   <td class="catalog-price right-line">
       <?=$item['PROPERTIES']['PACKAGED']['VALUE'] ? $item['PROPERTIES']['PACKAGED']['VALUE'] : 1?>
    </td>
    <td class="catalog-quantity right-line">
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

    <td class="catalog-compare">
        <a
            data-product-id="<?=$item['ID']?>"
            class="catalog-delete compare-delete"
            title="Удалить из избранного"
            href="#">

        </a>
    </td>
</tr>
