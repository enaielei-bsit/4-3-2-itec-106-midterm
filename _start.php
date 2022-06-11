<?php
    // Source: https://stackoverflow.com/a/30021074/14733693
    $project = explode('/', $_SERVER['REQUEST_URI'])[1];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT 4-3 | ITEC 106 | Midterm-Finals</title>

    <link rel="stylesheet" href="<?= "/$project/style.css" ?>">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body>
    <div id="main">