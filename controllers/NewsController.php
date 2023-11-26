<?php

require_once './models/News.php';
 
class NewsController
{
    public function getNews($param, $data)
    {
        $queryParams = array();
        $allowedKeys = ['news_id'];
        $select = ['news_id' ,'created_at', 'updated_at', 'image_url' , 'title', 'content'];

        if (isset($_SERVER['QUERY_STRING'])) {
           
            $queryString = $_SERVER['QUERY_STRING'];
            parse_str($queryString, $queryParams);

            if(count(array_intersect_key($queryParams, array_flip($allowedKeys)))==0) { // if the parameter does not match the allowed key (case get single new)
               
                http_response_code(404);
                echo json_encode(["message" => "News Params not Suitable"]);
                return;
            }
        } 
        
        try {
            $News = new News();
            $result = $News->get($queryParams, $allowedKeys, $select);
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
           
            if($queryParams == []) { 

                http_response_code(200);
                echo json_encode(["message" => "News List fetched Successfully", "data" => $rows]);
            } else {

                if($result->rowCount() == 0) {

                    http_response_code(404);
                    echo json_encode(["message" => "No News found"]);
                } else {

                    http_response_code(200);
                    echo json_encode(["message" => "News fetched Successfully", "data" => $rows]);
                }
            }
        } catch (PDOException $e) {
            echo "Unknown error in NewsController::getNewss: " . $e->getMessage();
            die();
        }
    }


    
    public function addNews($param, $data)
    {
        if (!isset($data['user_id'])
            || !isset($data['content'])
            || !isset($data['title'])
            || !isset($data['image_url'])) 
        {
            http_response_code(400);
            echo json_encode(["message" => "Missing user_id, content, title, image_url"]);
            return;
        }

        $allowedKeys = ['image_url' , 'title', 'content'];

        try {
            $News = new News();
            $result = $News->create(
                $data,
                $allowedKeys
            );

            http_response_code(200);
            echo json_encode(["message" => "News created successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in NewsController:: addNews: " . $e->getMessage();
            die();
        }
    }
 
    public function updateNews($param, $data)
    {
        
    }

    
    public function deleteNews($param, $data)
    {
        try {
            $News = new News();

            // Check if News exist
            $result = $News->get(['news_id' => $param['news_id']], ['news_id'], ['news_id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "News does not exist"]);
                die();
            }
            // Delete News
            $News->delete($param['news_id']);
            //Not yet handle delete comment
     
            http_response_code(200);
            echo json_encode(["message" => "News deleted successfully"]);
        } catch (PDOException $e) {
     
            echo "Unknown error in NewsController::deleteNews : " . $e->getMessage();
            die();
        }
    }
}