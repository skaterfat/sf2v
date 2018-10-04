$(document).ready(function() {

    var changeTimer = '';
    var searchResultGlobal = '';
    var catalogLeftSearchResult = false;

    var popupYouCity = $('.popupYouCity');
    
    $(document).on('click', '.popup-city-yes', function () {

        popupYouCity.hide();

        $.ajax({
            type: 'GET',
            url: '/ajax/ajax.system.php',
            dataType: 'json',
            data: {ACTION: 'setCity', LOCATION_ID: $(this).data('location-id')},
            success: function(data) {

            },
            error:  function(xhr, str){
                dialogOpen('Возникла ошибка: ' + xhr.responseCode);
            }
        });
    });

    $(document).on('click', '.popup-city-save', function () {
        var obj = $(this);
        var objSelect = obj.prev('select');

        popupYouCity.hide();

        $.ajax({
            type: 'GET',
            url: '/ajax/ajax.system.php',
            dataType: 'json',
            data: {ACTION: 'setCity', LOCATION_ID: objSelect.val()},
            success: function(data) {

                location.reload();

            },
            error:  function(xhr, str){
                dialogOpen('Возникла ошибка: ' + xhr.responseCode);
            }
        });

    });

    $(document).on('click', '.popup-city-no', function () {
        popupYouCity.html('');

        var content = '';

        $.ajax({
            type: 'GET',
            url: '/ajax/ajax.system.php',
            dataType: 'json',
            data: {ACTION: 'getCitys'},
            success: function(data) {

                htmlText = '';

                if (data.LOCATIONS) {
                    popupYouCity.html(buildSelect(data.LOCATIONS));
                }

                popupYouCity.html(popupYouCity.html()
                    + "<a href='#' class='btn btn-info popup-city-save'>Сохранить</a>");

            },
            error:  function(xhr, str){
                dialogOpen('Возникла ошибка: ' + xhr.responseCode);
            }
        });
    });
    
    
    var pagen = false;

    $(document).on('click', '.ajax-nav', function () {

        var obj = $(this);

        pagen = obj.attr('href');
        sectionId = obj.data('section-id');

        var bPreload = obj.hasClass('preload');

        searchStart($('#form-section_id-' + sectionId), bPreload);

        return false;
    });

    $('html').keydown(function(e){ //отлавливаем нажатие клавиш
        if (e.keyCode == 13) { //если нажали Enter, то true

            var filterName = $('.filter-name');
            if (filterName.is(':focus')) {
                $('.catalog-left-search-result').click();
            }

        }
    });

    $(".product-detail-gallery-big, .productDetail-sketch").fancybox({
        'titleShow'     : false
    });

    // Щелчок по "лупе"
    $(document).on('click', '.catalog-left-search-button', function () {
        $('.catalog-left-search-result').click();
        return false;
    });

    // Показать товары в резерве
    $(document).on('click', '.showGoodsOnOrder', function () {

        $(this).closest('form').submit();

        return true;
    });

    $(document).on('click', '.catalog-left-search-result', function () {

        var obj = $(this);
        var table = $('.table.section-list');
        var detailElement = $('.detail-element');

        var wrapperTableSectionLists = $('.wrapper-table-section-lists');

        if (searchResultGlobal) {

            wrapperTableSectionLists.empty();
            wrapperTableSectionLists.html(searchResultGlobal);

            detailElement.empty();
            detailElement.html(searchResultGlobal);

            // Рейтинг
            $('.section-main-page-rating-stars').barrating({
                theme: 'css-stars',
                showSelectedRating: false,
                readonly: true
            });

            // Много, мало
            $('.catalog-many').barrating({
                theme: 'bars-1to10',
                showSelectedRating: false,
                readonly: true
            });

            if (catalogLeftSearchResult == false) {
                obj.hide();
            }
        } else {
            obj.hide();
            $('table.section-list').html('<h2>Ничего не найдено!</h2>');
        }

        catalogLeftSearchResult = false;

        return false;
    });

    // Поисковые фильтры
    $(document).on('keydown', '.filter-name', function () {

        var obj = $(this);
        var form = obj.closest('form');

        searchStart(form);
    });

    // Остальные фильтры
    $(document).on('click', '.podshipnik-type, .podshipnik-podtype', function () {

        var obj = $(this);
        var form = obj.closest('form');

        searchStart(form);
    });

    function buildSelect(json){
        var selEl = $('<select class="form-control">');

        $.each(json, function(ID, LABEL){
            $('<option>').val(ID).text(LABEL).appendTo(selEl);
        });

        return selEl;
    }

    function searchStart(form, bPreload) {

        var searchResult = $('.catalog-left-search-result');

        if(changeTimer !== false) clearTimeout(changeTimer);

        changeTimer = setTimeout(function(){

            if (bPreload == true) {
                preloadShow();
            }

            $.ajax({
                type: 'POST',
                url: '/ajax/ajax.system.php?pagen=' + pagen,
                dataType: 'json',
                data: form.serialize(),
                success: function(data) {

                    if (bPreload == true) {
                        preloadHide();
                    }

                    if (data.error) {
                        searchResult.hide();
                    } else {

                        searchResult.html(data.TEXT);
                        searchResult.show();

                        searchResultGlobal = data.CONTENT;

                        catalogLeftSearchResult = true;
                        $('.catalog-left-search-result').click();
                    }

                },
                error:  function(xhr, str){
                    //dialogOpen('Возникла ошибка: ' + xhr.responseCode);
                }
            });

            changeTimer = false;

        },500);
    }

    // Главный слайдер
    $(".main-slider-carousel").owlCarousel({
        items : 1,
        loop: true,
        autoplay: false,
        nav:true,
        navText: ['', ''],
        responsive: {
            0:{
                dots: false,
                items:1,
                nav:false
            },
            600:{
                dots: false,
                items:1,
                nav:true
            }
        }
    });

    // Сладер в карточке товара
    $(".product-detail-gallery").owlCarousel({
        items : 1,
        loop: true,
        autoplay: true,
    });

    // Бренды слайдер
    $(".brands-list-carousel").owlCarousel({
        items : 8,
        loop: true,
        autoplay: false,
        nav:false,
        responsive: {
            0:{
                dots: false,
                items:2,
                nav:false
            },
            600:{
                dots: false,
                items:3,
                nav:false
            },
            1000:{
                dots: false,
                items:8,
                nav:false,
                loop:true,
                autoplay: true,
            }
        }
    });

    // Рейтинг
    $('.section-main-page-rating-stars').barrating({
        theme: 'css-stars',
        showSelectedRating: false,
        readonly: true
    });

    // Много, мало
    $('.catalog-many').barrating({
        theme: 'bars-1to10',
        showSelectedRating: false,
        readonly: true
    });

    // Много, мало в блоке "ПО СКЛАДАМ"
    $('.remains-list').barrating({
        theme: 'bars-1to10',
        showSelectedRating: false,
        readonly: true
    });

    // Закрыть модальное окно
    $(document).on('click', '.modal-close', function () {
        $(this).closest('.modal').modal('hide');
    });

    // Открыть / Закрыть блок "ПРОДУКЦИЯ"
    $(document).on('click', '.wrapper-catalog-menu-button', function () {

        if (location.pathname == '/') {
            return true;
        }

        var obj = $(this);
        var objFind = obj.find('.wrapper-catalog-menu');

        if (objFind.hasClass('active')) {
            objFind.removeClass('active');
        } else {
            objFind.addClass('active');
            return false;
        }

        return true;
    });

    // Открыть / Закрыть блок входа на сайт
    $(document).on('click', '.menu-profile', function () {
        var obj = $(this);

        if (obj.hasClass('auth')) {
            return true;
        }

        var wrapperDiv = obj.closest('div');

        if (wrapperDiv.hasClass('active')) {
            wrapperDiv.removeClass('active');
        } else {
            wrapperDiv.addClass('active');
        }
    });


    // Popup - подменю
    $(document).on('click', '.submenu-flag', function () {
        var obj = $(this);

        if (obj.hasClass('active')) {
            obj.removeClass('active');
        } else {
            obj.addClass('active');
            return false;
        }

        return true;
    });

    // Кнопка "добавить в корзину"
    $(document).on('click', '.addToCart', function () {

        var obj= $(this);

        var productId = obj.data('product-id');
        var fieldQuantity = $('#CART_QUANTITY_' + productId);

        $.ajax({
            type: 'POST',
            url: '/ajax/ajax.addTobasket.php',
            dataType: 'json',
            data: {PRODUCT_ID: productId, QUANTITY: fieldQuantity.val()},
            success: function(data) {

                if (data.error) {
                    dialogOpen(data.error);
                } else {
                    button = '<br><a href="/personal/cart/" class="button-green">Перейти в корзину</a>'
                        + '<a href="" onclick="remodal.close(); return false;" class="button-green">Продолжить покупки</a>';

                    dialogOpenBasket('Товар добавлен в корзину<br>', '', button);

                    jsAjaxUtil.InsertDataToNode('/ajax/ajaxbasketIndex.php', 'cartBox-index', false);
                }
            },
            error:  function(xhr, str){
                dialogOpen('Возникла ошибка: ' + xhr.responseCode);
            }
        });

        return false;
    });

    // Кнопка "добавить в избранное"
    $(document).on('click', '.catalog-favoritesLink, .favoritesLink', function () {

        var obj= $(this);

        if (obj.hasClass('active')) {
            location.href = '/personal/favorites/';
            return false;
        }

        var productId = obj.data('product-id');

        $.ajax({
            type: 'POST',
            url: '/ajax/ajax.addToFavorites.php',
            dataType: 'json',
            data: {PRODUCT_ID: productId},
            success: function(data) {

                if (data.error) {
                    dialogOpen(data.error);
                } else {
                    obj.addClass('active');
                    //dialogOpen(data.message);

                    updateFavorites();
                }
            },
            error:  function(xhr, str){
                dialogOpen('Возникла ошибка: ' + xhr.responseCode);
            }
        });

        return false;
    });

    // Кнопка "удалить из избранного"
    $('.compare-delete').click(function () {

        var obj= $(this);

        var productId = obj.data('product-id');

        var productBlock = obj.closest('tr');

        $.ajax({
            type: 'POST',
            url: '/ajax/ajax.addToFavorites.php',
            dataType: 'json',
            data: {PRODUCT_ID: productId, ACTION : 'DELETE'},
            success: function(data) {

                if (data.error) {
                    dialogOpen(data.error);
                } else {
                    dialogOpen(data.message);
                    productBlock.remove();

                }
            },
            error:  function(xhr, str){
                dialogOpen('Возникла ошибка: ' + xhr.responseCode);
            }
        });

        return false;
    });

    // Кнопка "Добавить в сравнение"
    $(document).on('click', '.catalog-compareLink, .compareLink', function () {

        var obj= $(this);

        var productId = obj.data('product-id');

        if (obj.hasClass('active')) {
            location.href = '/catalog/compare/?action=COMPARE';
            return false;
        }

        $.ajax({
            type: 'GET',
            url: '/catalog/compare/',
            dataType: 'json',
            data: {action: 'ADD_TO_COMPARE_LIST', id: productId, ajax_action : 'Y'},
            success: function(data) {

                if (data.STATUS == 'OK') {
                    obj.addClass('active');
                    //dialogOpen(data.MESSAGE);
                } else {
                    dialogOpen(data.MESSAGE);
                }
            },
            error:  function(xhr, str){
                dialogOpen('Возникла ошибка: ' + xhr.responseCode);
            }
        });

        return false;
    });

    // Popup - ВОЙТИ НА САЙТ
    $(document).on('click', '.show-authorization, .formBasketContacts-enter-profile', function () {
        showAuthorization();
        return false;
    });

    // Popup - ВОССТАНОВЛЕНИЕ ПАРОЛЯ
    $(document).on('click', '.show-forgotPassword', function () {
        showForgotPassword();
        return false;
    });

    // Popup - ВОССТАНОВЛЕНИЕ ПАРОЛЯ
    $(document).on('click', '.show-basketContacts', function () {
        showBasketContacts();
        return false;
    });

    // Развернуть блок в карточке товара с детальным описанием
    $('.catalog-preview-text-more').click(function () {
        var obj = $(this);
        obj.next('span').addClass('active');
        obj.remove();

        return false;
    });

    // Показать / скрыть блок характеристик в КОРЗИНЕ ТОВАРОВ
    $('.catalog-chs-show-full').click(function () {
        var obj = $(this);   
        var chs = obj.closest('.catalog-chs');

        if (chs.hasClass('show')) {
            obj.closest('.catalog-chs').removeClass('show');
            obj.html('Показать полностью');
        } else {
            obj.closest('.catalog-chs').addClass('show');
            obj.html('Скрыть полностью');
        }

        return false;
    });

    // Форма авторизации на сайте
    $('#form-enter-wrapper').submit(function() {
        var form = $(this);
        var urlPath = document.location.pathname;

        $.ajax({
            type: 'POST',
            url: '/ajax/ajax.autorize.php',
            dataType: 'json',
            data: form.serialize(),
            success: function(data) {

                if (data.error) {
                    dialogOpen(data.error);
                } else if (data.authorize) {

                    setTimeout(function(){ location="/personal/profile/"; }, 1000);
                }
            },
            error:  function(xhr, str){
                dialogOpen('Возникла ошибка: ' + xhr.responseCode);
            }
        });

    });

    // Форма восстановления пароля на сайте
    $('#formForgotPasswordWrapper').submit(function() {
        var form = $(this);

        $.ajax({
            type: 'POST',
            url: '/ajax/ajax.password.recovery.php',
            dataType: 'json',
            data: form.serialize(),
            success: function(data) {

                if (data.error) {
                    dialogOpen(data.error);
                } else {
                    $('.modal').modal('hide')
                    dialogOpen('Новый пароль отправлен на указанную почту');

                }
            },
            error:  function(xhr, str){
            }
        });
    });

    // Меню Каталог товаров - раскрывающееся
    $(document).on('click', '.catalog-left > li > form > a', function () {

        var obj = $(this);
        var el  = obj.closest('li')

        if (!el.hasClass('active')) {
            el.addClass('active');
        } else {
            el.removeClass('active');
        }


        return false;
    })

    // Подменю Каталог товаров - раскрывающийся
    $(document).on('click', '.catalog-left .podmenu > li > a', function () {

        var obj = $(this);
        var el  = obj.next('.podmenu-checkbox');

        if (!el.hasClass('active')) {
            el.addClass('active');
        } else {
            el.removeClass('active');
        }

        return false;
    });

    // Вложенное подменю Каталог товаров - раскрывающийся
    $(document).on('click', '.catalog-left .podmenu-checkbox > li > a, .catalog-left .podmenu-checkbox-level-2 > li > a', function (event) {

        var obj = $(this);
        var el  = obj.closest('li');

        var t = event.target;

        if (!el.hasClass('active')) {
            el.addClass('active');
        } else {
            el.removeClass('active');
        }

        if (t.tagName == 'A') {

            obj.find('input[type="checkbox"]').attr('checked', 'checked');
            return false;
        }

        return true;
    });

    //  Оформить заказ
    function checkOutSf()
    {
        return false;

        if (!!BX('coupon'))
            BX('coupon').disabled = true;
        BX("basket_form").submit();
        return true;
    }

    // Раскрыть/скрыть блок в разделе КОНТАКТЫ
    $(document).on('click', '.order-steps-address.contacts > div.row', function (event) {

        var obj = $(this);
        var block  = obj.closest('.order-steps-address');

        if (event.target.tagName == 'A') {
            return true;
        }

        if (!block.hasClass('active')) {
            block.addClass('active');
        } else {
            block.removeClass('active');
        }

        return false;
    });

    // Заказать обратный звонок
    $(".feedback-call").fancybox({
        'overlayShow': true,
        'overlayOpacity':0.3,
        'padding': 0,
        'margin' : 0,
        'scrolling' : 'no',
        'titleShow': false,
        'type': 'ajax',
        'width' : 450,
        'href': '/ajax/ajax.feedback.call.php',
        'onStart' : function () {

        }
    });

});

