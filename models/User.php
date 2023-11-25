<?php
require_once './config/Database.php';

$connection = Database::getInstance()->getConnection();

class User
{
    public function get($queryParams, $allowedKeys = [], $select = [])
    {
        global $connection;
        if (!empty($allowedKeys)) {
            $queryParams = array_intersect_key($queryParams, array_flip($allowedKeys));
        }
        //array_flip : đổi vị trí của key và value
        // array_intersect_key: lấy các element trong $queryParams sao cho các key trong $queryParams tồn tại trong array_flip($allowedKeys)
        //queryParams => dùng cho where clause kiểu để phân biệt, allowedKeys chỉ cần set key giống query param, $select lấy các field nào ra 
        $selectClause = empty($select) ? '*' : implode(', ', $select); //implode is used to create string from array
        $conditions = [];                   //declare array
        foreach ($queryParams as $key => $value) {
            $conditions[] = "$key='$value'";    //push "..." to $conditions
        }
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $query = "SELECT $selectClause FROM USER $whereClause";
        // echo $query;
        try {
            $result = $connection->prepare($query);

            $result->execute();     
            //$result is an object(PDOStatement), if you want to access the value after get
            // you need to use its method : fetch,fetchAll
            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in USER::get: " . $e->getMessage();
        }
    }

    public function create($data, $allowedKeys = [])
    {
        global $connection;

        if (!empty($allowedKeys)) {
            $data = array_intersect_key($data, array_flip($allowedKeys));
        }

        $keys = array_keys($data);
        $values = array_values($data);
        $query = "INSERT INTO USER (" . implode(", ", $keys) . ") VALUES ('" . implode("', '", $values) . "')";

        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in USER::create: " . $e->getMessage();
        }
    }

    public function update($id, $data, $allowedKeys = [])
    {
        global $connection;

        if (!empty($allowedKeys)) {
            $data = array_intersect_key($data, array_flip($allowedKeys));
        }

        foreach ($data as $key => $value) {
            $updates[] = "$key='$value'";
        }

        $query = "UPDATE USER SET " . implode(", ", $updates) . " WHERE id='$id'";

        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in USER::update: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        global $connection;

        $query = "DELETE FROM USER WHERE id='$id'";

        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in USER::delete: " . $e->getMessage();
        }
    }

    public function getGeneralList()
    {
        global $connection;

        $query = "SELECT user.id, email, name, role FROM USER INNER JOIN USER_INFO ON user.id = user_info.id";

        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in USER::get: " . $e->getMessage();
        }
    }
}