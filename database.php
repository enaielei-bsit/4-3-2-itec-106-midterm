<?php
    require_once(__DIR__ . "/vendor/autoload.php");

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    function getDSN($dbms, $host, $port, $dbname, $user, $password) {
        return "{$dbms}:host={$host};port={$port};dbname={$dbname};user={$user};password={$password}";
    }

    function query($string, $params=[]) {
        global $pdo;

        $query = $pdo->prepare($string);
        $query->execute($params);
        return $query;
    }

    try {
        $dsn = array(
            "dbms" => $_ENV["DBMS"] ?? "",
            "host" => $_ENV["HOST"] ?? "",
            "port" => $_ENV["PORT"] ?? "",
            "dbname" => $_ENV["DBNAME"] ?? "",
            "user" => $_ENV["USER"] ?? "",
            "password" => $_ENV["PASSWORD"] ?? ""
        );
        
        if(isset($_ENV["DATABASE_URL"])) {
            $db = parse_url($_ENV["DATABASE_URL"]);
            $dsn["dbms"] = "pgsql";
            $dsn["host"] = $db["host"];
            $dsn["port"] = $db["port"];
            $dsn["dbname"] = ltrim($db["path"], "/");
            $dsn["user"] = $db["user"];
            $dsn["password"] = $db["pass"];
        }
        
        $pdo = new PDO(
            getDSN($dsn["dbms"], $dsn["host"], $dsn["port"],
                $dsn["dbname"], $dsn["user"], $dsn["password"])
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\Throwable $th) {
        die("Cannot connect to the Database.<br>" . $th->getMessage());
    }
?>