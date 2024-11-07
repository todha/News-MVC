<?php
class ChuDeModel
{
    public $topic_id;
    public $topic_name;
    function __construct()
    {
        $this->topic_id = "";
        $this->topic_name = "";
    }

    public static function listAll()
    {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");
        $query = "SELECT * FROM topics";
        $result = $mysqli->query($query);
        $topicList = array();
        if ($result) {
            foreach ($result as $row) {
                $topic = new ChuDeModel();
                $topic->topic_id = $row["topic_id"];
                $topic->topic_name = $row["topic_name"];
                $topicList[] = $topic;
            }
        }
        $mysqli->close();
        return $topicList;
    }

}
