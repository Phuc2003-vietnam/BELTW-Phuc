<?php
require_once './config/Database.php';

$connection = Database::getInstance()->getConnection();

class Product
{
    public function get($queryParams, $allowedKeys = [], $select = [])
    {
        global $connection;

        if (!empty($allowedKeys)) {
            $queryParams = array_intersect_key($queryParams, array_flip($allowedKeys));
        }

        $selectClause = empty($select) ? '*' : implode(', ', $select);

        $conditions = [];
        foreach ($queryParams as $key => $value) {
            switch ($key):
                case 'name':
                    $conditions[] = "P.name LIKE '%$value%'";
                    break;
                case 'max_price':
                    $conditions[] = "P.price <= $value";
                    break;
                case 'min_price':
                    $conditions[] = "P.price >= $value";
                    break;
                case 'category_id':
                    $conditions[] = "PC.category_id='$value'";
                default:
                    $conditions[] = "$key='$value'";
            endswitch;
        }
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $query = "SELECT $selectClause 
                FROM PRODUCT AS P LEFT JOIN PRODUCT_CATEGORY AS PC ON P.id=PC.product_id
                $whereClause";

        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in PRODUCT::get: " . $e->getMessage();
        }
    }

    public function create($data, $allowedKeys = [])
    {
        global $connection;
        if (!empty($allowedKeys)) {
            $data = array_intersect_key($data, array_flip($allowedKeys));
        }
        $thumbnail = $data['thumbnail'];
        $size = $data['size'];

        unset($data['thumbnail']);
        unset($data['size']);
        var_dump($data);
        $keys = array_keys($data);
        $values = array_values($data);
        $query = "INSERT INTO PRODUCTS (" . implode(", ", $keys) . ") VALUES ('" . implode("', '", $values) . "')";
        try {
            $result = $connection->prepare($query);
            $result->execute();
            $lastInsertedId = $connection->lastInsertId();
            echo "WEW";
            //Store the thumbnail
            foreach ($thumbnail as $value) {
            $query_thumbnail= "INSERT INTO THUMBNAILS (product_id,thumbnail) VALUES (" . $lastInsertedId . ",'" . $value . "')" ;       // dont forget that thumnail is string so must add ' '

             $a = $connection->prepare($query_thumbnail);
             $a->execute();
            }
            
            //Store the size
            // foreach ($size as $value) {
            //     $query_size= "INSERT INTO SIZES (product_id,size) VALUES (" . $lastInsertedId . "," . $value . ")" ;
            //      $b = $connection->prepare($query_size);
            //      $b->execute();
            //     }
            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in PRODUCTS::create: " . $e->getMessage();
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

        $query = "UPDATE PRODUCT SET " . implode(", ", $updates) . " WHERE id='$id'";

        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in PRODUCT::update: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        global $connection;

        $query = "DELETE FROM PRODUCT WHERE id='$id'";

        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in PRODUCT::delete: " . $e->getMessage();
        }
    }
}