<?php header("Access-Control-Allow-Origin: *"); ?>
<?php
    // Get input
    $username = addslashes(htmlentities($_POST['username']));
    $title = addslashes(htmlentities($_POST['title'])); 
    $description = addslashes(htmlentities($_POST['description']));
    $institute = addslashes(htmlentities($_POST['institute']));
    $datetime = join(" ", explode("T", addslashes(htmlentities($_POST['datetime']))));
    $datetime = substr($datetime, 0, -6);
    $latitude = addslashes(htmlentities($_POST['latitude']));
    $longitude = addslashes(htmlentities($_POST['longitude']));
    $files = $_FILES["photo"];

    $arr = array();
    $is_error = false; // Flag variable to monitor wheters errors are exists

    // Check for empty values
    if(empty($files['name'][0]) || empty($title) || empty($description) || empty($institute) || empty($datetime)) {
        // Show error message
        $is_error = true;
        $arr['status'] = "ERROR";
        $arr['message'] = "Tolong Isi Semua Data Yang Diminta";
    }
    else {
        include "./connection.php";

        // Create connection object
        $mysqli = new mysqli($server, $id, $pw, $db);        

        // Check for connection error
        if ($mysqli->connect_errno) {
            $is_error = true;
            $arr['status'] = "ERROR";
            $arr['message'] = "Gagal terhubung ke database MySQL: " . $mysqli->connect_error;
        }

        // Create data to Tambahkan data ke tabel posting terlebih dahulu
        $stmt = $mysqli->prepare("INSERT INTO kejadian
            (username, judul, deskripsi, instansi_tujuan, tanggal, longitude, latitude) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssdd', $username, $title , $description, $institute, $datetime, $longitude, $latitude);
        $stmt->execute();
        
        // Get last report id from the abce INSERT command
        $idreport = $stmt->insert_id;

        $stmt->close();

        // Loop the images provided by the user
        foreach($files['name'] as $key => $name) {
            // If no error is found
            if($files['error'][$key] == 0) {
                $file_info = getimagesize($files['tmp_name'][$key]);
                if(!empty($file_info)) {
                    // Only image less than 200kB is allowed
                    if($files['size'][$key] < 200000) {
						$ext = substr($name, strrpos($name, '.') + 1);

                        // Proceeed with INSERT command to gambar_kejadian table
                        $stmt2 = $mysqli->prepare("INSERT INTO gambar_kejadian (idkejadian, extension)
                                                    VALUES (?,?)");
                        $stmt2->bind_param('is', $idreport , $ext);
                        $stmt2->execute();

                        // Get last image id from the abce INSERT command
                        $idimage = $stmt2->insert_id;

                        $filename = $idimage . '.' . $ext;
                        $destination = "/var/www/html/ubaya/s160717023/matawarga/gambar_kejadian/" . $filename;

                        // Move the image to "gambar_kejadian" folder
                        if(!move_uploaded_file($files['tmp_name'][$key], $destination)) {
                            $is_error = true;
                            $arr['status'] = "ERROR";
                            $arr['message'] = "Aduh! foto $name gagal diupload :(";
                        }
                        $stmt2->close();
					}
                    else {
                        $is_error = true;
                        $arr['status'] = "ERROR";
                        $arr['message'] = "Mohon upload gambar dengan size di bawah 200kB";
                    }
                }
                else {
                    $is_error = true;
                    $arr['status'] = "ERROR";
                    $arr['message'] = "Foto yang Anda upload nampaknya bukan sebuah gambar";
                }
            }
            else {
                $is_error = true;
                $arr['status'] = "ERROR";
                $arr['message'] = "Oops! Terdapat error pada saat upload. Silahkan coba lagi";
            }
        }

        if(!$is_error) {
            $arr['status'] = "SUCCESS";
        }        

        /* close connection */
        $mysqli->close();
    }

    echo json_encode($arr);
?>