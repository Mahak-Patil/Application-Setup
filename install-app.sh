#!/bin/bash
# make_page - A script to produce an HTML file

cat << _EOF_


<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>$title $HOSTNAME</title>
  <meta name="description" content="ITM0-544-Week-05">
  <meta name="author" content="SitePoint">

  <link rel="stylesheet" href="css/styles.css?v=1.0">
</head>

<body>
  <h1>MAHAK PATIL</h1>
  <h1>ITMO 544</h1>
  <a href="https://s3.amazonaws.com/Bucket-2-for-itmo-544/Mahak-Patil.jpg">Clic$
  <br><br>
  <a href="page2.html">PAGE 2</a>
</body>
</html>
_EOF_

