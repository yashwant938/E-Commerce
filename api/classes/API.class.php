<?php
class API
{

    public static function PostData($api_link,$dataarray){
        

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$dataarray,
            CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json'
            ),
          ));
          $response = curl_exec($curl);
          $err = curl_error($curl);
          if ($err) {
              return array();
          }
          curl_close($curl);
  
          return json_decode($response,true);
          

    }
    

}
