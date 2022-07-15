<?php

use Models\Guest;
use Models\Reservation;

include("includes.php");
$connection  = DB::getInstance()->getConnection();
$reservation = new Reservation($connection);
$guest_model = new Guest($connection);

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['form_id'] == "reservation") {

    // Check is guest already exists in database
    // Creates new and returns his ID if not exists
    $_POST['guest_id'] = $guest_model->findOrCreate($_POST);

    // Now creating new reservation
    $reservation->create($_POST);

    // Redirecting user to main/previous page
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
}

?>