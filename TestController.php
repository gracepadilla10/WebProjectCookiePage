<?php

if (!isset($_SERVER['HTTPS'])) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] .
           $_SERVER['REQUEST_URI'];  // start with /...
    header("Location: " . $url);  // Redirect - 302
    exit;                         // should be before any output
}                               // 


if (empty($_POST['page'])) {  // When no page is sent from the client; The initial display
                                // You may use if (!isset($_POST['page'])) instead of empty(...).
    $display_type = 'no-signin';  // This variable will be used in 'view_startpage.php'.
                              // It will display the start page without any box, i.e., no SignIn box, no Join box, ...
    include ('view_cookie_startpage.php');
    exit();
}

session_start();

require('cookie_model.php');  // This file includes some routines to use DB.

// When commands come from StartPage
if ($_POST['page'] == 'StartPage')
{
    $command = $_POST['command'];
    switch($command) {  // When a command is sent from the client
        case 'SignIn':  // With username and password
//          if (there is an error in username and password) {
            if (!is_valid($_POST['username'], $_POST['password'])) {
                $error_msg_username = '* Wrong username, or';
                $error_msg_password = '* Wrong password'; // Set an error message into a variable.
                                                        // This variable will used in the form in 'view_startpage.php'.
                $display_type = 'signin';  // It will display the start page with the SignIn box.
                                           // This variable will be used in 'view_startpage.php'.
                include('view_cookie_startpage.php');
            }
            else {
                $_SESSION['signedin'] = 'YES';
                $_SESSION['username'] = $_POST['username'];
                $username = $_POST['username'];
                include ('view_cookie_mainpage.php');
            }
            exit();

        case 'Join':  // With username, password, email, some other information
            if (does_exist($_POST['username'])) {
                $error_msg_username = '* The user exists.';
                $error_msg_password = '';
                $display_type = 'join';
                include('view_cookie_startpage.php');
            } else {
                if (insert_new_user($_POST['username'], $_POST['password'], $_POST['email'])) {
                    $error_msg_username = '';
                    $error_msg_password = '';
                    $display_type = 'signin';
                    include('view_cookie_startpage.php');
                } else {
                    $error_msg_username = '* Insertion error';
                    $error_msg_password = '* Password doesnt match pattern';
                    $display_type = 'join';
                    include('view_cookie_startpage.php');
                }
            }
            exit();
        //...
    }
}

// When commands come from 'MainPage'
else if ($_POST['page'] == 'MainPage')
{
    // support commands

    $command = $_POST['command'];

    switch($command) {
        case 'SignOut':
            // destroy session variables and the session
            session_unset();
            session_destroy();
            // go to 'StartPage'
            $display_type = 'no-signin';
            include('view_cookie_startpage.php');
            break;

        // posting a question
        case 'BuyCookie':
            post_question($_POST['question'], $_SESSION['username']);  // in model.php
            break;

        // list questions
       case 'SellCookie':
            sell_cookie($_POST['question'], $_POST[], $_POST[], $_SESSION['username']);
            break;
            
        case 'SearchByName':
            search_by_name($_POST['searchTerm']);
            break;
            
        case 'SearchBySeller':
            search_by_seller($_POST['searchTerm']);
            break;
            
        case 'SearchByID':
            search_by_ID($_POST['searchTerm']);
            break;

        case 'Unsubscribe':
            unsubscribe($_SESSION['username']);
            break;

        default:
            echo 'Unknown command - ' . $command . '<br>';
    }
}

else {
    //...
}
?>