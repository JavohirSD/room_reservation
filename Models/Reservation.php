<?php

namespace Models;

use Exception;
use Models\Helpers\Email;
use PDO;
use Models\Interfaces\ModelFinder;

class Reservation implements ModelFinder
{
    use Email;

    const TABLE_NAME = 'reservation';
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findByRoomId(int $room_id)
    {
        $statement = $this->connection->prepare("SELECT * FROM " . self::TABLE_NAME . " INNER JOIN " . Guest::TABLE_NAME . " as g ON guest_id = g.id WHERE room_id=:id AND leaving_date > UNIX_TIMESTAMP() LIMIT 1;");
        $statement->bindValue(':id', $room_id, PDO::PARAM_INT);
        $statement->execute();
        $reserve = $statement->fetch(PDO::FETCH_OBJ);
        return $reserve === false ? null : $reserve;
    }

    public function findOne(int $id)
    {
        $statement = $this->connection->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id=:id");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $room = $statement->fetch(PDO::FETCH_OBJ);
        return $room === false ? null : $room;
    }

    public function findAll(): ?array
    {
        $query = "SELECT rv.*, number, full_name FROM " . self::TABLE_NAME . " as rv
                    INNER JOIN room ON room.id = rv.room_id
                    INNER JOIN guest ON guest.id = rv.guest_id AND leaving_date > UNIX_TIMESTAMP() ORDER BY id DESC";

        $statement = $this->connection->prepare($query);
        $statement->execute();
        $comment = $statement->fetchAll(PDO::FETCH_OBJ);
        return $comment === false ? null : $comment;
    }

    public function findReservations(array $params): ?array
    {
        $query = "SELECT rv.*, number, full_name FROM " . self::TABLE_NAME . " as rv
        INNER JOIN room ON room.id = rv.room_id
        INNER JOIN guest ON guest.id = rv.guest_id
        WHERE number = :number AND arriving_date <= :date_to AND leaving_date >= :date_from";

        $statement = $this->connection->prepare($query);

        $date_from = strtotime($params['arriving_date']);
        $date_to   = strtotime($params['leaving_date']);
        $number    = $params['room_number'];

        $statement->bindParam(':number', $number, PDO::PARAM_INT);
        $statement->bindParam(':date_from', $date_from, PDO::PARAM_INT);
        $statement->bindParam(':date_to', $date_to, PDO::PARAM_INT);
        $statement->execute();
        $comment = $statement->fetchAll(PDO::FETCH_OBJ);
        return $comment === false ? null : $comment;
    }

    public function create($params)
    {
        try {
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->beginTransaction();

            $query     = "INSERT INTO " . self::TABLE_NAME . " (`room_id`,`guest_id`,`arriving_date`,`leaving_date`,`created_at`,`comments`) VALUES (:room_id,:guest_id,:date1,:date2,UNIX_TIMESTAMP(),:comment)";
            $statement = $this->connection->prepare($query);

            $date_from = strtotime($params['arriving_date']);
            $date_to   = strtotime($params['leaving_date']);

            $statement->bindParam(':room_id', $params['room_id']);
            $statement->bindParam(':guest_id', $params['guest_id']);
            $statement->bindParam(':date1', $date_from);
            $statement->bindParam(':date2', $date_to);
            $statement->bindParam(':comment', $params['comment']);

            if ($status = $statement->execute()) {
                $model_room  = new Room($this->connection);
                $model_guest = new Guest($this->connection);
                $guest       = $model_guest->findOne($params['guest_id']);
                $room        = $model_room->findOne($params['room_id']);

                $text = "Hello " . $guest->full_name .
                    ". Room №" . $room->number .
                    " reserved for you from " . $params['arriving_date'] .
                    " to " . $params['leaving_date'];

                if (isset($params['send_email'])) {
                    $this->sendEmail($guest->email, 'Hotel reservation', $text);
                }

                if (isset($params['send_sms'])) {
                    // Sending sms logic will be here
                    // Paid API key required !
                }
            }
            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollBack();
            echo "Failed to reserve: " . $e->getMessage();
            exit;
        }
        return $status;
    }
}