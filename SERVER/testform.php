<?php
//if server returns GET it's a server problem not the code
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<h2>Received POST request</h2>';
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
}
?>
<form method="POST" action="">
    <input type="text" name="mail" placeholder="Email">
    <input type="password" name="pwd" placeholder="Password">
    <button type="submit">Submit</button>
</form>