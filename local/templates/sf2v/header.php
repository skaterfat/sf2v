<? use Sf\Base;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!doctype html>
<html>
<head>
    <title><?=$APPLICATION->ShowTitle(true)?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="cmsmagazine" content="2dcd14ba52b1fedf0886b777e98fb5c8" />
    <?$APPLICATION->AddHeadScript("/bitrix/js/main/ajax.js");?>
    <?$APPLICATION->ShowHead();?>
    <?if ($_REQUEST['PAGEN_1']):?>
        <link rel="canonical" href="https://<?=($_SERVER['SERVER_NAME'] . str_replace('index.php', '', \Sf\Base::App()->GetCurPage()))?>"/>
    <?elseif (!empty(\Sf\Base::App()->GetCurParam())):?>
        <link rel="canonical" href="https://<?=($_SERVER['SERVER_NAME'] . str_replace('index.php', '', \Sf\Base::App()->GetCurPage()))?>"/>
    <?else:?>
        <link rel="canonical" href="https://<?=($_SERVER['SERVER_NAME'] . str_replace('index.php', '', \Sf\Base::App()->GetCurPage()))?>"/>
    <?endif;?>
    <link rel="shortcut icon" href="favicon.ico?111" type="image/x-icon">
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div class="container-fluid">
    <div class="row header">
        <div class="col-sm-12">
            <div class="container">
                <div class="row header">
                    <div class="col-sm-3 col-xs-6">
                        <a class="header-logo" href="/"></a>
                        <a class="header-mail" href="mailto:<?=\Ceteralabs\UserVars::GetVar('email')['VALUE']?>"><?=\Ceteralabs\UserVars::GetVar('email')['VALUE']?></a>
                    </div>
                    <div class="col-sm-3 col-xs-6 visible-xs-6 wrapper-menu-profile-m">
                        <?if (\Sf\Base::User()->IsAuthorized()):?>
                            <a class="menu-profile" href="#"><span><?=\Sf\Base::User()->GetLogin()?></span></a>
                            <ul class="menu-profile-list">
                                <li><a href="/personal/">Личный кабинет</a></li>
                                <li><a href="/login/?logout=yes">Выход</a></li>
                            </ul>
                        <?else:?>
                            <div class="menu-profile-m">
                                <a class="show-authorization" href="#"><span>Войти</span></a> /
                                <a href="/login/?register=yes"><span>Регистрация</span></a>
                            </div>
                        <?endif;?>

                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12 hidden-xs header-phone">
                        <?
                        $arLocations = \Sf\Base::getLocations();

                        $arCurrentLocation = \Sf\Base::getCurrentLocation(['LOCATION_ID' => (int)$_REQUEST['LOCATION_ID']]);
                        ?>
                        <?if (empty(Base::App()->get_cookie('LOCATION_ID'))):?>
                            <div class="col-md-12 col-sm-12 col-xs-12 popupYouCity">
                                <span>Ваш город <strong><?=$arCurrentLocation['CITY']?></strong>?</span>
                                <a data-location-id = "<?=$arCurrentLocation['LOCATION_ID']?>" href="#" class="btn btn-info popup-city-yes">Да</a>
                                <a href="#" class="btn btn-info popup-city-no">Нет</a>
                            </div>
                        <?endif;?>
                        <div class="header-location-city">
                            <a class="menu-profile" href="#"><span><?=$arCurrentLocation['CITY']?></span></a>
                            <ul class="menu-profile-list">
                                <?foreach ($arLocations as $iLocationId => $sLocationName):?>
                                    <li><a href="<?=\Sf\Base::App()->GetCurPage() . '?LOCATION_ID='
                                        . $iLocationId?>"><?=$sLocationName?></a></li>
                                <?endforeach;?>
                            </ul>
                        </div>
                        <a href="tel:8<?=\Sf\User::PhoneNumberConvert($arCurrentLocation['PHONE'])?>">
                            <?=$arCurrentLocation['PHONE']?>
                            <span class="feedback-call"><i class="fa fa-mobile" aria-hidden="true"></i>Заказать звонок</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12 header-phone">
                        <a href="tel:8<?=\Sf\User::PhoneNumberConvert(\Ceteralabs\UserVars::GetVar('phoneRussia')['VALUE'])?>">
                            <?=\Ceteralabs\UserVars::GetVar('phoneRussia')['VALUE']?>
                            <span class="header-phone-sup">Бесплатно по всей России</span>
                        </a>
                    </div>
                    <?
                    $sPhoneMessengerConvert = \Sf\User::PhoneNumberConvert($arCurrentLocation['MESSENGER_PHONE']);
                    ?>
                    <div class="soc-block col-sm-6 col-xs-6 visible-xs">
                        <a rel="nofollow" class="whatsapp" href="whatsapp://send?phone=+7<?=$sPhoneMessengerConvert?>"></a>
                        <a rel="nofollow" class="viber" href="viber://chat?number=+7<?=$sPhoneMessengerConvert?>"></a>
                        <a rel="nofollow" class="telegram" href="tg://resolve?domain=7<?=$sPhoneMessengerConvert?>"></a>
                        <a rel="nofollow" class="facebook" href="tg://resolve?domain=7<?=$sPhoneMessengerConvert?>"></a>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3 hidden-xs header-phone">
                        <a href="tel:8<?=\Sf\User::PhoneNumberConvert($sPhoneMessengerConvert)?>">
                        <?=\Sf\User::getPhoneForView($sPhoneMessengerConvert)?>
                            <span class="header-phone-sup">Месенджер</span>
                        </a>
                        <div class="soc-block">
                            <a class="whatsapp" href="whatsapp://send?phone=+7<?=$sPhoneMessengerConvert?>"></a>
                            <a class="viber" href="viber://chat?number=+7<?=$sPhoneMessengerConvert?>"></a>
                            <a class="telegram" href="tg://resolve?domain=7<?=$sPhoneMessengerConvert?>"></a>
                            <a class="facebook" href="tg://resolve?domain=7<?=$sPhoneMessengerConvert?>"></a>
                        </div>
                    </div>
                </div>
            </div>

			<?/* ПРАЗДНИКИ - информационное сообщение ?>
            <div class="container">
                <div class="row new-year">
                    <div class="col-sm-12">
						<p> <b><span style="background: #ffff00;"> Режим работы в майские праздники:</span></b>
</br> <b> 05.05.2018</b> с <b>10</b> до <b>14 <span style="color: #ff0000;"> кроме</span> </b> филиала <b><span style="color: #ff0000;">Молодёжная</span> </b> и филиала в <b> <span style="color: #ff0000;">г. Клин</span> </b> (отключение электроэнергии), </br>
					<b> 07.05</b> с <b>9</b> до <b>18, </b> далее <b> 08.05 </b> с <b>9</b> до <b>17</b> (предпраздничный день на 1 час короче),  <span style="color: #ff0000;"> <b>09 мая ДЕНЬ ПОБЕДЫ</b> выходной </span> </br> <b> с 10.05.2018 </b>  с <b>9</b> до <b>18</b> в штатном режиме  </p>

                    </div>
                </div>
            </div>

			<?*/?>

            <div class="container">
                <div class="row menu">
                    <?$APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
                        "ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
                            "MENU_CACHE_TYPE" => "A",	// Тип кеширования
                            "MENU_CACHE_TIME" => "36000000",	// Время кеширования (сек.)
                            "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
                            "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
                            "MAX_LEVEL" => "1",	// Уровень вложенности меню
                            "CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
                            "USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
                            "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
                            "COMPONENT_TEMPLATE" => "top",
                            "DELAY" => "N",	// Откладывать выполнение шаблона меню
                        ),
                        false
                    );?>
                    <div class="col-sm-3 wrapper-menu-profile">
                        <?if (\Sf\Base::User()->IsAuthorized()):?>
                            <a class="menu-profile" href="#"><span><?=\Sf\Base::User()->GetLogin()?></span></a>
                            <ul class="menu-profile-list">
                                <li><a href="/personal/">Личный кабинет</a></li>
                                <li><a href="/login/?logout=yes">Выход</a></li>
                            </ul>
                        <?else:?>
                            <a class="menu-profile" href="#"><span>Профиль</span></a>
                            <ul class="menu-profile-list">
                                <li><a class="show-authorization" href="#">Вход</a></li>
                                <li><a href="/login/?register=yes">Регистрация</a></li>
                            </ul>
                        <?endif;?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row search">
        <div class="container">
            <div class="col-sm-3 wrapper-catalog-menu-button">
                <div class="wrapper-catalog-menu" <?if (\Sf\Base::App()->GetCurPage() == '/index.php'):?>style="display:block;"<?endif;?>>
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:catalog.section.list",
                        "catalog-menu",
                        array(
                            "IBLOCK_TYPE" => '',
                            "IBLOCK_ID" => 2,
                            "CACHE_TYPE" => 'A',
                            "CACHE_TIME" => '3600',
                            "CACHE_GROUPS" => 'Y',
                            "COUNT_ELEMENTS" => 'N',
                            "TOP_DEPTH" => 1,
                            "SECTION_URL" => '',
                            "VIEW_MODE" => '',
                            "SHOW_PARENT_NAME" => '',
                            "HIDE_SECTION_NAME" => '',
                            "ADD_SECTIONS_CHAIN" => 'N',
                            "ELEMENT_SORT_FIELD" => "sort",
                            "ELEMENT_SORT_ORDER" => "asc",
                        ),
                        '',
                        array("HIDE_ICONS" => "Y")
                    );?>
                </div>
                <a class="catalog-name-header" href="#">
                    <i class="fa fa-bars" aria-hidden="true"></i>продукция
                </a>
            </div>
            <div class="col-sm-4 col-md-6 nopadding">
                <?$APPLICATION->IncludeComponent("bitrix:search.title", "visual1", Array(
                    "NUM_CATEGORIES" => "1",	// Количество категорий поиска
                        "TOP_COUNT" => "5",	// Количество результатов в каждой категории
                        "CHECK_DATES" => "N",	// Искать только в активных по дате документах
                        "SHOW_OTHERS" => "N",	// Показывать категорию "прочее"
                        "PAGE" => SITE_DIR."catalog/",	// Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
                        "CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),	// Название категории
                        "CATEGORY_0" => array(	// Ограничение области поиска
                            0 => "iblock_catalog",
                        ),
                        "CATEGORY_0_iblock_catalog" => array(	// Искать в информационных блоках типа "iblock_catalog"
                            0 => "all",
                        ),
                        "CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
                        "SHOW_INPUT" => "Y",	// Показывать форму ввода поискового запроса
                        "INPUT_ID" => "title-search-input",	// ID строки ввода поискового запроса
                        "CONTAINER_ID" => "search",	// ID контейнера, по ширине которого будут выводиться результаты
                        "PRICE_CODE" => array(	// Тип цены
                            0 => "BASE",
                        ),
                        "SHOW_PREVIEW" => "Y",	// Показать картинку
                        "PREVIEW_WIDTH" => "75",	// Ширина картинки
                        "PREVIEW_HEIGHT" => "75",	// Высота картинки
                        "CONVERT_CURRENCY" => "Y",	// Показывать цены в одной валюте
                    ),
                    false
                );?>
            </div>
            <div class="col-sm-5 col-md-3">
                <div class="nav-icon-block">
                    <span class="compareBox-index">
                        <a class="compare <?echo  ($iCompareCount = (new \Sf\Base())->getCompareCount()) ? 'active' : '';?>" href="/catalog/compare/?action=COMPARE">
                        <span><?=$iCompareCount?></span>
                    </a>
                    </span>
                    <span class="favoritesBox-index">
                        <a  class="favorites <?echo  ($iFavoritesCount = (new \Sf\Favorites)->getCount()) ? 'active' : '';?>"
                            href="/personal/favorites/">
                            <span><?=$iFavoritesCount;?></span>
                        </a>
                    </span>
                    <span id="cartBox-index">
                        <?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", ".default", Array(
                            "PATH_TO_BASKET" => SITE_DIR."personal/cart/",	// Страница корзины
                                "PATH_TO_PERSONAL" => SITE_DIR."personal/",	// Страница персонального раздела
                                "SHOW_PERSONAL_LINK" => "N",	// Отображать персональный раздел
                                "SHOW_NUM_PRODUCTS" => "Y",	// Показывать количество товаров
                                "SHOW_TOTAL_PRICE" => "Y",	// Показывать общую сумму по товарам
                                "SHOW_PRODUCTS" => "N",	// Показывать список товаров
                                "POSITION_FIXED" => "N",	// Отображать корзину поверх шаблона
                                "COMPONENT_TEMPLATE" => ".default",
                                "SHOW_EMPTY_VALUES" => "Y",	// Выводить нулевые значения в пустой корзине
                                "SHOW_AUTHOR" => "N",	// Добавить возможность авторизации
                                "PATH_TO_REGISTER" => SITE_DIR."login/",	// Страница регистрации
                                "PATH_TO_PROFILE" => SITE_DIR."personal/",	// Страница профиля
                                "POSITION_HORIZONTAL" => "left",
                                "POSITION_VERTICAL" => "top",
                                "PATH_TO_ORDER" => SITE_DIR."personal/order/make/",	// Страница оформления заказа
                                "HIDE_ON_BASKET_PAGES" => "N",	// Не показывать на страницах корзины и оформления заказа
                            ),
                            false
                        );?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<?if ($APPLICATION->GetCurPage(true) != SITE_DIR."index.php"):?>
<div class="container-fluid grey">
    <div class="row">
        <div class="container">
            <div class="row breadcrumbs">
                <div class="col-sm-12 nopadding">
                <?$APPLICATION->IncludeComponent("bitrix:breadcrumb","",Array(
                        "START_FROM" => "0",
                        "PATH" => "",
                        "SITE_ID" => "s1"
                    )
                );?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid grey">
    <div class="row">
        <div class="container">
            <?if (strpos(\Sf\Base::App()->GetCurPage(), 'itemslist') == true):?>

            <?elseif (strpos(\Sf\Base::App()->GetCurPage(), 'catalog') != true):?>
                <h1 class="show-title"><?=\Sf\Base::App()->ShowTitle(false)?></h1>
            <?endif;?>
<?endif;?>
