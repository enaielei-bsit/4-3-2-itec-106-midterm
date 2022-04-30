<?php require_once("./_init.php") ?>
<?php
    $user = signedIn(null, "./sign-in.php");
?>
<?php require_once("./_start.php"); ?>
<a class="link-button" href="#sign-out" onclick="confirm('Are you sure you want to sign out?') ? window.location='./sign-out.php' : null"><< Sign Out</a>
<a class="link-button" href="./account.php">Account</a>
<h1>Welcome <?= $user["name"] ?>!!!</h1>
<em>Hello and Good Morning!</em>
<br>
<?php require_once("./_end.php"); ?>