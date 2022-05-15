<?php
    $database_server=getenv("DATABASE_SERVER");
    $database_database=getenv("DATABASE_DATABASE");
    $database_user=getenv("DATABASE_USER");
    $database_password=getenv("DATABASE_PASSWORD");

    $database = new mysqli($database_server, $database_user, $database_password, $database_database);
    if ($database->connect_errno) {
        throw new RuntimeException('mysqli connection error: ' . $mysqli->connect_error);
    }

    session_start();
    
    $date = (isset($_GET["date"])) ? '"'.$_GET["date"].'"' : 'DATE_FORMAT(CURRENT_TIMESTAMP(), "%b %y")';
    

    if (isset($_POST["function"])) {
        switch ($_POST["function"]) {
            case "login":
                $result = $database->query('SELECT id_user FROM users WHERE username = "'.$_POST["username"].'" AND password = "'.$_POST["password"].'"');
                if ($result->num_rows == 1) {
                    $_SESSION["user_id"] = $result->fetch_row()[0];
                }
                break;
            case "logout":
                session_destroy();
                session_start();
                break;
            case "insert-entry":
                if (isset($_SESSION["user_id"])) {
                    $database->query('CALL insert_entry ('.$_SESSION["user_id"].', "'.$_POST["date"].'", "'.$_POST["start-time"].'", "'.$_POST["end-time"].'");');
                }
                break;
            case "delete-entry":
                if (isset($_SESSION["user_id"])) {
                    $database->query('CALL delete_entry ("'.$_POST["tracker-id"].'", '.$_SESSION["user_id"].');');
                }
                break;
        }
    }

    // Check if user is logged in
    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        include_once 'modules/main.php';
    } else {
        include_once 'modules/login.php';
    }

?>
