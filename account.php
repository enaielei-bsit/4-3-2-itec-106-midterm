<?php require_once("./_init.php") ?>
<?php
    $user = signedIn(null, "./sign-in.php");

    $messages = [];
    $redirect = null;
    if(check($_POST, "submit", "account")) {
        $user_ = $_POST["user"];
        keep($user_, "name", "new_password", "new_password_confirmation", "password");
        apply($user_, fn($k, $v) => trim($v));
        $subtitles = [];

        if(empty($user_["password"])) array_push($subtitles, "Password must not be empty.");
        elseif($user) {
            if(!password_verify($user_["password"], $user["password"])) {
                array_push($subtitles, "Password is incorrect.");
            }
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
                $name = pop($user_, "name");
                $password = pop($user_, "new_password");
                pop($user_, "new_password_confirmation");
                pop($user_, "password");
                
                if(!empty($name) && $name != $user["name"])
                    $user_["name"] = $name;
                
                if(!empty($password) && !password_verify($password, $user["password"]))
                    $user_["password"] = password_hash($password, null);

                if(!empty($user_)) {
                    $columns = join(", ", array_map(fn($e) => "{$e} = :{$e}", array_keys($user_)));
                    $user_["email"] = $user["email"];
                    query("update users set {$columns} where email = :email", $user_);

                    array_push($messages, [
                        "type" => "positive",
                        "title" => "Account Update Succeeded",
                        "subtitle" => "Your account information has been updated."
                    ]);

                    $user = signedIn();

                    if(isset($user_["password"])) {
                        $messages[count($messages) - 1]["subtitle"] .= " For security purposes, you need to sign in your account again.";
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
<h1>Account</h1>
<em>Change and update your account information.</em>
<br>
<?php render("./messages.php", ["messages" => $messages]) ?>
<form action="./account.php" method="post">
    <div class="field">
        <label for="user_email">
            Email
        </label>
        <input type="email" id="user_email" value="<?= $user["email"] ?>" readonly disabled>
    </div>
    <div class="field">
        <label for="user_name">
            Name <?php render("./hint.php", ["char" => "?",
                "title" => "Must not be empty"]); ?>
        </label>
        <input type="text" name="user[name]" value="<?= $user["name"] ?>" id="user_name">
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
    <br>
    <small>For added security, you need to enter your current password first before making any changes to your account.</small>
    <div class="field">
        <label for="user_password">
            Password
        </label>
        <input type="password" name="user[password]" id="user_password" required>
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