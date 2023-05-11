<?php

include 'components/dbconfig.php';

session_start();

if (isset($_SESSION['captain_id'])) {
    $captain_id = $_SESSION['captain_id'];
} else {
    $captain_id = '';
};

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['password']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $select_user = $conn->prepare("SELECT * FROM `captains` WHERE username = ? AND password = ?");
    $select_user->execute([$username, $pass]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($select_user->rowCount() > 0) {
        $_SESSION['captain_id'] = $row['id'];
        header('Location: index.php');
    } else {
        $errors[] = 'incorrect username or password!';
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <style>
        body {
            background-color: #F2F2F2;
            font-family: Arial, sans-serif;
        }

        .container {
            margin: auto;
            max-width: 500px;
            padding: 20px;
            background-color: #FFFFFF;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            border-radius: 5px;
        }

        h1 {
            text-align: center;
            margin-top: 0;
        }

        input[type=text],
        input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }

        button[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button[type=submit]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <?php
        if (!empty($errors)) {
            echo '<div class="error">';
            foreach ($errors as $error) {
                echo '<p>' . $error . '</p>';
            }
            echo '</div>';
        }
        ?>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="captain_register.php">Register now</a></p>
    </div>
</body>

</html>