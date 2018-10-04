<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

try{

    $iProductId = (int)$_REQUEST['PRODUCT_ID'];
    $sQuantity  = (int)$_REQUEST['QUANTITY'] ? (int)$_REQUEST['QUANTITY'] : 1;

    if(!Add2BasketByProductID($iProductId, $sQuantity, [], [])){
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