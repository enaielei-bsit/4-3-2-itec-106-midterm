<?php require_once("./_init.php"); ?>
<?php
    $user = signedIn("./index.php");

    $messages = [];
    if(check($_POST, "submit", "sign-in")) {
        $session = $_POST["session"];
        keep($session, "email", "password");
        apply($session, fn($k, $v) => trim($v));
        $subtitles = [];
        $user = null;

        if(empty($session["email"])) array_push($subtitles, "Email must not be empty.");
        else {
            $query = query("select id, password from users where email = ?", [$session["email"]]);
            if($query->rowCount() == 0)
                array_push($subtitles, "Email does not exist.");
            else $user = $query->fetch();
        }

        if(empty($session["password"])) array_push($subtitles, "Password must not be empty.");
        elseif($user) {
            if(!password_verify($session["password"], $user["password"])) {
                array_push($subtitles, "Password is incorrect.");
            }
        }

        if(!empty($subtitles)) array_push($messages, [
            "type" => "negative",
            "title" => "Account Authentication Failed",
            "subtitles" => $subtitles
        ]);
        else {
            try {
                signIn($user);
                header("Location: ./index.php");
                // array_push($messages, [
                //     "type" => "positive",
                //     "title" => "Account Authentication Succeeded",
                //     "subtitle" => "Your account has been registered. You may now try signing in this account."
                // ]);
            } catch (\Throwable $th) {
                echo var_dump($th);
                array_push($messages, [
                    "type" => "negative",
                    "title" => "Account Authentication Failed",
                    "subtitle" => "Something went wrong while authenticating your account."
                ]);
            }
        }
    }
?>
<?php require_once("./_start.php"); ?>
<a class="link-button" href="./sign-up.php"><< Sign Up</a>
<a class="link-button" href="./reset-password.php">Reset Password</a>
<h1>Sign In</h1>
<em>Already have an account? Sign in and gain access!</em>
<br>
<?php render("./messages.php", ["messages" => $messages]) ?>
<form action="./sign-in.php" method="post">
    <div class="field">
        <label for="session_email">
            Email <?php render("./hint.php", ["char" => "*",
                "title" => "Required"]); ?>
        </label>
        <input type="email" name="session[email]" id="session_email" required>
    </div>
    <div class="field">
        <label for="session_password">
            Password <?php render("./hint.php", ["char" => "*",
                "title" => "Required"]); ?>
        </label>
        <input type="password" name="session[password]" id="session_password" required>
    </div>
    <div class="field">
        <button type="submit" name="submit" value="sign-in">Submit</button>
    </div>
</form>
<?php require_once("./_end.php"); ?>