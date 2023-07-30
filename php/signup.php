<?php
session_start();
include_once "config.php";
$firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
$lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($password)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = mysqli_query($conn, "SELECT email FROM users WHERE email = '{$email}'");
        if (mysqli_num_rows($sql) > 0) {
            echo "$email - This email already exists";
        } else {
            if (isset($_FILES['image'])) {
                $image_name = $_FILES['image']['name'];
                $image_type = $_FILES['image']['type'];
                $temp_name = $_FILES['image']['tmp_name'];

                $image_explode = explode('.', $image_name);
                $image_extension = end($image_explode);
                $extensions = ['png', 'jpg', 'jpeg'];
                if (in_array($image_extension, $extensions) === true) {
                    $types = ["image/jpeg", "image/jpg", "image/png"];
                    if (in_array($image_type, $types) === true) {
                        $time = time();
                        $new_image_name = $time . $image_name;
                        if (move_uploaded_file($temp_name, "images/" . $new_image_name)) {
                            $ran_id = rand(time(), 100000000);
                            $status = "Active now";
                            $encrypt_pass = md5($password);
                            $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, firstName, lastName, email, password, image, status)
                            VALUES ({$ran_id}, '{$firstName}','{$lastName}', '{$email}', '{$encrypt_pass}', '{$new_image_name}', '{$status}')");
                            if ($insert_query) {
                                $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                if (mysqli_num_rows($select_sql2) > 0) {
                                    $result = mysqli_fetch_assoc($select_sql2);
                                    $_SESSION['unique_id'] = $result['unique_id'];
                                    echo "success";
                                } else {
                                    echo "This email address not Exist!";
                                }
                            } else {
                                echo "Something went wrong. Please try again!";
                            }
                        }
                    } else {
                        echo "Please upload an image file - jpeg, png, jpg";
                    }
                } else {
                    echo "Please upload an image file - jpeg, png, jpg";
                }
            }
        }
    } else {
        echo "Email is not valid!";
    }
} else {
    echo "All input fields are required!";
}
