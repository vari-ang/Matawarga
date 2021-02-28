<?php header("Access-Control-Allow-Origin: *"); ?>
<?php 
    include "./connection.php";

    // Get data
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
    if(empty($username) || empty($password)) {
        $arr['status'] = "ERROR";
        $arr['message'] = "Tolong Isi Semua Data Yang Diminta";
    }
    else {
        $stmt = $mysqli->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        
        // Jika login benar
        $row = $res->fetch_assoc();

        if($row) {
            // re-create final password from salt & typed password from user
            $md5_pass = md5($password);
            $combination = $md5_pass . $row["salt"];
            $final_password = md5($combination);

            // Jika hasil enkripsi password berbeda dengan yg ada di database
            if($final_password == $row["password"]) {
                $arr['status'] = "SUCCESS";
            }
            else {
                $arr['status'] = "ERROR";
                $arr['message'] = "Password salah. Silahkan coba lagi";
            }
        }
        else {
            $arr['status'] = "ERROR";
            $arr['message'] = "Username tidak ditemukan. Silahkan coba lagi";
        }
        $stmt->close();
    }

    echo json_encode($arr);

    // Close connection
    $mysqli->close();
?>
