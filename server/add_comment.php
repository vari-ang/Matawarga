<?php header("Access-Control-Allow-Origin: *"); ?>
<?php
    include "./connection.php";

    // Create connection object
    $mysqli = new mysqli($server, $id, $pw, $db);

    // Check for connection error
    if ($mysqli->connect_errno) {
        $arr['status'] = "ERROR";
        $arr['message'] = "Gagal terhubung ke database MySQL: " . $mysqli->connect_error;
    }

    // Get input
    $username = $_POST["username"];
    $idkejadian = $_POST["idkejadian"];
    $comment = $_POST["comment"];

    $arr = array();

    // Check for empty values
    if(empty($comment)) {
        // Show error message
        $arr['status'] = "ERROR";
        $arr['message'] = "Tolong Isi Semua Data Yang Diminta";
    }
    else {
        $stmt = $mysqli->prepare("INSERT INTO komen_kejadian(idkejadian, username, komentar) 
                                      VALUES (?,?,?)");
        $stmt->bind_param('iss', $idkejadian, $username, $comment);

        // Jika query berhasil
        if($stmt->execute()) { $arr['status'] = "SUCCESS"; }
        else { 
            $arr['status'] = "ERROR";
            $arr['message'] = "Gagal menambahkan komen. Silahkan coba kembali";
        }
        
        $stmt->close();
    }

    echo json_encode($arr);

    /* close connection */
    $mysqli->close();
?>