// Форма авторизации в popup
function showAuthorization() {

    $('.modal').modal('hide');
    var popup = $('#form-enter');

    popup.modal().show();

    return false;
}
// Форма восстановления пароля
function showForgotPassword() {

    $('.modal').modal('hide');
    var popup = $('#form-forgotPassword');

    popup.modal().show();

    return false;
}

// Форма контакты - в корзине товаров
function showBasketContacts() {

    $('.modal').modal('hide');
    var popup = $('#form-basketContacts');

    popup.modal().show();

    return false;
}

// Форма "Добавить в корзину"
function showAddToBasket() {

    $('.modal').modal('hide');
    var popup = $('#form-add-to-basket');

    popup.modal().show();

    return false;
}

// Всплывающее окно
function dialogOpen(text, title, button)
{

    block = $('[data-remodal-id=modal]');
    remodal = block.remodal({
        hashTracking : false,
    });
    block.find('.head').empty().html(title);
    block.find('.message').empty().html(text);

    remodal.open();

    return false;
}

// Всплывающее окно для корзины
function dialogOpenBasket(text, title, button)
{

    block = $('[data-remodal-id=modal-basket]');
    remodal = block.remodal({
        hashTracking : false,
    });
    block.find('.head').empty().html(title);
    block.find('.message').empty().html(text);

    if (button) {
        block.find('.message').append(button);
    }

    remodal.open();

    return false;
}

