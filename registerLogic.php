<?php
header("Location: index.php");
include 'includes/connection.php';
// include('includes/productHeader.inc.php');
// include('includes/navBar.inc.php');

var_dump($_POST);

$c = new Connection();
$db = $c->getDb();
function get_client_ip_server() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
  
    return $ipaddress;
  }

date_default_timezone_set('Europe/London');
$ipAddress = get_client_ip_server();
$dateAndTime = date('Y-m-d H:i:s');

//checks whether the form has been submitted if not shows the form
    if(!empty($_POST))
    {
        if(empty($_POST['username']))
        {
          echo "<script>
          alert(\"Please enter a username\");
          window.history.back();
          </script>";
        }
        //if empty statements to check whether fields are complete
        if(empty($_POST['password']))
        {
          echo "<script>
          alert(\"Please enter a password\");
          window.history.back();
          </script>";
        }
        //ensure that the email address is actually valid using filter command
        // if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        // {
        //   echo "<script>
        //   alert(\"Your Email address is invalid. Please try again\");
        //
        //   </script>";
        //     //die("Invalid E-Mail Address");
        // }
        // select query to check in the database whether the username is already registered
        // using tokens for this ':username' to help prevent SQL injection
        $query = "SELECT 1
            FROM users
            WHERE
                username = :username";
        $query_params = array('username' => $_POST['username']);
        // try and catch command to run the query in the SQL database
        try
        {
            $state1 = $db->prepare($query);
            $result = $state1->execute($query_params);
        }
        catch(PDOException $ex)
        {
            die("Failed to run query: " . $ex->getMessage());
        }
        //fetch method to bring the results back
        //if a result is found then the username must already exist
        $row = $state1->fetch();

        if($row)
        {
            echo "<script>
            alert(\"This username is already in use. Please try again\");
            window.history.back();
            </script>";
        }

        $query = "SELECT 1
            FROM users
            WHERE
                email = :email";

        $query_params = array(
            'email' => $_POST['email']);

        try
        {
            $state1 = $db->prepare($query);
            $result = $state1->execute($query_params);
        }
        catch(PDOException $ex)
        {
            die("Failed to run query: " . $ex->getMessage());
        }

        $row = $state1->fetch();

        if($row)
        {
          echo "<script>
          alert(\"Your Email address is already in use. Please try again\");
          window.history.back();
          </script>";
            //die("This email address is already registered");
        }
        //insert command to insert the details into the database
        $query = "INSERT INTO users (
                firstname,
                secondname,
                username,
                password,
                acctype,
                salt,
                email,
                RegisterDate,
                IP
            ) VALUES (
                :firstname,
                :secondname,
                :username,
                :password,
                :acctype,
                :salt,
                :email,
                :todaydate,
                :userip)";
        //salts used to protect against brute force attacks
        //this produces and 8 byte salt
        $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
        //sha256 used here to hash the password and salt together to be stored
        //securely within the database
        //this produces a 64 byte hex meaning the orignal password cannot be obtained
        $password = hash('sha256', $_POST['password'] . $salt);
        // for command to has the value 65536 times make it virtually impossible
        // to brute force attack
        for($round = 0; $round < 65536; $round++)
        {
            $password = hash('sha256', $password . $salt);
        }
        // the tokens are now prepared to be inserted within the database
        $query_params = array(
            'firstname' => $_POST['firstname'],
            'secondname' => $_POST['secondname'],
            'username' => $_POST['username'],
            'password' => $password,
            'acctype' => "U",
            'salt' => $salt,
            'email' => $_POST['email'],
            'todaydate' => $dateAndTime,
            'userip' => $ipAddress
        );
        // try command to insert the user into the database
        try
        {
            $state1 = $db->prepare($query);
            $result = $state1->execute($query_params);
            echo $result;
        }
        catch(PDOException $ex)
        {
            die("Failed to run query: " . $ex->getMessage());
        }
        //header command to redirect the user to the login page
        
        //die command to prevent the php script executing when leaving
        //die("Redirecting to index.php");
    }

?>