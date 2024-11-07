<?php

class AuthorController
{

    public function showInfo()
    {
        LoginController::authentication();
        $author_id = $_SESSION["UserID"];
        $author = TacGiaModel::getAuthorById($author_id);
        $papers = BaiBaoModel::listPapersByAuthor($author_id);
        $VIEW = "./view/ThongTinTacGia.phtml";
        require("./template/template.phtml");
    }

    public function showPaper()
    {
        LoginController::authentication();
        $author_id = $_SESSION["UserID"];
        $papers = BaiBaoModel::listPapersByAuthor($author_id);
        $papers_member = BaiBaoModel::getPapersPaticipateByAuthor($author_id);
        $VIEW = "./view/DanhSachBBTacGia.phtml";
        require("./template/template.phtml");
    }
    public function updateProfile()
    {
        LoginController::authentication();
        // Xử lý khi submit form cập nhật profile
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Xử lý thông tin cơ bản
            $user_id = $_SESSION["UserID"];
            $full_name = $_POST['full_name'];
            $website = $_POST['website'];
            $bio = $_POST['bio'];
            $education = $_POST['education'];
            $work_experiences = $_POST['work_experiences'];
            $interests = $_POST['interests']; // Đây sẽ là mảng

            // Tạo mảng profile JSON mới
            $profile = array(
                "bio" => $bio,
                "interests" => $interests,
                "education" => $education,
                "work_experiences" => $work_experiences
            );

            // Chuyển mảng profile thành JSON
            $profile_json_text = json_encode($profile);

            // Xử lý upload ảnh đại diện
            if ($_FILES['image_path']['error'] == UPLOAD_ERR_OK) {
                $image_path = 'images/' . basename($_FILES['image_path']['name']);
                move_uploaded_file($_FILES['image_path']['tmp_name'], $image_path);
            } else {
                $image_path = $_POST['old_pp'];
            }

            // Cập nhật thông tin vào cơ sở dữ liệu
            $success = TacGiaModel::updateProfile($user_id, $full_name, $website, $profile_json_text, $image_path);

            if ($success) {
                // Chuyển hướng sau khi cập nhật thành công
                header("Location: index.php?action=profile&user_id=" . $user_id);
                exit();
            } else {
                // Xử lý khi cập nhật không thành công
                // Thường là hiển thị thông báo lỗi
                $error_message = "Có lỗi xảy ra khi cập nhật profile. Vui lòng thử lại.";
            }
        }


        $author_id = $_SESSION["UserID"];
        $author = TacGiaModel::getAuthorById($author_id);
        $VIEW = "./view/CapNhatThongTin.phtml";
        require("./template/template.phtml");
    }
}
