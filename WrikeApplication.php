<?php 
header("Access-Control-Allow-Origin: *");
include('inc/header.php');
include('src/wrike.php');
$wrike = new wrike();            
?>
<?php include('inc/container.php');?>
<div class="container">
   <h2>Simple REST API with PHP and MySQL for Wrike Application</h2> 
   <br>
   <br>
   <form action="" method="POST">
      <div class="form-group">
         <h3>Client id</h3>
         <br>        
         <input type="text" name="client_id" value="Enter Client Id" class="form-control" required/>
         <h3>Client secret</h3>
         <br>        
         <input type="text" name="client_secret" value="Enter Client Secret" class="form-control" required/>
      </div>
      <button type="submit" name="userdetail" class="btn btn-default">SUBMIT</button>
   </form>
   <p>&nbsp;</p>
   <?php
   if(isset($_POST['userdetail']))  {
      $client_id = $_POST['client_id'];
      $client_secret = $_POST['client_secret'];
         $getInitaillize=$wrike->InitailizationWrikeApplication($client_id,$client_secret);
      if ($getInitaillize==-1||$getInitaillize==-2)
      {  
         if ($getInitaillize==-1) {
            ?>
            <script type="text/javascript">
            alert("Incorrect client_id");
            </script>
            <?php 
         }
         if ($getInitaillize==-2) {
            ?>
            <script type="text/javascript">
            alert("Incorrect Url");
            </script>
            <?php 
         }
      }
   }
   ?> 
      <form action="" method="POST">
      <div class="form-group">
         <h3>Authorize code</h3>
         <br>        
         <input type="text" name="code" value="Enter Code" class="form-control" required/>
      </div>
      <button type="submit" name="codesubmit" class="btn btn-default">SUBMIT</button>
   </form>
   <p>&nbsp;</p>
   <?php
   if(isset($_POST['codesubmit']))  {
         $code = $_POST['code'];
         $getAccessToken=$wrike->GenerateAccessToken($code);
           if ($getAccessToken==-1||$getAccessToken==-2||$getAccessToken==-3)
               {  
                  if ($getAccessToken==-1) {
                  ?>
                  <script type="text/javascript">
                  alert("No Connection Found");
                  </script>
                  <?php 
                  }
                  if ($getAccessToken==-2) {
                  ?>
                  <script type="text/javascript">
                  alert("No Record Found");
                  </script>
                  <?php 
                  }
                  if ($getAccessToken==-3) {
                  ?>
                  <script type="text/javascript">
                  alert("No Record Added");
                  </script>
                  <?php 
                  }
               }
            else
               {
                  ?>
                  <script type="text/javascript">
                  alert(" AccessToken Found Successfully");
                  </script>
                  <?php 
               }
         
   }
?>
      <form action="" method="POST">
      <div class="form-group">
         <h3>Test Connection</h3>
         <br>
         <button type="submit" name="testconnection" class="btn btn-default">Test Connectiion</button>
      </form>
      <p>&nbsp;</p>
   <?php
   if(isset($_POST['testconnection'])) {
      $IsFindContacts=$wrike->UsingAccessTokenFindContacts();
         if ($IsFindContacts==-1) {
            ?>
            <script type="text/javascript">
            alert("Not Found Contacts");
            </script>
            <?php 
            }
         if ($IsFindContacts[0]=='Access token is unknown or invalid') {
            $isRefreshToken=$wrike->GenerateRefreshAccessToken();
            if ($isRefreshToken==-1||$isRefreshToken==-2||$isRefreshToken==-3)
               {  
                  if ($isRefreshToken==-1) {
                  ?>
                  <script type="text/javascript">
                  alert("No Connection Found");
                  </script>
                  <?php 
                  }
                  if ($isRefreshToken==-2) {
                  ?>
                  <script type="text/javascript">
                  alert("No Record Found");
                  </script>
                  <?php 
                  }
                  if ($isRefreshToken==-3) {
                  ?>
                  <script type="text/javascript">
                  alert("No Record Added");
                  </script>
                  <?php 
                  }
               }
            else
               {
                  ?>
                  <script type="text/javascript">
                  alert("Token Refresh Successfully");
                  </script>
                  <?php 
               }
            }
            else
            {
                  ?>
                  <script type="text/javascript">
                  alert("Contacts Found Successfully");
                  </script>
                  <?php 
           
            }  
   
   }
?>
</div>
<?php include('inc/footer.php');?>