// Обновить блока "Избранное"
function updateFavorites() {

    $.ajax({
        type: 'GET',
        url: '/ajax/ajax.system.php',
        dataType: 'json',
        data: {ACTION: 'favorites'},
        success: function(data) {

            if (data.FAVORITES_COUNT > 0) {

                var blockFavoritesIndex = $('.favoritesBox-index');
                blockFavoritesIndex.find('a').addClass('active');
                blockFavoritesIndex.find('span').html(data.FAVORITES_COUNT);
            }
        },
        error:  function(xhr, str){
            dialogOpen('Возникла ошибка: ' + xhr.responseCode);
        }
    });
}

// Показать скрыть блок с минимальной ценой
function showMinPriceAlert() {
    var priceBlock = $('.catalog-sum-price-value');
    var price = priceBlock.html().replace(/[^\d,]/g, '');

    var noValidBlock = $('.order-sum-min-no-valid');
    var catalogOrderStep = $('.catalog-order-step');

    if (price < 500) {
        noValidBlock.addClass('active');
        catalogOrderStep.removeClass('active');
    } else {
        noValidBlock.removeClass('active');
        catalogOrderStep.addClass('active');
    }

}

function preloadShow() {
    obPreloader = $('#page-preloader');
    obPreloader.css('display', 'block');
}

function preloadHide() {
    obPreloader = $('#page-preloader');
    obPreloader.css('display', 'none');
}

// Событие, которое слушает изменение товаров в корзине
BX.addCustomEvent(window, 'OnBasketChange', showMinPriceAlert);