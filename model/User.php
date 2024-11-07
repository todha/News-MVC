<?php

class UserModel
{
    public $UserID;
    public $UserName;
    public $Role;
    public $Password;

    function __construct()
    {
        $this->UserID = "";
        $this->UserName = "";
        $this->Role = "";
        $this->Password = "";
    }

    // Hàm đăng nhập và lấy thông tin người dùng
    public static function login($username, $password)
    {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");

        $query = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $mysqli->prepare($query);

        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        // Lấy kết quả
        $user = $result->fetch_assoc();

        if ($user) {
            unset($user['Password']);
            $stmt->close();
            $mysqli->close();
            // Trả về thông tin người dùng (không bao gồm mật khẩu)
            return $user;
        } else {
            // Người dùng không tồn tại
            $stmt->close();
            $mysqli->close();
            return null;
        }
    }
    public static function usernameExists($username) {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");

        $query = "SELECT COUNT(*) as count FROM users WHERE UserName = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $row = $result->fetch_assoc();
        $count = $row['count'];

        $stmt->close();
        $mysqli->close();

        return $count > 0;
    }
}
