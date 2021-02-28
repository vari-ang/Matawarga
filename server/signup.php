<?php header("Access-Control-Allow-Origin: *"); ?>
<?php 
    include "./connection.php";

    // Get data
    $name = addslashes(htmlentities($_POST['name']));
    $username = addslashes(htmlentities($_POST['username']));
    $password = addslashes(htmlentities($_POST['password']));

    $arr = array();

    // Create connection object
    $mysqli = new mysqli($server, $id, $pw, $db);

    // Check for errors
    if ($mysqli->connect_errno) {
        $arr['status'] = "ERROR";
        $arr['message'] = "Gagal terhubung ke database MySQL: " . $mysqli->connect_error;
    }

    // No null values
    if(empty($name)|| empty($username) || empty($password)) {
        $arr['status'] = "ERROR";
        $arr['message'] = "Tolong Isi Semua Data Yang Diminta";
    }
    else {
        // Check to see if this username is already being taken
        $stmt = $mysqli->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();

        // If username is already taken
        if($res->fetch_assoc()) {
            $arr['status'] = "ERROR";
            $arr['message'] = "Username sudah digunakan oleh orang lain. Coba dengan Username yang berbeda";
        }
        else {
            // Create salt
            $salt = md5($username . strtotime("now"));

            // Create final password
            $md5_pass = md5($password);
            $combination = $md5_pass . $salt;
            $final_password = md5($combination);

            $stmt = $mysqli->prepare("INSERT INTO user VALUES (?,?,?,?)");
            $stmt->bind_param('ssss', $username, $name, $final_password, $salt);
            $stmt->execute();

            // If INSERT command is succesfully commited
            if($stmt->affected_rows != 0) { 
                $arr['status'] = "SUCCESS";
            }
        }

        $stmt->close();
    }

    echo json_encode($arr);

    // Close connection
    $mysqli->close();
?>
