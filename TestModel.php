<?php
// connect to MySQL DB
$conn = mysqli_connect('localhost', 'gpadilla', '3250Turg', 'C354_gpadilla');

function insert_new_user($username, $password, $email)
{
    global $conn;
    $password_pattern = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.* )(?=.*[^a-zA-Z0-9]).{8,16}$/';
    $hashed_password = sha1($password);
    
    if (does_exist($username))
        return false;
    
    if (!preg_match($password_pattern, $password))
        return false;
    
    else {
        $current_date = date('Ymd');
        $sql = "insert into Users values (NULL, '$username', '$hashed_password', '$email')";
        $result = mysqli_query($conn, $sql);
        return $result;
    }
}

function is_valid($username, $password)
{
    global $conn;
    $hashed_password = sha1($password);

    $sql = "select * from Users where (Username = '$username' and Password = '$hashed_password')";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0)
        return true;
    else
        return false;
}

function does_exist($username)
{
    global $conn;

    $sql = "select * from Users where (Username = '$username')";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0)
        return true;
    else
        return false;
}

/*
*   Queries
*/

function sell_cookie($cookie_name, $cookie_quantity, $cookie_price, $u)  // cookie, username
{
    global $conn;

    $sql = "select * from Users where (Username = '$u')";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $uid = $row['UserId'];
    }
    else {
        echo "Selling cookie failed!";
    }
    
    $sql = "INSERT into Cookie_Marketplace values (NULL, '$u', '$cookie_name', $cookie_quantity, $cookie_price)";
    
    $result = mysqli_query($conn, $sql);
    echo "Cookie added in Marketplace succesfully";
    return;
}

// Function to search by the name of the cookie 
function search_by_name($cookie_name) 
{
    $data = array();
    global $conn; //send the JSON string of cookie records back to the client
    
    $sql = "SELECT * FROM Cookie_Marketplace WHERE Cookie_Name LIKE '%$cookie_name%'";
    $result = mysqli_query($conn, $sql);
    $i = 0;
    
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $data[$i++] = $row;
        }
        echo json_encode($data);
    }
    return;
}

// Function to search by the seller of the cookie 
function search_by_seller($cookie_seller) 
{
    $data = array();
    global $conn; //send the JSON string of cookie records back to the client
    
    $sql = "SELECT * FROM Cookie_Marketplace WHERE Owner LIKE '%$cookie_seller%'";
    $result = mysqli_query($conn, $sql);
    $i = 0;
    
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $data[$i++] = $row;
        }
        echo json_encode($data);
    }
    return;
}

// Function to search by the cookie ID of the cookie 
function search_by_ID($cookie_ID) 
{
    $data = array();
    global $conn; //send the JSON string of cookie records back to the client
    
    $sql = "SELECT * FROM Cookie_Marketplace WHERE Cookie_ID LIKE '%$cookie_ID%'";
    $result = mysqli_query($conn, $sql);
    $i = 0;
    
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $data[$i++] = $row;
        }
        echo json_encode($data);
    }
    return;
}

// Function to display the Marketplace table and the items within
function display_marketplace() {
    global $conn;

    $sql = "SELECT * FROM Cookie_Marketplace limit 25";
    $result = mysqli_query($conn, $sql);
    $i = 0;

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $data[$i++] = $row;
        }
        echo json_encode($data);
    }
    return;
}

// Function to update the quantity of an specific cookie
function update_cookie_quantity($cookie_ID, $cookie_quantity) {
    global $conn;

    $sql = "UPDATE Cookie_Marketplace set Quantity = $cookie_quantity WHERE (Cookie_ID = $cookie_ID)";
    $result = mysqli_query($conn, $sql);
    echo "Cookie quantity updated succesfully!";

    $sqli = "SELECT * FROM Cookie_Marketplace WHERE Cookie_ID LIKE '%$cookie_ID%'";
    $results = mysqli_query($conn, $sqli);
    $i = 0;

    if (mysqli_num_rows($results) > 0) {
        while($row = mysqli_fetch_assoc($results)) {
            $data[$i++] = $row;
        }
        echo json_encode($data);
    }
    return;
}

// Function to update the price of an specific coookie
function update_cookie_price($cookie_ID, $cookie_price) {
    global $conn;

    $sql = "UPDATE Cookie_Marketplace set Price = $cookie_price WHERE (Cookie_ID = $cookie_ID)";
    $result = mysqli_query($conn, $sql);
    echo "Cookie price updated succesfully!";

    $sqli = "SELECT * FROM Cookie_Marketplace WHERE Cookie_ID LIKE '%$cookie_ID%'";
    $results = mysqli_query($conn, $sqli);
    $i = 0;

    if (mysqli_num_rows($results) > 0) {
        while($row = mysqli_fetch_assoc($results)) {
            $data[$i++] = $row;
        }
        echo json_encode($data);
    }
    return;
}

// Function to delete a cookie from the Marketplace table
function delete_cookie($cookie_ID) {
    global $conn;

    $sql = "DELETE from Cookie_Marketplace where (Cookie_ID = $cookie_ID)";
    $result = mysqli_query($conn, $sql);
    echo "Cookie deleted succesfully!";

    return;
}

function unsubscribe($username) {
    global $conn;

    if (!does_exist($username))
        return false;
    else {
        $sql = "DELETE from Users WHERE (Username = '$username')";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

}
?>
