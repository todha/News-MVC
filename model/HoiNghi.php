<?php
class HoiNghiModel {
    public $conference_id;
    public $name;
    public $abbreviation;
    public $rank;
    public $start_date;
    public $end_date;
    public $type;

    // Phương thức khởi tạo
    function __construct() {
        $this->conference_id = "";
        $this->name = "";
        $this->abbreviation = "";
        $this->rank = "";
        $this->start_date = "";
        $this->end_date = "";
        $this->type = "";
    }

    // Phương thức tĩnh để lấy danh sách tất cả các hội nghị
    public static function listAll() {
        // Kết nối đến cơ sở dữ liệu
        $mysqli = connect();
        // Đảm bảo dữ liệu truy vấn được mã hóa đúng
        $mysqli->query("SET NAMES utf8");

        // Truy vấn để lấy tất cả các hội nghị
        $query = "SELECT * FROM CONFERENCES";
        $result = $mysqli->query($query);

        // Khởi tạo mảng để chứa danh sách hội nghị
        $conferenceList = array();

        // Kiểm tra kết quả truy vấn và tạo đối tượng ConferenceModel
        if ($result) {
            foreach ($result as $row) {
                $conference = new HoiNghiModel();
                $conference->conference_id = $row["conference_id"];
                $conference->name = $row["name"];
                $conference->abbreviation = $row["abbreviation"];
                $conference->rank = $row["rank"];
                $conference->start_date = $row["start_date"];
                $conference->end_date = $row["end_date"];
                $conference->type = $row["type"];
                $conferenceList[] = $conference; // Thêm đối tượng vào mảng
            }
        }

        // Đóng kết nối
        $mysqli->close();

        // Trả về danh sách các hội nghị
        return $conferenceList;
    }
}