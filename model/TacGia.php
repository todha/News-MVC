<?php
class TacGiaModel
{
    public $user_id;

    public $full_name;
    public $website;
    public $profile_json_text;
    public $image_path;

    function __construct()
    {
        $this->user_id = "";
        $this->full_name = "";
        $this->website = "";
        $this->profile_json_text = "";
        $this->image_path = "";
    }
    public static function listAllExceptCurrAuthor($author_id)
    {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");
        $query = "SELECT * FROM authors WHERE user_id != $author_id";
        $result = $mysqli->query($query);
        $authorList = array();
        if ($result) {
            foreach ($result as $row) {
                $author = new TacGiaModel();
                $author->user_id = $row["user_id"];
                $author->full_name = $row["full_name"];
                $authorList[] = $author;
            }
        }
        $mysqli->close();
        return $authorList;
    }
    public static function getAuthorById($author_id) {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");

        $query = "SELECT * FROM AUTHORS WHERE user_id = {$author_id}";
        $result = $mysqli->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public static function getAuthorsByIds($author_ids) {
        $mysqli = connect();
        $author_list = array();

        $id_list = implode(',', array_map('intval', $author_ids));

        $query = "SELECT * FROM AUTHORS WHERE user_id IN ($id_list)";
        $result = $mysqli->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $author = new TacGiaModel();
                $author->user_id = $row["user_id"];
                $author->full_name = $row["full_name"];
                $author_list[] = $author;
            }
        }

        $mysqli->close();
        return $author_list;
    }       

    public static function updateProfile($user_id, $full_name, $website, $profile_json_text, $avatar_path) {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");

        $query = "UPDATE AUTHORS SET full_name = ?, website = ?, profile_json_text = ? WHERE user_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssi", $full_name, $website, $profile_json_text, $user_id);
        $result = $stmt->execute();
        $stmt->close();

        // Cập nhật đường dẫn ảnh đại diện
        if (!empty($avatar_path)) {
            $query_avatar = "UPDATE AUTHORS SET image_path = ? WHERE user_id = ?";
            $stmt_avatar = $mysqli->prepare($query_avatar);
            $stmt_avatar->bind_param("si", $avatar_path, $user_id);
            $result_avatar = $stmt_avatar->execute();
            $stmt_avatar->close();
        }

        $mysqli->close();

        return $result && $result_avatar; // Trả về true nếu cả hai câu lệnh cập nhật đều thành công
    }

}
