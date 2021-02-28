<?php header("Access-Control-Allow-Origin: *"); ?>
<?php 
    $q = addslashes(htmlentities($_GET['q']));

    $search_results = array();
    if(!empty($q)) {
        include "./connection.php";

        // Create connection object
        $mysqli = new mysqli($server, $id, $pw, $db);

        // Check for errors
        if ($mysqli->connect_errno) {
            echo "Gagal terhubung ke database MySQL: " . $mysqli->connect_error;
        }

        // Do `select` query from `kejadian` table
        $stmt = $mysqli->prepare("SELECT idkejadian, judul FROM kejadian WHERE judul LIKE ?");
        $search = "%$q%";
        $stmt->bind_param('s', $search);
        $stmt->execute();

        $res = $stmt->get_result();

        if($res->num_rows != 0) {
            while($row = $res->fetch_assoc()) {
                $result = array();
                $result['idkejadian'] = $row['idkejadian'];
                $result['judul'] = $row['judul'];
                $search_results[] = $result;
            }
        }

        $stmt->close();

        // Close connection
        $mysqli->close();
    }
    
    echo json_encode($search_results);
?>
