<?php require_once("./_init.php") ?>
<?php
    $user = signedIn("./index.php");

    $messages = [];
    $redirect = null;
    if(check($_POST, "submit", "account")) {
        $user_ = $_POST["user"];
        keep($user_, "email", "new_password", "new_password_confirmation");
        apply($user_, fn($k, $v) => trim($v));
        $subtitles = [];

        if(empty($user_["email"])) array_push($subtitles, "Email must not be empty.");
        else {
            $query = query("select id, password from users where email = ?", [$user_["email"]]);
            if($query->rowCount() == 0)
                array_push($subtitles, "Email does not exist.");
        }

        if(empty($subtitles)) {
            if(!empty($user_["new_password"]) && strlen($user_["new_password"]) < 7)
                array_push($subtitles, "Password must be 7 characters or more.");
    
            if(!empty($user_["new_password_confirmation"])
                && !empty($user_["new_password"])
                && strlen($user_["new_password"]) >= 7
                && $user_["new_password"] != $user_["new_password_confirmation"])
                array_push($subtitles, "New Password Confirmation must match New Password.");
        }

        if(!empty($subtitles)) array_push($messages, [
            "type" => "negative",
            "title" => "Account Update Failed",
            "subtitles" => $subtitles
        ]);
        else {
            try {
                $password = pop($user_, "new_password");
                pop($user_, "new_password_confirmation");
                pop($user_, "password");
                
                if(!empty($password))
                    $user_["password"] = password_hash($password, null);

                if(!empty($user_)) {
                    $columns = join(", ", array_map(fn($e) => "{$e} = :{$e}", array_keys($user_)));
                    $user_["email"];
                    query("update users set {$columns} where email = :email", $user_);

                    array_push($messages, [
                        "type" => "positive",
                        "title" => "Account Update Succeeded",
                        "subtitle" => "Your account information has been updated."
                    ]);

                    if(isset($user_["password"])) {
                        $messages[count($messages) - 1]["subtitle"] .= " You can now try accessing the account with these new credentials.";
                        $redirect = "./sign-out.php";
                        signOut();
                    }
                } else {
                    array_push($messages, [
                        "type" => "neutral",
                        "title" => "Account Update Ignored",
                        "subtitle" => "No changes were made."
                    ]);
                }
            } catch (\Throwable $th) {
                echo var_dump($th);
                array_push($messages, [
                    "type" => "negative",
                    "title" => "Account Update Failed",
                    "subtitle" => "Something went wrong while updating your account."
                ]);
            }
        }
    }
?>
<?php require_once("./_start.php"); ?>
<a class="link-button" href="./index.php"><< Home</a>
<h1>Password Reset</h1>
<em>Change your existing password.</em>
<br>
<?php render("./messages.php", ["messages" => $messages]) ?>
<form action="./reset-password.php" method="post">
    <div class="field">
        <label for="user_email">
            Email <?php render("./hint.php", ["char" => "*",
                "title" => "Required"]); ?>
        </label>
        <input type="email" name="user[email]" id="user_email" required>
    </div>
    <div class="field">
        <label for="user_new_password">
            New Password <?php render("./hint.php", ["char" => "?",
                "title" => "Must be 7 or more characters long"]); ?>
        </label>
        <input type="password" name="user[new_password]" id="user_new_password" minlength="7">
    </div>
    <div class="field">
        <label for="user_new_password_confirmation">
            New Password Confirmation
        </label>
        <input type="password" name="user[new_password_confirmation]" id="user_new_password_confirmation">
    </div>
    <div class="field">
        <button type="submit" name="submit" value="account">Update</button>
    </div>
</form>
<?php require_once("./_end.php"); ?>
<?php if($redirect != null) { ?>
    <script>
        setTimeout(function() {
            window.location="<?= $redirect ?>";
        }, 7000);
    </script>
<?php } ?>