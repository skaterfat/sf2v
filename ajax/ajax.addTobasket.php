<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

try{

    $iProductId = (int)$_REQUEST['PRODUCT_ID'];
    $sQuantity  = (int)$_REQUEST['QUANTITY'] ? (int)$_REQUEST['QUANTITY'] : 1;
    $nPriceId  = (int)$_REQUEST['PRICE_ID'] ? (int)$_REQUEST['PRICE_ID'] : false;

    //if(!Add2BasketByProductID($iProductId, $sQuantity, [], [])){
    if($nPriceId) {

        $arProduct = CIBlockElement::GetList(Array(), Array('ID' => $iProductId), false, false, Array())->GetNext();
        AddMessage2Log(print_r($arElement, true), "OnGetOptimalPriceHandler_intProductID");
        $arPrice = GetCatalogProductPriceList($iProductId, "SORT", "ASC");

        foreach($arPrice as $arP) {
            if($arP['ID'] == $nPriceId) {
                $PRICE = $arP;
                break;
            }
        }

        $arFields = array(
            "PRODUCT_ID" => $PRICE['PRODUCT_ID'],
            "PRODUCT_PRICE_ID" => $PRICE['ID'],
            "PRICE" => $PRICE['PRICE'],
            "CURRENCY" => $PRICE['CURRENCY'],
            "QUANTITY" => $sQuantity,
            "LID" => LANG,
            "NAME" => $arProduct['NAME'],
            "DETAIL_PAGE_URL" => $arProduct['DETAIL_PAGE_URL']
        );

        CSaleBasket::Add($arFields);

    }
    elseif(!Add2BasketByProductID($iProductId, $sQuantity, [], [])){
        throw new Exception('Товар отсутствует на складе!');
    }

    $sStatus = 'SUCCESS';


} catch (Exception $obEx) {

    echo json_encode([
        'status' => $sStatus,
        'error' => $obEx->getMessage()
    ]);
    exit;
}

echo json_encode([
    'status' => $sStatus,
]);


?>