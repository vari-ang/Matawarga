<?php header("Access-Control-Allow-Origin: *"); ?>
<?php 
    $username = addslashes(htmlentities($_GET['username']));

    if(!empty($username)) {
        include "./connection.php";

        // Create connection object
        $mysqli = new mysqli($server, $id, $pw, $db);

        // Check for errors
        if ($mysqli->connect_errno) {
            echo "Gagal terhubung ke database MySQL: " . $mysqli->connect_error;
        }

        // Get all incidents
        $stmt = $mysqli->prepare("SELECT * FROM kejadian ORDER BY tanggal DESC");
        $stmt->bind_param();
        $stmt->execute();

        $res = $stmt->get_result();
        if($res->num_rows > 0) {
            $incidents = array();
            $i = 0;

            while($row = $res->fetch_assoc()) {
                $incidents[$i]['idkejadian'] = addslashes(htmlentities($row['idkejadian']));
                $incidents[$i]['username'] = addslashes(htmlentities($row['username']));
                $incidents[$i]['judul'] = addslashes(htmlentities($row['judul']));
                $incidents[$i]['deskripsi'] = addslashes(htmlentities($row['deskripsi']));
                $incidents[$i]['instansi_tujuan'] = addslashes(htmlentities($row['instansi_tujuan']));
                $incidents[$i]['tanggal'] = date('l, j M Y H:i', strtotime(addslashes(htmlentities($row['tanggal']))));

                // Get first image for this incident id
                $stmt2 = $mysqli->prepare("SELECT idgambar, idkejadian, extension FROM gambar_kejadian 
                    WHERE idkejadian = ? LIMIT 1");
                $stmt2->bind_param('i', $row['idkejadian']);
                $stmt2->execute();

                $res2 = $stmt2->get_result();
                $row2 = $res2->fetch_assoc();
                $incidents[$i]['link_gambar'] = addslashes(htmlentities($row2['idgambar'] . '.' . $row2['extension']));
                
                // Get likes count for this incident id
                $stmt3 = $mysqli->prepare("SELECT COUNT(*) AS jumlah_like FROM like_kejadian WHERE idkejadian = ?");
                $stmt3->bind_param('i', $row['idkejadian']);
                $stmt3->execute();

                $res3 = $stmt3->get_result();
                $row3 = $res3->fetch_assoc();
                $incidents[$i]['jumlah_like'] = isset($row3['jumlah_like']) ? $row3['jumlah_like'] : 0;

                // Get comments count for this incident id
                $stmt4 = $mysqli->prepare("SELECT COUNT(*) AS jumlah_komen FROM komen_kejadian WHERE idkejadian = ?");
                $stmt4->bind_param('i', $row['idkejadian']);
                $stmt4->execute();

                $res4 = $stmt4->get_result();
                $row4 = $res4->fetch_assoc();
                $incidents[$i]['jumlah_komen'] =isset($row4['jumlah_komen']) ? $row4['jumlah_komen'] : 0;

                // Check if the username has already liked this incident
                $stmt5 = $mysqli->prepare("SELECT * FROM like_kejadian 
                                            WHERE idkejadian = ? AND username = ?");
                $stmt5->bind_param('is', $row['idkejadian'], $username);
                $stmt5->execute();

                $res5 = $stmt5->get_result();
                $incidents[$i]['liked'] = ($res5->num_rows !== 0) ? true : false;

                $i++;

                $stmt2->close();
                $stmt3->close();
                $stmt4->close();
                $stmt5->close();
            }

            echo json_encode($incidents);
        }

        $stmt->close();

        // Close connection
        $mysqli->close();
    }
?>
