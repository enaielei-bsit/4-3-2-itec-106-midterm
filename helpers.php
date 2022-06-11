<?php
    require_once(__DIR__ . "/utils.php");

    function user() {
    }

    function signIn($user) {
        startSession();
        $_SESSION["user_id"] = $user["id"];
    }

    function signOut() {
        startSession();
        session_unset();
        session_destroy();
    }

    function signedIn($true=null, $false=null) {
        StartSession();
        if(isset($_SESSION["user_id"])) {
            try {
                $query = query("select * from users where id = ?", [$_SESSION["user_id"]]);
                if($query->rowCount() > 0) {
                    if($true) header("Location: {$true}");
                    $user = $query->fetch();
                    return $user;
                }
            } catch(Throwable $th) {}
        }

        if($false) header("Location: {$false}");
        return null;
    }

    function startSession() {
        if(session_status() === PHP_SESSION_NONE) session_start();
    }
?>