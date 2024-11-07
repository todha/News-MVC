<?php

class PaperController
{
    public function latestPapersByTopic()
    {
        $data = BaiBaoModel::listLatestPapersByTopic();
        $VIEW = "./view/DanhSachBaiBao.phtml";
        require("./template/template.phtml");
    }

    public function paperDetail()
    {
        $id = $_REQUEST["id"];
        $paper = BaiBaoModel::getPaperById($id);
        $VIEW = "./view/ChiTietBaiBao.phtml";
        require("./template/template.phtml");
    }

    public function addPaperForm()
    {
        LoginController::authentication();
        $author_id = $_SESSION["UserID"];
        $conferences = HoiNghiModel::listAll();
        $topics = ChuDeModel::listAll();
        $authors = TacGiaModel::listAllExceptCurrAuthor($author_id);
        $VIEW = "./view/ThemBaiBao.phtml";
        require("./template/template.phtml");
    }

    public function addPaper()
    {
        LoginController::authentication();
        $title = $_POST['title'];
        $abstract = $_POST['abstract'];
        $conference_id = $_POST['conference_id'];
        $topic_id = $_POST['topic_id'];
        $author_ids = $_POST['authors'];

        // Thêm bài báo mới vào cơ sở dữ liệu
        $paper_id = BaiBaoModel::addPaper($title, $abstract, $conference_id, $topic_id, $author_ids);

        // Thêm các tác giả vào bài báo
        if ($paper_id) {
            foreach ($author_ids as $author_id) {
                BaiBaoModel::addAuthorToPaper($paper_id, $author_id);
            }
        }


        header("Location:index.php?action=myPapers");
    }

    public function searchAJAX()
    {
        $data = BaiBaoModel::listAll();
        $VIEW = "./view/TimKiemAJAX.phtml";
        require("./template/template.phtml");
    }

    public function search()
    {
        // Nhận các tham số tìm kiếm từ request GET
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $author = isset($_GET['author']) ? $_GET['author'] : '';
        $conference = isset($_GET['conference']) ? $_GET['conference'] : '';
        $time = isset($_GET['time']) ? $_GET['time'] : '';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // Gọi hàm searchPapers từ PaperModel để lấy dữ liệu bài báo
        $data = BaiBaoModel::searchPapers($keyword, $author, $conference, $time, $field, $page);

        // Đưa dữ liệu vào View để hiển thị
        $VIEW = "./view/TimKiemForm.phtml";
        $RESULT_VIEW = "./view/DanhSachBaiBao.phtml";

        // Include the main template and pass the result view to be included
        include('./template/template.phtml');
    }

    public function removeAuthor()
    {
        if (isset($_GET['paper_id']) && isset($_GET['author'])) {
            $paperId = intval($_GET['paper_id']);
            $authorToRemove = urldecode($_GET['author']);
    
            // Lấy thông tin bài báo từ cơ sở dữ liệu
            $paper = BaiBaoModel::getPaperById($paperId);
    
            if ($paper) {
                $authors = explode(',', $paper['author_string_list']);
                $authors = array_filter($authors, function($author) use ($authorToRemove) {
                    return trim($author) !== $authorToRemove;
                });
    
                // Cập nhật lại author_string_list
                $newAuthorStringList = implode(', ', $authors);
    
                // Lưu vào cơ sở dữ liệu
                BaiBaoModel::updateAuthorStringList($paperId, $newAuthorStringList);
    
                // Điều hướng lại trang chi tiết bài báo
                header("Location: index.php?action=paperDetail&id=" . $paperId);
                exit;
            }
        }
    }
}
