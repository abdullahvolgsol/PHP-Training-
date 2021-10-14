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
         $getInitaillize=$wrike->Initailization($client_id,$client_secret);

         if ($getInitaillize==-1) {
            ?>
            <script type="text/javascript">
            alert("Incorrect client_id");
            </script>
            <?php 
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
      $getAccessToken=$wrike->generateAccessToken($code);
         if ($getAccessToken==-1) {
            ?>
            <script type="text/javascript">
            alert("Not Found AccessToken");
            </script>
            <?php 
         }
      else
      {
         ?>
            <script type="text/javascript">
            alert("Finally Found AccessToken");
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
      $IsFindContacts=$wrike->Usingaccesstokenfindcontacts();
         if ($IsFindContacts==-1) {
            ?>
            <script type="text/javascript">
            alert("Not Found Contacts");
            </script>
            <?php 
            }
            if ($IsFindContacts[0]=='Access token is unknown or invalid') {
               $wrike->generaterefreshAccessToken();
               if ($IsFindContacts==-1) {
                  ?>
                     <script type="text/javascript">
                     alert("Not Found Token");
                     </script>
                  <?php 
               }
               else
               {
                  ?>
                     <script type="text/javascript">
                     alert("Token is Refreshed");
                     </script>
                  <?php 
               }  
            }
            else
            {
               print_r($IsFindContacts);


            }  
   
}
?>

</div>
<?php include('inc/footer.php');?>

