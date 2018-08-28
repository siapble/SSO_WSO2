<?php

namespace siapble\sso_wso2;

use Illuminate\Support\Str;

class SSO_WSO2
{
    protected $client_id;
    protected $client_secret;  
    protected $wso2_server_url;

    protected function setClientId($code) {
        $this->client_id = $code;
        return $this;
    }

    protected function setClientSecretId($code) {
        $this->client_secret = $code;
        return $this;
    }

    protected function setWSO2ServerURL($url) {
        $this->wso2_server_url = $url;
        return $this;
    }

    public function callback($processCode){
        try {
            $response = '';

            if (empty($processCode)) {
                $response = array(
                    'success'=>'false',
                    'result'=>'ProcessCode is empty.'
                );
                return $response;
            }

            if (!$this->client_id) {
                $this->setClientId(app('config')->get("wso2spc.client_id"));
            }

            if (!$this->client_secret) {
                $this->setClientSecretId(app('config')->get("wso2spc.client_secret"));
            }     

            if (!$this->wso2_server_url) {
                $this->setWSO2ServerURL(app('config')->get("wso2spc.wso2_server_url"));
            }      

            //1. get token             
            $input = array(
                'code' =>  $processCode,
                'grant_type' => 'authorization_code',
                'redirect_uri' => url('/sso/callback'),
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret
            );
               
            ob_start();       
            $ch = curl_init($this->wso2_server_url.'/oauth2/token');
            curl_setopt ($ch, CURLOPT_VERBOSE, 1);
            curl_setopt ($ch, CURLOPT_POST, 1);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($input));
            curl_exec ($ch);
            curl_close ($ch);
            $process_result = ob_get_contents();
            ob_end_clean();

            $dataToken = json_decode($process_result, true);             
            $access_token = '';
            //$id_token = '';
            if (!empty($dataToken) && array_key_exists('access_token', $dataToken)) {
                $access_token = $dataToken['access_token'];
                //$id_token = $dataToken['id_token'];
            }else{
                $response = array(
                    'success'=>'false',
                    'result'=>'Login Fail : Get token fail, '.$dataToken['error_description']
                );
                return $response;                
            }

            //2. verify token
            $input = array(
                'scope' => 'openid',
                'grant_type' =>  'client_credentials',
                'access_token' => $access_token,                
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret
            );

            ob_start();       
            $ch = curl_init($this->wso2_server_url.'/oauth2/userinfo?schema=openid');
            curl_setopt ($ch, CURLOPT_VERBOSE, 1);
            curl_setopt ($ch, CURLOPT_POST, 1);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($input));
            curl_exec ($ch);
            curl_close ($ch);
            $process_result = ob_get_contents();
            ob_end_clean();

            $dataUser = json_decode($process_result, true); 

            $userId = '';
            if (!empty($dataUser) && array_key_exists('sub', $dataUser)) {
                $userId = $dataUser['sub'];

                //****auth & redirect
            }else{
                $response = array(
                    'success'=>'false',
                    'result'=>'Login Fail : Invalid token, '.$dataUser['error_description']
                );
            }

            $response = array(
                'success'=>'true',
                'result'=> $processCode.','.$access_token.','.$userId
            );

            return $response;

       } catch (Exception $e) {
           return array(
                        'success'=>'false',
                        'result'=>'login fail',
                        'Exception'=> $e->getMessage()                       
                    );
       }  
        
    }

    public function make_authorization_url($callback_url){
    	try {

            if (!$this->client_id) {
                $this->setClientId(app('config')->get("wso2spc.client_id"));
            }
            
            if (!$this->wso2_server_url) {
                $this->setWSO2ServerURL(app('config')->get("wso2spc.wso2_server_url"));
            }                          

    		$uuid = uniqid();
    		$data = array(
			    'client_id' => $this->client_id,
			    'Grant_type' => 'authorization_code',
			    'response_type' => 'code',
			    'state' => $uuid,
			    'redirect_uri' =>  $callback_url, //url('/sso/callback'),
			    'scope' => 'openid'
			);
    		$dataEncode = http_build_query($data);

            $output = $this->wso2_server_url."/oauth2/authorize?".$dataEncode;
    		
            $response = array(
                'success'=>'true',
                'result'=> $output
            );

    		return $response;
    	} catch (Exception $e) {
    		 return array(
                        'success'=>'false',
                        'result'=>'make_authorization_url fail',
                        'Exception'=> $e->getMessage()                       
                    );
    	}
    }    
}
