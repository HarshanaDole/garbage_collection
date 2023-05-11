<?php

include 'components/dbconfig.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if (isset($_POST['register'])) {

    $username = $_POST['username'];
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $password = sha1($_POST['password']);
    $password = filter_var($password, FILTER_SANITIZE_STRING);
    $confirm_password = sha1($_POST['confirm_password']);
    $confirm_password = filter_var($confirm_password, FILTER_SANITIZE_STRING);

    $select_user = $conn->prepare("SELECT * FROM `captains` WHERE email = ?");
    $select_user->execute([$email]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    $errors = array();

    if ($select_user->rowCount() > 0) {
        $errors[] = 'email already exists!';
    } else {
        if ($password != $confirm_password) {
            $errors[] = 'confirm password not matched!';
        } else {
            $insert_user = $conn->prepare("INSERT INTO `captains`(username, email, password) VALUES(?,?,?)");
            $insert_user->execute([$username, $email, $confirm_password]);

            // Registration successful, redirect to login page
            header('Location: captain_login.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Add some basic styles to make the form look nicer */
        body {
            font-family: sans-serif;
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
            margin-top: 0;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        label {
            display: block;
            margin-bottom: 0;
            margin-top: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 95%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 95%;
        }

        input[type="submit"]:hover {
            background-color: #3e8e41;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <form action="" method="post">
        <h1>Register</h1>
        <?php
        if (!empty($errors)) {
            echo '<div class="error">';
            foreach ($errors as $error) {
                echo '<p>' . $error . '</p>';
            }
            echo '</div>';
        }
        ?>
        <label for="name">Userame:</label>
        <input type="text" name="username" id="username" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <input type="submit" name="register" value="Register">
        <p>Already have an account? <a href="captain_login.php">Login now</a></p>
    </form>

</body>

</html>