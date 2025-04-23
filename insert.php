<?php
    include("connection.php");

    if (isset($_POST["submit"])) {
        // File Upload Setup
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an actual image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".<br>";
            $uploadOk = 1;
        } else {
            echo "File is not an image.<br>";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.<br>";
            $uploadOk = 0;
        }

        // Check file size (limit: ~500KB)
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.<br>";
            $uploadOk = 0;
        }

        // Allow specific file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" &&
            $imageFileType != "jpeg" && $imageFileType != "gif"
        ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
            $uploadOk = 0;
        }

        // If everything is okay, try uploading
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.<br>";
                $photo_name = basename($_FILES["fileToUpload"]["name"]);

                // Get Form Data
                $f_name = trim($_POST["fname"]);
                $s_email = trim($_POST["semail"]);
                $s_contact = trim($_POST["contact"]);
                $s_password = trim($_POST["spassword"]);
                $gender = trim($_POST["gender"]);
                $branch = trim($_POST["branch"]);

                // Encrypt password
                $enc_password = password_hash($s_password, PASSWORD_DEFAULT);

                // Prepare and bind SQL statement
                $myQuery = $conn->prepare("INSERT INTO student(fullname, semail, contact, password, gender, branch, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $myQuery->bind_param("sssssss", $f_name, $s_email, $s_contact, $enc_password, $gender, $branch, $photo_name);

                if ($myQuery->execute()) {
                    echo "
                        <script type='text/javascript'>
                            alert('Data Inserted Successfully');
                            window.location.href='demoSelect.php';
                        </script>
                    ";
                } else {
                    echo "
                        <script type='text/javascript'>
                            alert('Database Error: Could not insert data');
                            window.location.href='registration.html';
                        </script>
                    ";
                }
            } else {
                echo "Sorry, there was an error uploading your file.<br>";
            }
        } else {
            echo "
                <script type='text/javascript'>
                    alert('File validation failed. Upload canceled.');
                    window.location.href='registration.html';
                </script>
            ";
        }
    } else {
        echo "
            <script type='text/javascript'>
                alert('Please fill the form properly.');
                window.location.href='registration.html';
            </script>
        ";
    }
?>
