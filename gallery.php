<html>
<head><title>Gallery</title>
 <!--jQuery-->
  <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
  <!--Fotorama-->
  <link href="fotorama.css" rel="stylesheet">
  <script src="fotorama.js"></script>
</head>
<body>
<div class="fotorama" data-width="800" data-ratio="800/567" data-max-width="100%">


<?php
// NOTE: code provided by Jeremy Hajek is modified.
session_start();
$email = $_POST["email"];
echo $email;

require 'vendor/autoload.php';
//create client for s3 bucket
//use Aws\Rds\RdsClient;
//$client = RdsClient::factory(array(
//'region'  => 'us-east-1'
//));
$rds = new Aws\Rds\RdsClient([
  'version' => 'latest',
  'region'  => 'us-east-1'
  
]);
$result = $rds->describeDBInstances(array('DBInstanceIdentifier' => 'ITMO-544-Database',
));

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];

//echo "begin database";
$link = mysqli_connect($endpoint,"controller","ilovebunnies","ITMO-544-Database") or die("Error " . mysqli_error($link));

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

//below line is unsafe - $email is not checked for SQL injection -- don't do this in real life or use an ORM instead
$link->real_query("SELECT * FROM ITM0-544-Table");
$res = $link->use_result();
while ($row = $res->fetch_assoc())
{
  echo "<img src =\"" . $row['rawS3Url']."\" /><img src =\"" .$row['finishedS3Url'] . "\"/>";
  echo $row['id'] . "Email: " . $row['email'];
}
$link->close();
?>
</div>
</body>
</html>
