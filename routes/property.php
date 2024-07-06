<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Smalot\PdfParser\Parser;

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$app->get('/property/list', function (Request $request, Response $response) use ($app) {   
    return $this->get('renderer')->render($response, 'myListing.php',['route'=>$app->getRouteCollector()->getRouteParser()]);
})->setName('myPropertyList')->add($authMiddleware);

$app->get('/propertyList', function (Request $request, Response $response) use ($app) {   

    $db = Database::getInstance();
    Property::constructStatic($db);
    Functions::constructStatic($db);

    $locationList=Property::fetchAllLocations();
    $location_p_arr=array();
    foreach($locationList as $locationItem){
        $location_p_arr[]=$locationItem["location_id"];
    }

    $amenity_p_arr=array();
    $amenitys=Property::fetchAllAmenity();
    foreach($amenitys as $amenityItem){
        $amenity_p_arr[]=$amenityItem["amenity_id"];
    }
    
    $min_budget=$_GET["min"];
    $max_budget=$_GET["max"];
    $search=$_GET["s"];

    $avi_date=Functions::date_db_format($_GET["avi"]);
    if($avi_date==""){
        $avi_date=date("Y-m-d", strtotime("+6 months"));
    }

    $location_id=json_decode($_GET["fl"]);
    $amenity_id=json_decode($_GET["fa"]);
    $type=json_decode($_GET["ft"]);

    $location_arr=array();
    $amenity_arr=array();
    $type_arr=array();

    if($location_id){
        $location_arr=$location_id;
        foreach($location_id as $location_elm){
            if(!in_array($location_elm,$location_p_arr)){
                $location_arr=$location_id;
            }
        }
    }else{
        $location_arr=$location_p_arr;
    }
    if($type){
        $type_arr=$type;
        foreach($type as $pt){
            if(!in_array($pt,array("0","1"))){
                $type_arr=array(0,1);
            }
        }
    }else{
        $type_arr=array(0,1);
    }

    if($amenity_id){
        $amenity_arr=$amenity_id;
        foreach($amenity_id as $amenity_elm){
            if(!in_array($amenity_elm,$amenity_p_arr)){
                $amenity_arr=$amenity_p_arr;
            }
        }
    }else{
        $amenity_arr=$amenity_p_arr;
    }

 
    $properties=Property::fetchAllProperty($_SESSION["userId"],$type_arr,$min_budget,$max_budget,$avi_date,$location_arr,$amenity_arr,$search);

    $renderer = new PhpRenderer('pages');

    return $renderer->render($response, 'listingList.php',['route'=>$app->getRouteCollector()->getRouteParser(),'properties'=>$properties]);

})->setName('allPropertyList')->add($authMiddleware);



