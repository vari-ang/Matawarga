<?php header("Access-Control-Allow-Origin: *"); ?>
<?php 
    $incidentId = $_GET['id'];
    $username = addslashes(htmlentities($_GET['username']));

    if(!empty($incidentId)) {
        include "./connection.php";

        // Create connection object
        $mysqli = new mysqli($server, $id, $pw, $db);

        // Check for errors
        if ($mysqli->connect_errno) {
            echo "Gagal terhubung ke database MySQL: " . $mysqli->connect_error;
        }

        // Get specified incidents
        $stmt = $mysqli->prepare("SELECT * FROM kejadian WHERE idkejadian = ?");
        $stmt->bind_param('i', $incidentId);
        $stmt->bind_param();
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $incident = array();
        $incident['idkejadian'] = addslashes(htmlentities($row['idkejadian']));
        $incident['username'] = addslashes(htmlentities($row['username']));
        $incident['judul'] = addslashes(htmlentities($row['judul']));
        $incident['deskripsi'] = addslashes(htmlentities($row['deskripsi']));
        $incident['instansi_tujuan'] = addslashes(htmlentities ($row['instansi_tujuan']));
        $incident['tanggal'] = date('l, j M Y H:i', strtotime(addslashes(htmlentities($row['tanggal']))));
        $incident['longitude'] = addslashes(htmlentities ($row['longitude']));
        $incident['latitude'] = addslashes(htmlentities ($row['latitude']));

        // Get all images for this incident id
        $stmt2 = $mysqli->prepare("SELECT * FROM gambar_kejadian WHERE idkejadian = ?");
        $stmt2->bind_param('i', $incidentId);
        $stmt2->execute();

        $res2 = $stmt2->get_result();
        $gambars = array();
        while($row2 = $res2->fetch_assoc()) {
            $gambars[] = addslashes(htmlentities($row2['idgambar'] . '.' . $row2['extension']));
        }
        $incident['link_gambars'] = $gambars;
        
        // Get likes count for this incident id
        $stmt3 = $mysqli->prepare("SELECT COUNT(*) AS jumlah_like FROM like_kejadian WHERE idkejadian = ?");
        $stmt3->bind_param('i', $incidentId);
        $stmt3->execute();

        $res3 = $stmt3->get_result();
        $row3 = $res3->fetch_assoc();
        $incident['jumlah_like'] = isset($row3['jumlah_like']) ? $row3['jumlah_like'] : 0;

        // Get comments count for this incident id
        $stmt4 = $mysqli->prepare("SELECT COUNT(*) AS jumlah_komen FROM komen_kejadian WHERE idkejadian = ?");
        $stmt4->bind_param('i', $incidentId);
        $stmt4->execute();

        $res4 = $stmt4->get_result();
        $row4 = $res4->fetch_assoc();
        $incident['jumlah_komen'] =isset($row4['jumlah_komen']) ? $row4['jumlah_komen'] : 0;

        // Check if the username has already liked this incident
        $stmt5 = $mysqli->prepare("SELECT * FROM like_kejadian 
                                    WHERE idkejadian = ? AND username = ?");
        $stmt5->bind_param('is', $incidentId, $username);
        $stmt5->execute();

        $res5 = $stmt5->get_result();
        $incident['liked'] = ($res5->num_rows !== 0) ? true : false;

        // Get all comments
        $stmt6 = $mysqli->prepare("SELECT * FROM komen_kejadian WHERE idkejadian = ?");
        $stmt6->bind_param('i', $incidentId);
        $stmt6->execute();

        $res6 = $stmt6->get_result();
        $komens = array();
        while($row6 = $res6->fetch_assoc()) {
            $komen = array();
            $komen["username"] = addslashes(htmlentities($row6['username']));
            $komen["komentar"] = addslashes(htmlentities($row6['komentar']));
            $komens[] = $komen;
        }
        $incident['komens'] = $komens;

        echo json_encode($incident);

        $stmt->close();
        $stmt2->close();
        $stmt3->close();
        $stmt4->close();
        $stmt5->close();
        $stmt6->close();

        // Close connection
        $mysqli->close();
    } 
?>
