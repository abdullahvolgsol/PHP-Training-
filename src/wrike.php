<?php 

class Wrike 
{
    public $Id;
    public $database = false;
    public function __construct()
    {
    $servername  = 'localhost';
    $username  = 'root';
    $password   = "";
    $databasename  = "wrikes";      
    $this->database = mysqli_connect($servername, $username, $password,$databasename);  
    
    if (!$this->database) {
    die("Connection failed: " . mysqli_connect_error());
    exit;
    }

    }


   public function Initailization($getclient_id,$getclient_secret)
   {
    if (isset($getclient_id)) {


        if (mysqli_query($this->database,"INSERT INTO `user`(`client_id`,`client_secret`) VALUES ('$getclient_id','$getclient_secret');"))
        {
        $url="https://login.wrike.com/oauth2/authorize/v4?client_id=".$getclient_id."&response_type=code";
        header("Location:".$url);
        }
        else{

            return -1;
        }
    }
    else
    {
        return -1;

    }
  } 
    public function generateAccessToken($mycode)
    {
        $getclient_id="";
        $getclient_secret="";
        $last_id="";
 
       if(!$this->database) 
       {
        return -1;
       }

        $sql = "SELECT * FROM user";
        $result = mysqli_query($this->database, $sql);
        $counts=mysqli_num_rows($result);
        if ($counts > 0) {
         $row = mysqli_fetch_assoc($result); 
            $last_id=$row['id'];
            $getclient_id=$row["client_id"];
            $getclient_secret=$row['client_secret'];
            
        } 
        else {
            return -1;
        }
        $this->Id=$last_id;
        $counts=0;
        $res=mysqli_query($this->database,"SELECT * FROM `user` WHERE id='$last_id';");
        $row= mysqli_fetch_assoc($res);
        $counts=mysqli_num_rows($res);
        if ($counts>0 )
         {

         $getclient_id=$row['client_id'];
         $getclient_secret=$row['client_secret'];
         
         }
         
        $url="https://login.wrike.com/oauth2/token";
        $grant_type="authorization_code";
        $client_id =$getclient_id;
        $client_secret =$getclient_secret; 
        $code = $mycode;
        echo $client_id."    ".$client_secret."     ".$last_id;
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
        if (mysqli_query($this->database,"UPDATE `user` SET authorize_code ='$tokenArray[0]', refresh_authorize_token = '$tokenArray[4]' WHERE id=".$last_id.";"))
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
   
   public function generaterefreshAccessToken($getclient_id,$getclient_secret)
   {
        $url="https://login.wrike.com/oauth2/token";
        $grant_type="refresh_token";
        $scope="Default";
        $client_id = $getclient_id;
        $client_secret = $getclient_secret;
        $refresh_token = $this->refresh_authorize_token;    

       
    
        $dataArray= array('client_id' =>$client_id,'client_secret'=>$client_secret ,'grant_type'=>$grant_type,'refresh_token'=>$refresh_token,'scope'=>$scope);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result=curl_exec($curl);
       // print_r($result);
        if($e=curl_error($curl))
        {
            echo $e;
        }
        else
        {
        $result = json_decode($result,true);
        print_r($result);
        $this->access_token=$result['access_token'];
        $this->refresh_authorize_token=$result['refresh_token'];
      
        }
        curl_close($curl);


   } 


    public function Usingaccesstokenfindcontacts()
    {
        $res=mysqli_query($this->database,"SELECT * FROM `user` WHERE authorize_code is not NULL;");
        $row= mysqli_fetch_assoc($res);
        $counts=mysqli_num_rows($res);
        if ($counts>0 )
         {
         $getid=$row['id'];   
         $getclient_id=$row['client_id'];
         $getclient_secret=$row['client_secret'];
         $getauthorize_token=$row['authorize_code'];
         $getrefresh_authorize_token=$row['refresh_authorize_token'];
         
         }
         else
         {
            return -1;
         }
        

        $url="https://www.wrike.com/api/v4/contacts?me=true";
        $token=$getauthorize_token;
        

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(   "Accept: application/json",   "Authorization: Bearer ".$token, );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        var_dump($resp);
        print_r($resp);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result=curl_exec($curl);    
        curl_close($curl);
        $result = json_decode($result,true);
        print_r($result);


    }
        public function Usingaccesstokenfindfolders()
    {
        $url="https://www.wrike.com/api/v4/folders";
        $token=$this->access_token;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(   "Accept: application/json",   "Authorization: Bearer ".$token, );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $headerResult = curl_exec($curl);
        curl_close($curl);
        var_dump($headerResult);
        print_r($headerResult);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result=curl_exec($curl);    
        curl_close($curl);
        $result = json_decode($result,true);
        print_r($result);


    }



}