$app->get('/property/add', function (Request $request, Response $response) use ($app,$csrf) {    
    return $this->get('renderer')->render($response, 'addLsting.php',['route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
})->setName('addPropertyForm')->add($authMiddleware);

$app->post('/property/add', function (Request $request, Response $response) use ($app,$csrf) {  

    
    $db = Database::getInstance();
    Property::constructStatic($db);
    Functions::constructStatic($db);

    $locationList=Property::fetchAllLocations();
    $location_p_arr=array();
    foreach($locationList as $locationItem){
        $location_p_arr[]=$locationItem["location_id"];
    }
    
    $amenity_p_arr=array();
    $amenitys=Property::fetchAllAmenity();
    foreach($amenitys as $amenityItem){
        $amenity_p_arr[]=$amenityItem["amenity_id"];
    }

    $data = $request->getParsedBody();

    if (!$csrf->validate('addlisting-form')){
        return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "Invalid Token, Refresh page",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }

    if(!isset($data["location"]) || !isset($data["plotSize"]) || !isset($data["price"]) || !isset($data["property_type"]) || !isset($data["description"]) || !isset($data["address"]) || !isset($data["amenity"]) || !isset($data["propert_available_from"]) || !isset($request->getUploadedFiles()["property_ownership"]) || !isset($request->getUploadedFiles()["propert_images"]) 
    || trim($request->getUploadedFiles()["property_ownership"]->getFilePath())=="" || trim($request->getUploadedFiles()["propert_images"]->getFilePath())=="" 
    || trim($data["location"]=="") || trim($data["plotSize"]=="") || trim($data["price"]=="") || trim($data["property_type"]=="") || trim($data["description"]=="") || trim($data["address"]=="") || trim($data["amenity"]=="") || trim($data["propert_available_from"]=="")){    

        return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "All fields are required",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }

    

    $property_type=$data["property_type"];
    if($property_type!="0" && $property_type!="1"){
        return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "Invalid Property Type",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }

    $lease_months="0";
    if($property_type=="0"){
        if(!isset($data["lease_months"]) || trim($data["lease_months"]=="") || !Functions::isNumber($data["lease_months"]) || ($data["lease_months"] > 60 || $data["lease_months"]<1)){
            return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "Invalid Lease Months",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
        }
        $lease_months=$data["lease_months"];
    }

    $location=$data["location"];
    if(!in_array($location,$location_p_arr)){
        return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "Invalid Location",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }
    
    $plotSize=$data["plotSize"];
    if(!Functions::isNumber($plotSize)){
        return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "Plot Size should be integer",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }



    $price=$data["price"];
    if(!Functions::isPriceDecimal($price)){
        return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "Price should be decimal and less than 70000",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }

    
    
    $description=$data["description"];
    $address=$data["address"];

    if(!is_array($data["amenity"])){
        return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "Invalid Amenity",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }
    if(empty($data["amenity"])){
        return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "Amenity Required",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }
    foreach($data["amenity"] as $amenity_item){
        if(!in_array($amenity_item,$amenity_p_arr)){
            return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "Invalid Amenity",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
        }
    }
    $amenity=json_encode($data["amenity"]);


    if(!Functions::isValidDate($data["propert_available_from"])){
        return $this->get('renderer')->render($response, 'addLsting.php', ['message' => "Invalid available date",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }
    $available_from=Functions::date_db_format($data["propert_available_from"]);

    $images="";
    $user_id=$_SESSION["userId"];


    $property_ownership = $request->getUploadedFiles()["property_ownership"];
    
    $file_name = $property_ownership->getClientFilename();
    $FileType = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));

    
    if($FileType!="pdf"){
        $message="Invalid Ownership File, Only PDF allowed";
        return $this->get('renderer')->render($response, 'addLsting.php', ['message' => $message,'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }

    $property_ownership_name = Functions::generateRandomString(100).".".$FileType;    
    $property_ownership->moveTo("files/upload/ownership/".$property_ownership_name);


    $file = $request->getUploadedFiles()["propert_images"];
    $files_arr=array();
  //  foreach($files as $file){
        $file_name = $file->getClientFilename();

        $imageFileType = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));

        if(!in_array($imageFileType,array("png","jpg","jpeg"))){
            $message="Invalid Image, Only png, jpg & jpeg allowed";
            return $this->get('renderer')->render($response, 'addLsting.php', ['message' => $message,'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
        }	
        $newImgName = Functions::generateRandomString(100).".".$imageFileType;
        
        $file->moveTo("files/img/property/".$newImgName);
        array_push($files_arr,$newImgName);
  //  }
    $images=json_encode($files_arr);

    $message="";
    $property_uid=Functions::generateRandomString(50);

    if(Property::addProperty($property_uid,$location,$plotSize,$price,$property_type,$description,$images,$address,$amenity,$available_from,$property_ownership_name,$lease_months,$user_id)){
        $message="Property created";        
    }else{
        $message="Error while creating property";
    }


    return $this->get('renderer')->render($response, 'addLsting.php', ['message' => $message,'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);

})->setName('addProperty')->add($authMiddleware);



$app->get('/property/{uid}', function (Request $request, Response $response, $args) use ($app) {    
    $propertyId = $args['uid'];

    $db = Database::getInstance();
    Property::constructStatic($db);
    $propertyDetail = Property::fetchPropertyByUID($propertyId);

    
    if(!$propertyDetail){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    

    return $this->get('renderer')->render($response, 'property_details.php', ['propertyDetail' => $propertyDetail,'route'=>$app->getRouteCollector()->getRouteParser()]);
    
})->setName('viewPropertyDetails')->add($authMiddleware);

$app->post('/property/{uid}/payment', function (Request $request, Response $response, $args) use ($app,$csrf) {    
    $propertyId = $args['uid'];

    $db = Database::getInstance();
    Property::constructStatic($db);
    $propertyDetail = Property::fetchPropertyByUID($propertyId);
    
    if(!$propertyDetail){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    
    if($propertyDetail["isSold"]=="1"){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    if ($propertyDetail["created_by"]==$_SESSION["userId"]){
        return $response->withStatus(404)->write('Invalid operation');

    }

    $api = new Api(Config::KEY_ID, Config::KET_SECRET);
    $receipt_id=Functions::get_rand_critical_small(30);
    $orderData = [
        'receipt'         => $receipt_id,
        'amount'          => $propertyDetail["price"]*100, // 2000 rupees in paise
        'currency'        => 'INR',
        'payment_capture' => 1 // auto capture
    ];
    $razorpayOrder = $api->order->create($orderData);
    $razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;


$payment_json = [
    "key"               => Config::KEY_ID,
    "amount"            => $propertyDetail["price"],
    "name"              => "Cyb3rC1ph3r",
    "description"       => "Property Contract Payment",
    "image"             => "https://cdn-icons-png.flaticon.com/512/6557/6557706.png",
    "prefill"           => [
    "name"              => $_SESSION["full_name"],
    "email"             => $_SESSION["email"],
    "contact"           => "9999999999",
    ],
    "notes"             => [],
    "theme"             => ["color" => "#F37254"],
    "order_id"          => $razorpayOrderId,
];


    Property::addPayment($propertyDetail["property_id"],$propertyDetail["created_by"],$_SESSION["userId"],$propertyDetail["price"],$receipt_id,$razorpayOrderId,"INITIATED");
    
    $renderer = new PhpRenderer('pages');

    return $renderer->render($response, 'payment.php',['route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'payment_json'=>$payment_json,'csrf'=>$csrf]);

})->setName('paymentPoperty')->add($authMiddleware);

$app->post('/property/{uid}/buy', function (Request $request, Response $response, $args) use ($app,$csrf){    
    $propertyId = $args['uid'];


    $db = Database::getInstance();
    Property::constructStatic($db);
    $propertyDetail = Property::fetchPropertyByUID($propertyId);

    if(!$propertyDetail){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    
    if($propertyDetail["isSold"]=="1"){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    
    if (!$csrf->validate('submitpayment-form')) {
        $_SESSION["errorMessage"]="Session ended, try again";
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('viewPropertyDetails',array("uid"=>$propertyDetail["property_uid"])))->withStatus(302);
    }
    
    
    $data = $request->getParsedBody();
    $kyc_email=$data["kyc_email"];
    $kyc_password=$data["kyc_password"];

    $kycData='{
        "email": "'.$kyc_email.'",
        "password": "'.$kyc_password.'"
       }';
    $apiData=array();

       //    $apiData=API::PostData(Config::API_LINK,$kycData);
       
    $apiData["status"]="success";
    if(!$apiData || $apiData["status"]!="success"){
        $_SESSION["errorMessage"]="Invalid KYC details";
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('viewPropertyDetails',array("uid"=>$propertyDetail["property_uid"])))->withStatus(302);
    }

    $api = new Api(Config::KEY_ID, Config::KET_SECRET);
    try
    {
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature' => $data['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);

        
        Property::updatePayment($_SESSION['razorpay_order_id'],$data['razorpay_payment_id'],$data['razorpay_signature'],$kyc_email,"SUCESS");

        Property::buyProperty($propertyId,$_SESSION["userId"]); 
    }
    catch(SignatureVerificationError $e)
    {
        Property::updatePayment($_SESSION['razorpay_order_id'],$data['razorpay_payment_id'],$data['razorpay_signature'],$kyc_email,"FAILED");

        $_SESSION["errorMessage"]="Payment failed, try again later";
    }
    return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('viewPropertyDetails',array("uid"=>$propertyDetail["property_uid"])))->withStatus(302);
    

})->setName('buyPoperty')->add($authMiddleware);

$app->get('/property/{uid}/contract', function (Request $request, Response $response, $args) use ($app){    
    $propertyId = $args['uid'];

    $db = Database::getInstance();
    Property::constructStatic($db);
    $propertyDetail = Property::fetchPropertyByUID($propertyId);

    if(!$propertyDetail){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }

    if($_SESSION["userId"]!=$propertyDetail["created_by"] && $_SESSION["userId"]!=$propertyDetail["buyer_id"]){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }

    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('times', '', 10);

    $p1="SELLER";
    $p2="BUYER";
   
    $type="PURCHASE";
    if($_SESSION["userId"]==$propertyDetail["created_by"]){
        $userType="Seller";
    }else{
        $userType="Buyer";
    }
    if($propertyDetail["property_type"]=="0"){
        $type="LEASE";
        if($_SESSION["userId"]==$propertyDetail["created_by"]){
            $userType="Lessor";
        }else{
            $userType="Lessee";
        }
        
        $p1="LESSOR";
        $p2="LESSEE";
    }

    $data = '<h1 style="text-align:center;">'.$type.' AGREEMENT</h1> <h3><b><u>PARTIES</u></b></h3><br> This Contract is executed on the <b>"' . htmlspecialchars(Functions::date_disp_format($propertyDetail["buy_date"]), ENT_QUOTES, 'UTF-8') . '"</b> , between the following parties:<br> <br>'.$p1.': <br>Name: <b>"' . htmlspecialchars($propertyDetail["owner_name"], ENT_QUOTES, 'UTF-8') . '"</b> <br>Email: <b>"' . htmlspecialchars($propertyDetail["owner_email"], ENT_QUOTES, 'UTF-8') . '"</b> <br>Address: <b>"' . htmlspecialchars($propertyDetail["owner_address"], ENT_QUOTES, 'UTF-8') . '"</b> <br><br>'.$p2.': <br>Name: <b>"' . htmlspecialchars($propertyDetail["buyer_name"], ENT_QUOTES, 'UTF-8') . '"</b> <br>Email: <b>"' . htmlspecialchars($propertyDetail["buyer_email"], ENT_QUOTES, 'UTF-8') . '"</b> <br>Address: <b>"' . htmlspecialchars($propertyDetail["buyer_address"], ENT_QUOTES, 'UTF-8') . '"</b> <br><br>PROPERTY DESCRIPTION: <br>Address: <b>"' . htmlspecialchars($propertyDetail["address"], ENT_QUOTES, 'UTF-8') . '"</b> <br>Property Size: <b>"' . htmlspecialchars($propertyDetail["property_size"], ENT_QUOTES, 'UTF-8') . '" sq m</b> <br>Location: <b>"' . htmlspecialchars($propertyDetail["location"], ENT_QUOTES, 'UTF-8') . '"</b> ';
    if($propertyDetail["property_type"]=="0"){ $data.='<br>Duration: <b>"' . htmlspecialchars($propertyDetail["lease_months"], ENT_QUOTES, 'UTF-8') . ' Months"</b> '; }
    if($propertyDetail["property_type"]=="1"){
        $data.='<br><br>PURCHASE PRICE: <b>"' . htmlspecialchars($propertyDetail["price"], ENT_QUOTES, 'UTF-8') . '"/- Rs</b> <br><br>The Buyer agrees to purchase the property described above from the Seller for the total purchase price of <b>"' . htmlspecialchars($propertyDetail["price"], ENT_QUOTES, 'UTF-8') . '"</b> /- Rs (the "Purchase Price"). <br>';
    }else{
        $data.='<br><br>LEASED PRICE: <b>"' . htmlspecialchars($propertyDetail["price"], ENT_QUOTES, 'UTF-8') . '"/- Rs</b> <br><br>The lessee agrees to lease the property described above from the lessor for the price of <b>"' . htmlspecialchars($propertyDetail["price"], ENT_QUOTES, 'UTF-8') . '"</b> /- Rs per month. <br>';
    }
    
    $data.='<br>ENTIRE AGREEMENT:<br> This Contract represents the entire agreement between the parties, superseding all prior and contemporaneous understandings, agreements, representations, and warranties. <br><br><br><br><b>'.htmlspecialchars($_SESSION["full_name"], ENT_QUOTES, 'UTF-8').'</b> <br><b>'.$userType."</b>";
    
    $data=preg_replace('!\s+!', ' ', $data);

    $pdf->writeHTML($data, true, false, true);

    $hash=hash('sha256', strip_tags($data));
    $sign=openssl_encrypt($hash,"AES-128-ECB",Config::SERVER_PRIVATE_KEY);

    $pdf->SetY(-40);
    $pdf->writeHTML('<hr/>', true, false, true, false, 'L');
    $pdf->writeHTML('Signature: '.$sign, true, false, true, false, 'L');

    $pdfContent = $pdf->Output('', 'I');

    return $response
    ->withHeader('Content-Type', 'application/pdf')
    ->withHeader('Content-Disposition', 'attachment; filename="contract.pdf"')
    ->withBody(new \Slim\Psr7\Stream(fopen('php://temp', 'r+')))
    ->write($pdfContent);

})->setName('popertyContract')->add($authMiddleware);


$app->get('/check/property', function (Request $request, Response $response, $args) use ($app,$csrf) {    
    $db = Database::getInstance();    
    return $this->get('renderer')->render($response, 'checkProperty.php',['route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);

})->setName('checkPropertyForm')->add($authMiddleware);

$app->post('/check/property', function (Request $request, Response $response, $args) use ($app,$csrf) {    

    $db = Database::getInstance();
    Property::constructStatic($db);

    if (!$csrf->validate('checkcontract-form')){
        return $this->get('renderer')->render($response, 'checkProperty.php', ['route'=>$app->getRouteCollector()->getRouteParser(),'message' => "Invalid Token, Refresh page",'csrf'=>$csrf]);
    }

    if(!isset($request->getUploadedFiles()["property_file"])
    || trim($request->getUploadedFiles()["property_file"]->getFilePath())==""){    
        return $this->get('renderer')->render($response, 'checkProperty.php', ['route'=>$app->getRouteCollector()->getRouteParser(),'message' => "File Required",'csrf'=>$csrf]);      
    }

    $file = $request->getUploadedFiles()["property_file"];
    $file_name = $file->getClientFilename();
    $FileType = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));	

    if($FileType!="pdf"){
        $message="Invalid File";
    }else{
        $parser = new Parser();

        $pdf = $parser->parseFile($file->getFilePath());
        $text = $pdf->getText();
        $lines = preg_split('/\r\n|\r|\n/', $text);

        $signature="";
        $data="";
        $i=0;
        foreach ($lines as $line){    
            if(strpos($line, 'Signature:') !== false) {
                $signature=$lines[$i].$lines[$i+1];
                break;
            }
            $i++;
            $data.=trim($line)." ";
        }
        $data=trim($data);
        $data=preg_replace('!\s+!', ' ', $data);
        $calculatedHash=hash('sha256', $data);

        $signature=substr($signature, 11);
        $fileHash=openssl_decrypt($signature,"AES-128-ECB",Config::SERVER_PRIVATE_KEY);

        if($calculatedHash==$fileHash){
            $message="Valid Document";
        }else{
            $message="Invalid Document";
        }

    }

    return $this->get('renderer')->render($response, 'checkProperty.php', ['route'=>$app->getRouteCollector()->getRouteParser(),'message' => $message,'csrf'=>$csrf]);

})->setName('checkProperty')->add($authMiddleware);

$app->get('/property/{uid}/remove', function (Request $request, Response $response, $args) use ($app) {    
    $propertyId = $args['uid'];

    $db = Database::getInstance();
    Property::constructStatic($db);
    $propertyDetail = Property::fetchPropertyByUID($propertyId);
    
    if(!$propertyDetail){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    
    if($propertyDetail["isSold"]=="1"){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }

    if ($propertyDetail["created_by"]==$_SESSION["userId"]){
        Property::removeProperty($propertyId,$_SESSION["userId"]);      
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('myPropertyList'))->withStatus(302);
    } else {
        return $response->withStatus(404)->write('Invalid operation');
    }
})->setName('removePoperty')->add($authMiddleware);




$app->get('/property/{uid}/edit', function (Request $request, Response $response, $args) use ($app,$csrf) {    
    $propertyId = $args['uid'];
    $db = Database::getInstance();
    Property::constructStatic($db);
    $propertyDetail = Property::fetchPropertyByUID($propertyId);
    
    if(!$propertyDetail){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    
    if($propertyDetail["isSold"]=="1"){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }

    return $this->get('renderer')->render($response, 'editLsting.php',['route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
})->setName('editPopertyForm')->add($authMiddleware);

$app->post('/property/{uid}/edit', function (Request $request, Response $response, $args) use ($app,$csrf) {  

    
    $db = Database::getInstance();
    Property::constructStatic($db);
    Functions::constructStatic($db);

    $locationList=Property::fetchAllLocations();
    $location_p_arr=array();
    foreach($locationList as $locationItem){
        $location_p_arr[]=$locationItem["location_id"];
    }
    
    $amenity_p_arr=array();
    $amenitys=Property::fetchAllAmenity();
    foreach($amenitys as $amenityItem){
        $amenity_p_arr[]=$amenityItem["amenity_id"];
    }

    $data = $request->getParsedBody();

    $property_uid=$args['uid'];
    $propertyDetail = Property::fetchPropertyByUID($property_uid);
    if(!$propertyDetail){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }

    
    if($propertyDetail["isSold"]=="1"){
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    
    if (!$csrf->validate('editlisting-form')){
        return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "Invalid Token, Refresh page",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
    }

    
    if(!isset($data["location"]) || !isset($data["plotSize"]) || !isset($data["price"]) || !isset($data["property_type"]) || !isset($data["description"]) || !isset($data["address"]) || !isset($data["amenity"]) || !isset($data["propert_available_from"])
    || trim($data["location"]=="") || trim($data["plotSize"]=="") || trim($data["price"]=="") || trim($data["property_type"]=="") || trim($data["description"]=="") || trim($data["address"]=="") || trim($data["amenity"]=="") || trim($data["propert_available_from"]=="")){
        return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "All fields are required",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
    }


    $property_type=$data["property_type"];
    if($property_type!="0" && $property_type!="1"){
        return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "Invalid Property Type",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
    }

    $lease_months="0";
    if($property_type=="0"){
        if(!isset($data["lease_months"]) || trim($data["lease_months"]=="") || !Functions::isNumber($data["lease_months"]) || ($data["lease_months"] > 60 || $data["lease_months"]<1)){
            return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "Invalid Lease Months",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
        }
        $lease_months=$data["lease_months"];
    }

    
    $location=$data["location"];
    if(!in_array($location,$location_p_arr)){
        return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "Invalid Location",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
    }
    
    $plotSize=$data["plotSize"];
    if(!Functions::isNumber($plotSize)){
        return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "Plot Size should be integer",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
    }



    $price=$data["price"];
    if(!Functions::isPriceDecimal($price)){        
        return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "Price should be decimal and less than 70000",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
    }


    $description=$data["description"];
    $address=$data["address"];
    
    if(!is_array($data["amenity"])){
        return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "Invalid Amenity",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
    }
    if(empty($data["amenity"])){        
        return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "Amenity Required",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
    }
    foreach($data["amenity"] as $amenity_item){
        if(!in_array($amenity_item,$amenity_p_arr)){
            return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "Invalid Amenity",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
        }
    }
    $amenity=json_encode($data["amenity"]);


    if(!Functions::isValidDate($data["propert_available_from"])){        
        return $this->get('renderer')->render($response, 'editLsting.php', ['message' => "Invalid available date",'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);
    }
    $available_from=Functions::date_db_format($data["propert_available_from"]);




    $user_id=$_SESSION["userId"];

   

    $message="";

    if(Property::updateProperty($property_uid,$location,$plotSize,$price,$property_type,$description,$address,$amenity,$available_from,$lease_months,$user_id)){
        $message="Property updated";        
    }else{
        $message="Error while updating property";
    }

    $propertyDetail = Property::fetchPropertyByUID($property_uid);

    return $this->get('renderer')->render($response, 'editLsting.php', ['message' => $message,'route'=>$app->getRouteCollector()->getRouteParser(),'propertyDetail'=>$propertyDetail,'csrf'=>$csrf]);

})->setName('editPoperty')->add($authMiddleware);


?>