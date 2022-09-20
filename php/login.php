<?php
    session_start();

    //Connection information
    $DATABASE_HOST = 'localhost:3306';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'phplogin';

    //Try and connect using the info above
    $connection = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
    if(mysqli_connect_errno()){
        
        //If the is an error with connection, stop the script and display the error.
        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
    }

    //Now we check if the data from login form was submitted, isset() will check if the data exists.
    if( !isset($_POST['username'], $_POST['password']) ){
        //Colud not het the data that should have been sent.
        exit('Please fill both the username and password fields!');
    }

    //Prepare out SQL, preparing the SQL statement will prevent SQL injection
    if($stmt = $connection->('SELECT id, password FROM accounts WHERE username = ?')){
        //Bind parameters (s = string, i =int, b = blob), in our case the username is a string and use s
        $stmt->bind_param('s' , $_POST['username']);
        $stmt->execute();

        //Store the result so we can check  if the account exists in the database
        $stmt->store_result();
        if($stmt->num_rows > 0){
            $stmt->bind_result($id, $password);
            $stmt->fetch();
            //Account exists, now we verify the password
            if($_POST['password'] === $password)){
                //Verification success! User has logged-in!
                //Crreate sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['username'];
                $_SESSION['id'] = $id;
                echo 'Welcome' . $_SESSION['name'] . '!';
            }
            else{
                //Incorrect password
                echo 'Incorrect username and/or pasasword!';
            }
        }
        else{
            //Incorrect username
            echo 'Incorrect username and/or password!';
        }

        $stmt->close();
    }
?>