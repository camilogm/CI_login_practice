<?php 

require('Facebook/autoload.php');

class FacebookClass 
{
    public  $handler;
    public  $FBObject;
    public function __construct()
   {
    $this->FBObject = new \Facebook\Facebook([
        'app_id' => '2557214354338439',
        'app_secret' => '7e0a4092736c22db0601b9f6324cf119',
        'default_graph_version' => 'v2.10'
    ]);
    
    $this->handler = $this->FBObject -> getRedirectLoginHelper();    
   }

   public function GetUrlFacebook($redirectTo)
   {       
    $data = ['email'];
    $fullURL = $this->handler->getLoginUrl($redirectTo, $data);

    return $fullURL;
   }

   public function CallBack($baseURL)
   {
    
   

    try {
        $accessToken = $this->handler->getAccessToken();
    }catch(\Facebook\Exceptions\FacebookResponseException $e){
        echo "Response Exception: " . $e->getMessage();
        
    
    }catch(\Facebook\Exceptions\FacebookSDKException $e){
        echo "SDK Exception: " . $e->getMessage();
        
    
    } 
    echo '<script>document.body.innerHTML = "";</script>';

    if(!$accessToken)
    {    
        return null;
    }
    
    $oAuth2Client = $this->FBObject->getOAuth2Client();
    echo '<script>document.body.innerHTML = "";</script>';
    
    
    if(!$accessToken->isLongLived())
        $accessToken = $oAuth2Client->getLongLivedAccesToken($accessToken);
    
        
        $response = $this->FBObject->get("/me?fields=id, first_name, last_name, email, picture.type(large)", $accessToken);
        $UserData = $response->getGraphNode()->asArray();
        session_start();
        $_SESSION['UserData'] = $UserData;   
        echo '<script>document.body.innerHTML = "";</script>';
        echo '<script> window.location ="'.$baseURL.'";</script>';     
   }

}

?>