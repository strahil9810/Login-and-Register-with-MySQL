<?php
    $DATABASE_HOST = 'localhost:3306';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASAE_NAME = 'phplogin';

    //Try and connect using the info above.
    $connection = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASAE_NAME);
    if(mysqli_connect_errno()){
        //If there is an error with the connection, stop the script and display the error.
        exit('Failed to connect to MySQL:' . mysqli_connect_error());
    }

    //Now we check if the data was submitted, isset() function will check if the data exists.
    if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
        //Could not get the data that should have been sent.
        exit('Please complete the registration form!');
    }

    //Make sure the submitted registration values are not empty.
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
        //One or more values are empty.
        exit('Please complete the registration form.');
    }

    //We need to check if the account with that username exists.
    if ($stmt = $connection->prepare('SELECT id, password, FROM accounts WHERE username = ?')) {
        //Bind parameters (s = string, i = int, b = blob), hash the password using the PHP password.
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $smt->store_result();

        //Store the results so we can check if the account exitst in the database.
        if ($stmt->num_rows > 0) {
            //Username already exists
            echo 'Username exists, please choose another!';
        }
        else {
            //Username doesnt exists, insert new account
            if ($stmt = $connection->prepare('INSERT INTO accounts (username, password, email) VALUES (?, ?, ?)')) {
                //We do not want to expose password in our database, so hash the password and use password_verify when a user logs in.
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bind_param('sss', $_POST['username'], $password, $_POST['email']);
                $stmt->execute();
                $stmt->execute();
                echo 'You have sucessfully registered, you can now login!';
            }
            else {
                echo 'Could noe prepare statement!';
            }
        }
        $stmt->close();
    }
    else{
        //Something is wrong the sql statement, check to make sure accounts table exists with all 3 field.
        echo 'Could not prepare statement!';
    }
    $connection->close();
?>