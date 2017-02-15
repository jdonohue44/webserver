<?php
include "../inc/dbinfo.inc";
session_start();
?>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="./css/styles.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://use.fontawesome.com/b3331c12b9.js"></script>
</head>
<body class="home">
<?php
  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that tables exists. */
  VerifyUserTable($connection, DB_DATABASE);

  /* If input fields are populated, add a row to the Employees table. */
  $user_name  = htmlentities($_POST['Name']);
  $user_email = htmlentities($_POST['Email']);

  if (strlen($user_email)) {
    if(!strlen($user_name)){
      $user_name = $user_email;
    }
    AddUser($connection, $user_name, $user_email);
    $_SESSION["name"] =  $user_name;
    $_SESSION["email"] = $user_email;
    $user_name  = '';
    $user_email = '';
    header("Location: http://54.86.139.119/Interests.php");
  }
?>

<div class="flex_box_holder">

    <div class="screen_shot_img">
    </div>

    <div class="sign_in_container">

        <div class="info">
          <h1 class="espress-title">Espress</h1>
        </div>

        <div class="sign_in_form">
          <h2 class="espress-title-message">Sign up and start costumizing
          your newsletter.</h2>
          <!-- Input form -->
          <form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
            <div id="name_box">
                <input type="text" name="Name"  id="name_text" placeholder="Name" autocorrect="off" autocapitalize="off" tabindex=1 maxlength="30" size="30" />
                <!-- <i class="fa fa-times-circle-o" aria-hidden="true"></i> -->
            </div>

            <div id="email_box">
              <input type="text" name="Email" id="email_text" placeholder="Email" autocorrect="off" autocapitalize="off" tabindex=2 maxlength="35" size="40" />
                  <!-- <div id="check_box">
                    <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                  </div> -->
            </div>

            <h5 id="validation_typing"></h5>

            <input type="submit" name="validate_button" value="Get started" />
          </form>
        </div>

    </div>
</div>

<!-- Clean up. -->
<?php
  mysqli_free_result($result);
  mysqli_close($connection);
?>

<script>
  document.getElementsByName('validate_button')[0].disabled = true;

  // enable Validate button when Email is Valid
  $('#email_text').bind('input propertychange', function() {
    var text = $(this).val();
    $("#validation_typing").text("");
    var email = $('#email_text').val();
    if (validateEmail(email)) {
      $("#validation_typing").text(email + " is valid!");
      $("#validation_typing").css("color", "#3897F0");
      document.getElementsByName('validate_button')[0].disabled = false;
      document.getElementsByName('validate_button')[0].style.backgroundColor = "#3897F0";
    } else {
      if(!email.length){
        document.getElementsByName('validate_button')[0].disabled = true;
        // document.getElementsByName('validate_button')[0].style.backgroundColor = "#D8D8D8";
      }else{
        $("#validation_typing").text(email + " is not valid yet.");
        $("#validation_typing").css("color", "red");
        document.getElementsByName('validate_button')[0].disabled = true;
        // document.getElementsByName('validate_button')[0].style.backgroundColor = "#D8D8D8";
      }
    }
  });

  function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }

</script>
</body>
</html>

<?php

/* Add an employee to the table. */
function AddUser($connection, $name, $email) {
   $n = mysqli_real_escape_string($connection, $name);
   $e = mysqli_real_escape_string($connection, $email);
  //  $check_query = sprintf("SELECT * FROM `USERS` (`Name`,`Email`) WHERE `Email` = '%s';",
  //  mysqli_real_escape_string($e));
   $check_query = "SELECT * FROM USERS WHERE Email = '$e'";
   $present = mysqli_query($connection, $check_query);
   $num_rows = mysqli_num_rows($present);
   if($num_rows<1){
     $query = "INSERT INTO `USERS` (`Name`,`Email`) VALUES ('$n', '$e');";
     if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
   }else{
     echo "<script> alert('Email already registered. Weclome back.'); </script>";
   }
}

function VerifyUserTable($connection, $dbName) {
  if(!TableExists("USERS", $connection, $dbName))
  {
     $query = "CREATE TABLE `USERS` (
         `ID` int(11) NOT NULL AUTO_INCREMENT,
         `Name` varchar(45) DEFAULT NULL,
         `Email` varchar(90) DEFAULT NULL,
         PRIMARY KEY (`ID`),
         UNIQUE KEY `ID_UNIQUE` (`ID`)
       ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating users table.</p>");
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
