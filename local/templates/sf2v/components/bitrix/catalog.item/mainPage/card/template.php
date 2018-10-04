<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

if (!empty($item['IBLOCK_SECTION_ID'])) {
    $arSection = CIBlockSection::GetList([], ['ID' => $item['IBLOCK_SECTION_ID']])->Fetch();

   if (!empty($arSection['CODE'])) {
       $item['DETAIL_PAGE_URL'] = '/catalog/' . $arSection['CODE'] . '/' . $item['CODE'] . '.html';
   }
}

?>

<div class="col-sm-6 col-md-6 col-lg-3 section-main-page-item">
    <a
            class="section-main-page-img"
            style="background: url(<?=$item['PREVIEW_PICTURE']['SRC']?>) center center no-repeat;background-size: 100%;"
            href="<?=$item['DETAIL_PAGE_URL']?>">

    </a>
    <a class="section-main-page-name" href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a>
    <div class="section-main-page-rating">
        <div class="stars stars-example-css">
            <select class="section-main-page-rating-stars" name="rating" autocomplete="off">
                <option <?if($item['PROPERTIES']['RATING']['VALUE']==1):?>selected<?endif;?> value="1">1</option>
                <option <?if($item['PROPERTIES']['RATING']['VALUE']==2):?>selected<?endif;?> value="2">2</option>
                <option <?if($item['PROPERTIES']['RATING']['VALUE']==3):?>selected<?endif;?> value="3">3</option>
                <option <?if($item['PROPERTIES']['RATING']['VALUE']==4):?>selected<?endif;?> value="4">4</option>
                <option <?if($item['PROPERTIES']['RATING']['VALUE']==5):?>selected<?endif;?> value="5">5</option>
            </select>
        </div>
    </div>
    <div class="col-sm-7 section-main-page-price noppading">
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
    </div>
    <div class="col-sm-5">
        <a
                data-product-id="<?=$item['ID']?>"
                data-quantity="1"
                class="addToCart"
                href="#"><span></span>
        </a>
    </div>
</div>

