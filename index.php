<?php

use Models\Guest;
use Models\Room;
use Models\Reservation;

include("includes.php");

// Getting database connection instance
$connection  = DB::getInstance()->getConnection();

// Initialising table models
$room_model  = new Room($connection);
$reservation = new Reservation($connection);
$guest_model = new Guest($connection);

// Getting list of all rooms
$all_rooms = $room_model->findAll();

// Checking room availability using first tab form fields
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $all_reservations = $reservation->findReservations($_POST);
} else {
    $all_reservations = $reservation->findAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hotel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
    <style>
        .free {
            background-color: #6afd8c;
        }

        .reserved {
            background-color: #ff8686;
        }

        .card {
            cursor: pointer;
        }

        .free :hover {
            background-color: #2ed545;
        }

        .reserved :hover {
            background-color: #f55656;
        }
    </style>
</head>
<body>
<div class="jumbotron text-center">
    <h1>Hotel reservation</h1>
</div>

<div class="container">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
               aria-selected="true">Reservations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
               aria-selected="false">Rooms now</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">

        <!----------- BEGIN FIRST TAB: LIST OR RESERVATIONS -------------->

        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row mt-5">
                <form method="post" action="/">
                    <input type="hidden" name="form_id" value="search">
                    <b>Room Number:</b>
                    <input name="room_number" type="text" class="mb-5 mr-5" min="1" max="4">
                    <b>Arriving date:</b>
                    <input id="arriving_date" name="arriving_date" type="text" class="mb-5 mr-5" required>
                    <b>Leaving date:</b>
                    <input id="leaving_date" name="leaving_date" type="text" class="mb-5 mr-5" required>
                    <input class="btn btn-primary" type="submit" value="Check">
                </form>

                <?php if (!empty($_POST) && count($all_reservations) > 0) { ?>
                    <h2 class="text-danger">Room is reserved between <?= $_POST['arriving_date'] ?>
                        and <?= $_POST['leaving_date'] ?></h2>
                <?php } elseif (!empty($_POST) && count($all_reservations) === 0) { ?>
                    <h2 class="text-success">Room is free between <?= $_POST['arriving_date'] ?>
                        and <?= $_POST['leaving_date'] ?></h2>
                    <hr>
                    <div class="btn btn-success" data-toggle="modal" data-target="#myModal">Reserve now</div>
                <?php }
                if (!empty($_POST)) { ?>
                    <a href="/" class="btn btn-warning ml-2">Reset</a>
                <?php } ?>

                <table class="table table-bordered table-hover table-sm mt-2">
                    <thead>
                    <tr>
                        <th>№</th>
                        <th>Room number</th>
                        <th>Guest name</th>
                        <th>Arriving date</th>
                        <th>Leaving date</th>
                        <th>Comment/Note</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($all_reservations as $key => $reserve) { ?>
                        <tr>
                            <td><?= ($key + 1) ?></td>
                            <td><?= $reserve->number ?></td>
                            <td><?= $reserve->full_name ?></td>
                            <td><?= date('d.m.Y H:i', $reserve->arriving_date) ?></td>
                            <td><?= date('d.m.Y H:i', $reserve->leaving_date) ?></td>
                            <td><?= $reserve->comments ?></td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>

            </div>
        </div>
        <!----------- END OF FIRST TAB: LIST OR RESERVATIONS -------------->

        <!----------- BEGIN SECOND TAB: CURRENT STATUS OF ROOMS -------------->
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row mt-5">
                <?php foreach ($all_rooms as $room) {
                    $is_free = $room_model->is_free($room->id);
                    if (!$is_free) {
                        $reserve = $reservation->findByRoomId($room->id);
                    }
                    ?>

                    <div class="card w-20 mb-3 mr-3 <?= $is_free ? 'free' : 'reserved' ?>">
                        <div class="card-body">
                            <h1 class="card-title text-center rounded border border-dark w-50 mx-auto"><?= $room->number ?></h1>
                            <h5 class="card-title text-center"><?= $is_free ? 'FREE' : $reserve->full_name ?></h5>
                            <hr>
                            <p class="card-text">
                                From: <?= !$is_free ? date('d.m.Y H:i', $reserve->arriving_date) : '__________________' ?>
                            </p>
                            <p class="card-text">
                                To: <?= !$is_free ? date('d.m.Y H:i', $reserve->leaving_date) : '__________________' ?>
                            </p>
                        </div>
                    </div>

                <?php } ?>

            </div>
        </div>
        <!----------- END OF SECOND TAB: CURRENT STATUS OF ROOMS -------------->
    </div>
</div>


<!-- The Modal -->
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Reserve room № <?= $_POST['room_number'] ?></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form action="/reserve.php" method="post">
                    <input type="hidden" name="form_id" value="reservation">
                    <input type="hidden" name="room_id"
                           value="<?= !empty($_POST) ? $room_model->findByNumber($_POST['room_number'])->id : "" ?>">
                    <b>Full name</b>
                    <input class="mb-2 w-100" type="text" name="guest_name" minlength="4" maxlength="128">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <b>Email</b>
                            <input class="w-100" type="email" name="email">
                        </div>
                        <div class="col-md-6">
                            <b>Phone number</b>
                            <input class="w-100" type="number" name="phone_number">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <b>Arriving date</b>
                            <input class="w-100" id="arriving_date" type="text" name="arriving_date"
                                   value="<?= $_POST['arriving_date'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <b>Leaving date</b>
                            <input class="w-100" id="leaving_date" type="text" name="leaving_date"
                                   value="<?= $_POST['leaving_date'] ?>" required>
                        </div>
                    </div>
                    <textarea name="comment" class="w-100" rows="4" placeholder="Any comments or notes"></textarea>

                    <input type="checkbox" id="send_email" name="send_email" value="1">
                    <label for="send_email"> Send email notification</label><br>
                    <input type="checkbox" id="send_sms" name="send_sms" value="1">
                    <label for="send_sms"> Send sms notification</label><br>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Reserve</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
</body>
<script>
    // Masking datetime input fileds
    $("#arriving_date, #leaving_date").inputmask('datetime', {
        mask: '99.99.9999 99:99',
        placeholder: 'dd-mm-yyyy hh:mm',
    });
</script>
</html>
