<?php require_once("./_init.php"); ?>
<?php
    $user = signedIn("./index.php");

    $messages = [];
    if(check($_POST, "submit", "sign-up")) {
        $user = $_POST["user"];
        keep($user, "email", "name", "password", "password_confirmation");
        apply($user, fn($k, $v) => trim($v));
        $subtitles = [];

        if(empty($user["email"])) array_push($subtitles, "Email must not be empty.");
        else {
            $query = query("select id from users where email = ?", [$user["email"]]);
            if($query->rowCount() > 0)
                array_push($subtitles, "Email already exists."); 
        }

        if(empty($user["name"])) array_push($subtitles, "Name must not be empty.");

        if(empty($user["password"])) array_push($subtitles, "Password must not be empty.");
        elseif(strlen($user["password"]) < 7) array_push($subtitles, "Password must be 7 characters or more.");

        if(!empty($user["password_confirmation"]) && !empty($user["password"])
            && strlen($user["password"]) >= 7
            && $user["password"] != $user["password_confirmation"])
            array_push($subtitles, "Password Confirmation must match Password.");

        if(!empty($subtitles)) array_push($messages, [
            "type" => "negative",
            "title" => "Account Registration Failed",
            "subtitles" => $subtitles
        ]);
        else {
            try {
                pop($user, "password_confirmation");
                $user["password"] = password_hash($user["password"], null);
                $keys = array_keys($user);
                $columns = join(", ", $keys);
                $values = join(", ", array_map(fn($e) => ":{$e}", $keys));
                query("insert into users ({$columns}) values ({$values})", $user);

                array_push($messages, [
                    "type" => "positive",
                    "title" => "Account Registration Succeeded",
                    "subtitle" => "Your account has been registered. You may now try signing in this account."
                ]);
            } catch (\Throwable $th) {
                echo var_dump($th);
                array_push($messages, [
                    "type" => "negative",
                    "title" => "Account Registration Failed",
                    "subtitle" => "Something went wrong while registering your account."
                ]);
            }
        }
    }
?>
<?php require_once("./_start.php"); ?>
<a class="link-button" href="./sign-in.php"><< Sign In</a>
<h1>Sign Up</h1>
<em>New User? Sign up for an account!</em>
<br>
<?php render("./messages.php", ["messages" => $messages]) ?>
<form action="./sign-up.php" method="post">
    <div class="field">
        <label for="user_email">
            Email <?php render("./hint.php", ["char" => "*",
                "title" => "Required and must be unique"]); ?>
        </label>
        <input type="email" name="user[email]" id="user_email" required>
    </div>
    <div class="field">
        <label for="user_name">
            Name <?php render("./hint.php", ["char" => "*",
                "title" => "Required"]); ?>
        </label>
        <input type="text" name="user[name]" id="user_name" required>
    </div>
    <div class="field">
        <label for="user_password">
            Password <?php render("./hint.php", ["char" => "*",
                "title" => "Required and must be 7 or more characters long"]); ?>
        </label>
        <input type="password" name="user[password]" id="user_password" minlength="7" required>
    </div>
    <div class="field">
        <label for="user_password_confirmation">
            Password Confirmation
        </label>
        <input type="password" name="user[password_confirmation]" id="user_password_confirmation" minlength="7">
    </div>
    <div class="field">
        <button type="submit" name="submit" value="sign-up">Submit</button>
    </div>
</form>
<?php require_once("./_end.php"); ?>