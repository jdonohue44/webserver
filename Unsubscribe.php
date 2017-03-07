<?php
include "../inc/dbinfo.inc";
session_start();
?>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="./css/styles.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body>
<h1 align="center" style="padding:5% 5% 0 5%; font-weight:100; color: #505050;">Confirm your email address to unsubscribe.</h1>
<?php
  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to database: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  $user_email = htmlentities($_POST['Email']);

  if(strlen($user_email)){
    $_SESSION["unsubscribe"] = true;
    $_SESSION["email"] = $user_email;
    RemoveUser($connection, $user_email);
    header("Location: http://54.86.139.119/Thanks.php");
  }
?>

<div class="flex_box_holder">
  <div class="unsubscribe-container">
<!-- Input form -->
    <form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
        <input type="text" name="Email" id="email_text" placeholder="Email" tabindex=2 maxlength="35" size="40" />
        <h5 id="validation_typing"></h5>
        <div class="signup-button-container">
          <input type="submit" name="validate_button" value="Unsubscribe" />
        </div
    </form>
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
      $("#email_text").css("border-color", "green");
      document.getElementsByName('validate_button')[0].disabled = false;
      document.getElementsByName('validate_button')[0].style.backgroundColor = "#3897F0";
    } else {
      if(!email.length){
        document.getElementsByName('validate_button')[0].disabled = true;
      }else{
        $("#email_text").css("border-color", "red");
        document.getElementsByName('validate_button')[0].disabled = true;
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

/* Remove User (email and interests) */
function RemoveUser($connection, $email) {
    $e = mysqli_real_escape_string($connection, $email);
    $query = "SELECT ID FROM USERS WHERE Email = '$e';";
    $result = mysqli_query($connection, $query);
    if(!$result) echo("<p>" . mysqli_error($connection) . "</p>");
    $user_id = mysqli_fetch_object($result)->ID;

    $id = mysqli_real_escape_string($connection, $user_id);
    $query = "DELETE FROM USER_INTERESTS WHERE User_ID = '$id';";
    if(!mysqli_query($connection, $query)) echo("<p>Error removing user interests.</p>");

   $query = "DELETE FROM USERS WHERE Email = '$e';";
   if(!mysqli_query($connection, $query)) echo("<p>Error removing user email.</p>");
}
?>
