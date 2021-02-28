<?php header("Access-Control-Allow-Origin: *"); ?>
<?php
    // Get input
    $username = addslashes(htmlentities($_GET['username']));
    $idkejadian = addslashes(htmlentities($_GET['idkejadian']));

    $arr = array();

    if(!empty($username)) {
        include "./connection.php";

        // Create connection object
        $mysqli = new mysqli($server, $id, $pw, $db);

        // Check for connection error
        if ($mysqli->connect_errno) {
            $arr['status'] = "ERROR";
            $arr['message'] = "Gagal terhubung ke database MySQL: " . $mysqli->connect_error;
        }

        // Check if the username has already liked this incident
        $stmt = $mysqli->prepare("SELECT * FROM like_kejadian 
                                    WHERE idkejadian = ? AND username = ?");
        $stmt->bind_param('is', $idkejadian, $username);
        $stmt->execute();

        $res = $stmt->get_result();

        // If search results show that the username has already liked this incident
        if($res->num_rows !== 0) {
            // Minus the like count from this incident --> remove username from `like_kejadian` table
            $stmt2 = $mysqli->prepare("DELETE FROM like_kejadian 
                                       WHERE idkejadian = ? AND username = ?");
            $stmt2->bind_param('is', $idkejadian , $username);
           
            // If delete command is succesfully executed
            if($stmt2->execute()) { 
                $arr['status'] = "SUCCESS";
                $arr['message'] = "UNLIKED"; 
            }
            else { 
                $arr['status'] = "ERROR";
                $arr['message'] = "Terjadi kendala men-dislike postingan ini";
             }

            $stmt2->close();
        }
        // If the username has NOT already liked this incident
        else {
            // Add the like count from this incident --> add username from `like_kejadian` table
            $stmt2 = $mysqli->prepare("INSERT INTO like_kejadian (idkejadian, username)
                                       VALUES (?,?)");
            $stmt2->bind_param('is', $idkejadian , $username);

            // Jika query berhasil
            if($stmt2->execute()) {
                $arr['status'] = "SUCCESS";
                $arr['message'] = "LIKED";
            }
            else { 
                $arr['status'] = "ERROR";
                $arr['message'] = "Terjadi kendala men-like postingan ini";
            }

            $stmt2->close();
        }

        $stmt->close();

        echo json_encode($arr);

        /* close connection */
        $mysqli->close();
    }
?>