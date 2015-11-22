<?php
// This script is the modified version of "submit.php" provided by Jeremy Hajek.

echo "Hello";
session_start();
var_dump($_POST);
if(!empty($_POST)){
echo $_POST['useremail'];
echo $_POST['phone'];
echo $_POST['firstname'];
$_SESSION['firstname']=$_POST['firstname'];
$_SESSION['phone']=$_POST['phone'];
$_SESSION['useremail']=$_POST['useremail'];
}

else
{
echo "post empty";
}

$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
print '<pre>';

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
  echo "File is valid, and successfully uploaded.\n";
} else {
    echo "Possible file upload!\n";
}
echo 'Here is some more debugging info:';
print_r($_FILES);
print "</pre>";


require 'vendor/autoload.php';
#use Aws\S3\S3Client;
#$client = S3Client::factory();
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
#print_r($s3);
$bucket = uniqid("CharlieBucketsGallore",false);
#$result = $s3->createBucket(array(
#    'Bucket' => $bucket
#));
#
## AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);
# PHP version 3
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => $uploadfile,
   'ContentType' => $_FILES['userfile']['type'],
   'Body' => fopen($uploadfile,'r+')
]);  
$url = $result['ObjectURL'];
echo $url;

$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$result = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'ITMO-544-Database'
));	
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    echo "============\n". $endpoint . "================";
//echo "begin database";^M
$link = mysqli_connect($endpoint,"controller","ilovebunnies","CloudProject") or die("Error " . mysqli_error($link));
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
else {
echo "All is good";
}

#Creating SNS client
$result = new Aws\Sns\SnsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$result1 = $result->listTopics(array(
    
));
foreach ($result1['Topics'] as $key => $value){
if(preg_match("/ImageTopicSK/", $result1['Topics'][$key]['TopicArn'])){
$topicARN =$result['Topics'][$key]['TopicArn'];
}
}


if (!($stmt = $link->prepare("INSERT INTO ITMO-544-Table (uName,email,phone,rawS3Url,finishedS3Url,jpgFileName,state) VALUES (?,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}
$uName="Mahak Patil";
$email = $_POST['useremail'];
$phone = $_POST['phone'];
$rawS3Url = $url; 
$finishedS3Url = "none";
$jpgFileName = basename($_FILES['userfile']['name']);
$state=0;

$res = $link->query("SELECT * FROM ITMO-544-Table where email='$email'");
if($res->num_rows>0){
if (!($stmt = $link->prepare("INSERT INTO ITMO-544-Table (uName,email,phone,rawS3Url,finishedS3Url,jpgFileName,state) VALUES (?,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}

$stmt->bind_param("ssssssi",$uName,$email,$phone,$rawS3Url,$finishedS3Url,$jpgFileName,$state);
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno0 . ") " . $stmt->error;
}

printf("%d Row inserted.\n", $stmt->affected_rows);
$stmt->close();

$pub = $result->publish(array(
    'TopicArn' => $topicARN,
    'Subject' => 'ITMO-544',
    'Message' => 'Here is a sample message',   
));



$link->real_query("SELECT * FROM ITMO-544-Table");
$res = $link->use_result();
echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    echo $row['id'] . " " . $row['email']. " " . $row['phone'];
}
$link->close();
$url = "gallery.php";
header('Here it is -> ' . $url, true);
die();
}
else{
    $url	= "temp.php";
   header('Location: ' . $url, true);
   die();
}
?> 