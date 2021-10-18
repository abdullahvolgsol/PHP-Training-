<?php 

class Wrike 
{
    public $Id;
    public $database = false;
    public function __construct()
    {
    $serverName  = 'localhost';
    $userName  = 'root';
    $password   = "";
    $databaseName  = "wrikes";      
    $this->database = mysqli_connect($serverName, $userName, $password,$databaseName);      
    if (!$this->database) {
    die("Connection failed: " . mysqli_connect_error());
    exit;
    }
    }
   public function InitailizationWrikeApplication($clientId,$clientSecret)
   {
    if (isset($clientId)) {
        if (mysqli_query($this->database,"INSERT INTO `user`(`client_id`,`client_secret`) VALUES ('$clientId','$clientSecret');"))
        {
        $url="https://login.wrike.com/oauth2/authorize/v4?client_id=".$clientId."&response_type=code";
        header("Location:".$url);
        }
        else{
            return -2;
        }
    }
    else
    {
        return -1;
    }
    } 
    public function GenerateAccessToken($mycode)
    {
        $clientId="";
        $clientSecret="";
        $lastInsertedId="";
       if(!$this->database) 
       {
        return -1;
       }
        $sql = "SELECT * FROM user";
        $result = mysqli_query($this->database, $sql);
        $counts=mysqli_num_rows($result);
        if ($counts > 0) {
         $row = mysqli_fetch_assoc($result); 
            $lastInsertedId=$row['id'];
            $clientId=$row["client_id"];
            $clientSecret=$row['client_secret'];
            
        } 
        else {
            return -1;
        }
        $this->Id=$lastInsertedId;
        $counts=0;
        $res=mysqli_query($this->database,"SELECT * FROM `user` WHERE id='$lastInsertedId';");
        $row= mysqli_fetch_assoc($res);
        $counts=mysqli_num_rows($res);
        if ($counts>0 )
        {

         $clientId=$row['client_id'];
         $clientSecret=$row['client_secret'];
         
         }
        $url="https://login.wrike.com/oauth2/token";
        $grant_type="authorization_code";
        $client_id =$clientId;
        $client_secret =$clientSecret; 
        $code = $mycode;
        $dataArray= array('client_id' =>$client_id,'client_secret'=>$client_secret ,'grant_type'=>$grant_type,'code'=>$code);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result=curl_exec($curl);
        if($e=curl_error($curl))
        {
            echo $e;
        }
        else
        {
        $result = json_decode($result,true);
        $i=0;
        $tokenArray=array();    
        foreach ($result as $key ) {
        $tokenArray[$i]=$key;
        $i++;
        }
        if (mysqli_query($this->database,"UPDATE `user` SET authorize_code ='$tokenArray[0]', refresh_authorize_token = '$tokenArray[4]' WHERE id=".$lastInsertedId.";"))
        {   
            return 1;
        }
        else{
            return -1;
        }
        mysqli_close($this->database);
        }
        curl_close($curl);
   } 
   public function GenerateRefreshAccessToken()
   {
        $lastInsertedId="";   
        $clientId="";
        $clientSecret="";
        $authorizeToken="";
        $refreshAuthorizeToken="";
       if(!$this->database) 
       {
        return -1;
       }

        $sql = "SELECT * FROM user";
        $result = mysqli_query($this->database, $sql);
        $counts=mysqli_num_rows($result);
        if ($counts > 0) {
         $row = mysqli_fetch_assoc($result); 
            $lastInsertedId=$row['id'];   
            $clientId=$row['client_id'];
            $clientSecret=$row['client_secret'];
            $authorizeToken=$row['authorize_code'];
            $refreshAuthorizeToken=$row['refresh_authorize_token'];
        } 
        else {
            return -2;
        }
        $this->Id=$lastInsertedId;
        $counts=0;
        $res=mysqli_query($this->database,"SELECT * FROM `user` WHERE id='$lastInsertedId';");
        $row= mysqli_fetch_assoc($res);
        $counts=mysqli_num_rows($res);
        if ($counts>0 )
         {
            $clientId=$row['client_id'];
            $clientSecret=$row['client_secret'];
            $authorizeToken=$row['authorize_code'];
            $refreshAuthorizeToken=$row['refresh_authorize_token'];
         }
        $url="https://login.wrike.com/oauth2/token";
        $grant_type="refresh_token";
        $scope="Default";
        $client_id = $clientId;
        $client_secret = $clientSecret;
        $refresh_token = $refreshAuthorizeToken;    
        $dataArray= array('client_id' =>$client_id,'client_secret'=>$client_secret ,'grant_type'=>$grant_type,'refresh_token'=>$refresh_token,'scope'=>$scope);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result=curl_exec($curl);
        if($e=curl_error($curl))
        {
            echo $e;
        }
        else
        {
        $result = json_decode($result,true);
        $i=0;
        $tokenArray=array();    
        foreach ($result as $key ) {
        $tokenArray[$i]=$key;
        $i++;
        }

        if (mysqli_query($this->database,"UPDATE `user` SET authorize_code ='$tokenArray[0]', refresh_authorize_token = '$tokenArray[4]' WHERE id=".$lastInsertedId.";"))
        {
            
            return 1;
        }
        else{

            return -3;
        }
        mysqli_close($this->database);
        }
        curl_close($curl);
   } 
    public function UsingAccessTokenFindContacts()
    {
        $res=mysqli_query($this->database,"SELECT * FROM `user` WHERE authorize_code is not NULL;");
        $row= mysqli_fetch_assoc($res);
        $counts=mysqli_num_rows($res);
        if ($counts>0 )
         {
         $lastInsertedId=$row['id'];   
         $clientId=$row['client_id'];
         $clientSecret=$row['client_secret'];
         $authorizeToken=$row['authorize_code'];
         $refreshAuthorizeToken=$row['refresh_authorize_token'];
         }
         else
         {
            return -1;
         }
        $url="https://www.wrike.com/api/v4/contacts?me=true";
        $token=$authorizeToken;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(   "Accept: application/json",   "Authorization: Bearer ".$token, );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        if($e=curl_error($curl))
        {
            echo $e;
        }
        else
        {
        $result = json_decode($result,true);
        $i=0;
        $tokenArray=array();    
        foreach ($result as $key ) {
        $tokenArray[$i]=$key;
        $i++;
        }
        }
        curl_close($curl);
        return $tokenArray;        
    }
    public function UsingAccessTokenFindFolders()
    {
        $res=mysqli_query($this->database,"SELECT * FROM `user` WHERE authorize_code is not NULL;");
        $row= mysqli_fetch_assoc($res);
        $counts=mysqli_num_rows($res);
        if ($counts>0 )
         {
         $lastInsertedId=$row['id'];   
         $clientId=$row['client_id'];
         $clientSecret=$row['client_secret'];
         $authorizeToken=$row['authorize_code'];
         $refreshAuthorizeToken=$row['refresh_authorize_token'];
         }
         else
         {
            return -1;
         }
        $url="https://www.wrike.com/api/v4/folders";
        $token=$authorizeToken;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(   "Accept: application/json",   "Authorization: Bearer ".$token, );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        if($e=curl_error($curl))
        {
            echo $e;
        }
        else
        {
        $result = json_decode($result,true);
        $i=0;
        $tokenArray=array();    
        foreach ($result as $key ) {
        $tokenArray[$i]=$key;
        $i++;
        }
        }
        curl_close($curl);
        return $tokenArray; 
    }
}