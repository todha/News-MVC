<?php

class LoginController
{
    //add this function at any function of controller which require to authorize
    public static function authentication()
    {
        if (!isset($_SESSION["IsLogined"]) || $_SESSION["IsLogined"] != "true") {
            header("Location:index.php?action=error");
        }
    }

    public function logout()
    {
        unset($_SESSION["IsLogined"]);
        header("Location:index.php");
    }

    public function unauthorized_page()
    {
        $data = "";
        // $VIEW = "./view/ThieuQuyenTruyCap.phtml";
        // require("./template/template.phtml");
        require("./view/ThieuQuyenTruyCap.phtml");
    }

    public function login()
    {
        if (count($_POST) >= 0 && isset($_POST["UserName"])) {
            $username = $_POST["UserName"];
            $password = $_POST["Password"];
            // Gọi hàm login từ UserModel
            $user = UserModel::login($username, $password);
            
            if ($user) {
                $_SESSION["IsLogined"] = true;
                $_SESSION["UserID"] = $user['user_id'];
                $_SESSION["UserName"] = $user['username'];
                $_SESSION["UserRole"] = $user['user_type'];
                header("Location:index.php");

                // exit;
            }
		
            else {
                $data = "Thông tin đăng nhập bị sai, nhập lại thông tin !!! ";
                require("./view/DangNhap.phtml");
            }
        } else {
            $data = "";
            require("./view/DangNhap.phtml");
        }
    }

}
