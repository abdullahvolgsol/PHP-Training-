
<?php 
header("Access-Control-Allow-Origin: *");
include('inc/header.php');
?>
<?php include('inc/container.php');?>
<div class="container">
	<h2>Simple REST API with PHP and MySQL</h2>	
	<br>
	<br>
	<h3>1. Initailization</h3>	
	<form action="" method="POST">
		<div class="form-group">
			<label for="name"></label>
			<input type="text" name="url" value="https://login.wrike.com/oauth2/authorize/v4?client_id=luaXb1gH&response_type=code"class="form-control" required/>
		</div>
		<button type="submit" name="submit" class="btn btn-default">GET AUTHENTICATION TOKEN</button>
	</form>
	<p>&nbsp;</p>
	<?php
	if(isset($_POST['submit']))	{
		$url = $_POST['url'];
		header("Location:".$url);
	}
	?>	
	<h3>2. Exchanging authorization code for access token</h3>	
	<form action="" method="POST">
		<div class="form-group">
			<input type="text" name="url" value="https://login.wrike.com/oauth2/token" class="form-control" required/>
			<h3>client_id :</h3>
			<br>
			<input type="text" name="client_id" value="luaXb1gH" class="form-control" required/>
			<h3>client_secret :</h3>
			<br>
			<input type="text" name="client_secret" value="3K9WM4aANzzYNuYMNkPJulMXrFieCIoZRrbbaY7W2N6NOr6xyhC0tLKXYhh5j7S9" class="form-control" required/>
			<h3>  grant_type :</h3>
			<br>
			<input type="text" name="grant_type" value="authorization_code" class="form-control" required/>
			<h3>code :</h3>
			<br>
			<input type="text" name="code" value="Enter authorization_code" class="form-control" required/>
		</div>
		<button type="submit" name="submit2" class="btn btn-default">Exchanging authorization code for access token</button>
	</form>
	<p>&nbsp;</p>
	<?php
	if(isset($_POST['submit2'])){
		$url = $_POST['url'];
		$client_id = $_POST['client_id'];
		$client_secret = $_POST['client_secret'];
		$grant_type = $_POST['grant_type'];
		$code = $_POST['code'];
	
		$url = $_POST['url'];
   		$dataArray= array('client_id' =>$client_id,'client_secret'=>$client_secret ,'grant_type'=>$grant_type,'code'=>$code);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataArray);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


		$result=curl_exec($curl);
		print_r($result);
		if($e=curl_error($curl))
		{
			echo $e;
		}
		else
		{
        $result = json_decode($result,true);
        print_r($result);
		}
        curl_close($curl);
	}
	?>	
	<h3>3. Using access token</h3>	
	<form action="" method="POST">
		<div class="form-group">
			<h3>Enter access token :</h3>
			<br>
			<input type="text" name="token" value="Enter Access_token" class="form-control" required/>
			<h3>Using access token :</h3>
			<br>
			<input type="text" name="url" value="https://www.wrike.com/api/v4/contacts?me=true" class="form-control" required/>
		</div>
		<button type="submit" name="submit3" class="btn btn-default">Using access token</button>
	</form>
	<p>&nbsp;</p>
	<?php
	if(isset($_POST['submit3']))	{
		$url = $_POST['url'];
		$token=$_POST['token'];

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
	?>	
	<h3>4. Refreshing access token</h3>	
	<form action="" method="POST">
		<div class="form-group">
			<input type="text" name="url" value="https://login.wrike.com/oauth2/token" class="form-control" required/>
			<h3>client_id :</h3>
			<br>
			<input type="text" name="client_id" value="luaXb1gH" class="form-control" required/>
			<h3>client_secret :</h3>
			<br>
			<input type="text" name="client_secret" value="3K9WM4aANzzYNuYMNkPJulMXrFieCIoZRrbbaY7W2N6NOr6xyhC0tLKXYhh5j7S9" class="form-control" required/>
			<h3>grant_type :</h3>
			<br>
			<input type="text" name="grant_type" value="refresh_token" class="form-control" required/>
			<h3>refresh_token :</h3>
			<br>
			<input type="text" name="refresh_token" value="Enter refresh_token" class="form-control" required/>
			<h3>scope :</h3>
			<br>
			<input type="text" name="scope" value="Default" class="form-control" required/>
		</div>
		<button type="submit" name="submit4" class="btn btn-default">Exchanging authorization code for access token</button>
	</form>
	<p>&nbsp;</p>
	<?php
	if(isset($_POST['submit4']))	{
		$url = $_POST['url'];
		$client_id = $_POST['client_id'];
		$client_secret = $_POST['client_secret'];
		$grant_type = $_POST['grant_type'];
		$refresh_token = $_POST['refresh_token'];
		$scope = $_POST['scope'];
	
		$url = $_POST['url'];
   		$dataArray= array('client_id' =>$client_id,'client_secret'=>$client_secret ,'grant_type'=>$grant_type,'refresh_token'=>$refresh_token,'scope'=>$scope);
	    $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataArray);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$result=curl_exec($curl);
		print_r($result);
		if($e=curl_error($curl))
		{
			echo $e;
		}
		else
		{
        $result = json_decode($result,true);
        print_r($result);
		}
        curl_close($curl);

	}
	?>	

	<h3>5. Using access token Get Folder Api</h3>	
	<form action="" method="POST">
		<div class="form-group">
			<h3>Enter access token :</h3>
			<br>
			<input type="text" name="token" value="Enter Access_token" class="form-control" required/>
			<h3>Using access token find folder api :</h3>
			<br>
			<input type="text" name="url" value="https://www.wrike.com/api/v4/folders" class="form-control" required/>
		</div>
		<button type="submit" name="submit5" class="btn btn-default">Get Folder Api</button>
	</form>
	<p>&nbsp;</p>
		<?php
		if(isset($_POST['submit5']))	{
		$url = $_POST['url'];
		$token=$_POST['token'];

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
	?>	
	<h3>6. Using access token</h3>	
	<form action="" method="POST">
		<div class="form-group">
			<h3>Enter access token :</h3>
			<br>
			<input type="text" name="token" value="Enter Access_token" class="form-control" required/>
			<h3>folder creation :</h3>
			<br>
			<input type="text" name="url" value="https://www.wrike.com/api/v4/folders" class="form-control" required/>
		</div>
		<button type="submit" name="submit6" class="btn btn-default">Using access token</button>
	</form>
	<p>&nbsp;</p>
	<?php
	if(isset($_POST['submit6']))	{
		$url = $_POST['url'];
		$token=$_POST['token'];
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array(   "Accept: application/json",   "Authorization: Bearer ".$token, );
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_close($curl);

		$dataArray=array(
		'metadata' =>'[{"key":"testMetaKey","value":"testMetaValue"}]' ,
		'customFields' =>'[{"id":"IEABVODYJUAAPMDI","value":"testValue"}]',
		'description' =>'Test description',
		'project' =>'{"ownerIds":["KUGE7CNI"],"startDate":"2021-09-09","endDate":"2021-09-16","contractType":"Billable","budget":100}',
		'title' =>'Test folder',
		'shareds' =>'["KUGE7CNI"]'
		);

   		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataArray);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array(   "Accept: application/json", "Authorization: Bearer ".$token );
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);	
		$result=curl_exec($curl);
		print_r($result);
		if($e=curl_error($curl))
		{
			echo $e;
		}
		else
		{
        $result = json_decode($result,true);
      	print_r($result);

		}
        curl_close($curl);
	}
	?>	

</div>
<?php 

include('inc/footer.php');?>

