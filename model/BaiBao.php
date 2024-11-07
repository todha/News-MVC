<?php
class BaiBaoModel
{
    public $paper_id;
    public $title;
    public $author_string_list;
    public $abstract;
    public $conference_id;
    public $topic_id;
    public $user_id;
    public $topic_name;
    public $start_date;
    public $end_date;

    // Phương thức khởi tạo
    function __construct()
    {
        $this->paper_id = "";
        $this->title = "";
        $this->author_string_list = "";
        $this->abstract = "";
        $this->conference_id = "";
        $this->topic_id = "";
        $this->user_id = "";
        $this->topic_name = "";
        $this->start_date = "";
        $this->end_date = "";
    }

    public static function listAll()
    {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");
        $query = "SELECT * FROM papers";
        $result = $mysqli->query($query);
        $paperList = array();
        if ($result) {
            foreach ($result as $row) {
                $paper = new BaiBaoModel();
                $paper->paper_id = $row["paper_id"];
                $paper->title = $row["title"];
                $paper->author_string_list = $row["author_string_list"];
                $paper->abstract = $row["abstract"];
                $paperList[] = $paper;
            }
        }
        $mysqli->close();
        return $paperList;
    }

    public static function getPapersPaticipateByAuthor($author_id)
    {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");


        $query = "
        SELECT p.paper_id, p.title, p.abstract, p.conference_id, p.topic_id, t.topic_name
        FROM PARTICIPATION pa
        JOIN PAPERS p ON pa.paper_id = p.paper_id
        JOIN TOPICS t ON p.topic_id = t.topic_id
        WHERE pa.author_id = $author_id
        AND pa.role = 'member'
    ";

        $result = $mysqli->query($query);
        $paperList = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $paperList[] = $row;
            }
        }
        $mysqli->close();
        return $paperList;
    }


    // danh sách bài báo mới nhất trong năm theo chủ đề
    public static function listLatestPapersByTopic()
    {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");

        // Truy vấn để lấy các bài báo mới nhất trong năm theo chủ đề
        $query = "
            SELECT 
                p.paper_id,
                p.title,
                p.author_string_list,
                p.abstract,
                p.conference_id,
                p.topic_id,
                p.user_id,
                t.topic_name,
                c.start_date,
                c.end_date
            FROM 
                PAPERS p
            JOIN 
                TOPICS t ON p.topic_id = t.topic_id
            JOIN 
                CONFERENCES c ON p.conference_id = c.conference_id
            WHERE 
                YEAR(c.start_date) = YEAR(CURDATE())
            AND
                p.paper_id IN (
                    SELECT 
                        paper_id
                    FROM 
                        (
                            SELECT 
                                paper_id,
                                ROW_NUMBER() OVER (PARTITION BY p.topic_id ORDER BY c.start_date DESC) AS row_num
                            FROM 
                                PAPERS p
                            JOIN 
                                CONFERENCES c ON p.conference_id = c.conference_id
                            WHERE 
                                YEAR(c.start_date) = YEAR(CURDATE())
                        ) AS ranked_papers
                    WHERE 
                        row_num <= 5
                )
            ORDER BY 
                t.topic_name, c.start_date DESC
        ";

        $result = $mysqli->query($query);
        $paperList = array();

        if ($result) {
            foreach ($result as $row) {
                $paper = new BaiBaoModel();
                $paper->paper_id = $row["paper_id"];
                $paper->title = $row["title"];
                $paper->author_string_list = $row["author_string_list"];
                $paper->abstract = $row["abstract"];
                $paper->conference_id = $row["conference_id"];
                $paper->topic_id = $row["topic_id"];
                $paper->user_id = $row["user_id"];
                $paper->topic_name = $row["topic_name"];
                $paper->start_date = $row["start_date"];
                $paper->end_date = $row["end_date"];
                $paperList[] = $paper;
            }
        }

        $mysqli->close();
        return $paperList;
    }

    public static function listPapersByAuthor($author_id)
    {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");

        $query = "
            SELECT 
                p.paper_id,
                p.title,
                p.author_string_list,
                p.abstract,
                p.conference_id,
                p.topic_id,
                p.user_id,
                t.topic_name,
                c.start_date,
                c.end_date
            FROM 
                PAPERS p
            JOIN 
                TOPICS t ON p.topic_id = t.topic_id
            JOIN 
                CONFERENCES c ON p.conference_id = c.conference_id
            WHERE 
                p.user_id = {$author_id}
            ORDER BY 
                c.start_date DESC, p.paper_id DESC
        ";

        $result = $mysqli->query($query);

        $paperList = array();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $paperList[] = $row;
            }
        }

        return $paperList;
    }

    //xem thong tin chi tiet bai bao
    public static function getPaperById($paper_id)
    {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");

        $query = "
            SELECT 
                p.paper_id,
                p.title,
                p.author_string_list,
                p.abstract,
                p.conference_id,
                p.topic_id,
                c.name,
                p.user_id,
                t.topic_name,
                c.start_date,
                c.end_date,
                a.full_name AS author_name
            FROM 
                PAPERS p
            JOIN 
                TOPICS t ON p.topic_id = t.topic_id
            JOIN 
                CONFERENCES c ON p.conference_id = c.conference_id
            JOIN 
                AUTHORS a ON p.user_id = a.user_id
            WHERE 
                p.paper_id = ?
        ";

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $paper_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $paper = null;
        if ($result && $result->num_rows > 0) {
            $paper = $result->fetch_assoc();
        }

        $stmt->close();
        $mysqli->close();

        return $paper;
    }


    // Thêm bài báo mới vào cơ sở dữ liệu
    public static function addPaper($title, $abstract, $conference_id, $topic_id, $author_ids)
    {
        $mysqli = connect();
        $author_id = $_SESSION["UserID"];
        $author = TacGiaModel::getAuthorById($author_id);

        $authors = TacGiaModel::getAuthorsByIds($author_ids);
        $author_names = array_map(function ($author) {
            return $author->full_name;
        }, $authors);
        $author_string_list = $author['full_name'] . ', ' . implode(', ', $author_names);

        $stmt = $mysqli->prepare("
            INSERT INTO PAPERS (title, abstract, conference_id, topic_id, user_id, author_string_list)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssiiis", $title, $abstract, $conference_id, $topic_id, $author_id, $author_string_list);
        $stmt->execute();
        $paper_id = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();

        return $paper_id;
    }


    // Thêm tác giả vào bài báo
    public static function addAuthorToPaper($paper_id, $user_id)
    {
        $mysqli = connect();
        $date_added = date("Y-m-d H:i:s");

        $stmt = $mysqli->prepare("
        INSERT INTO PARTICIPATION (paper_id, author_id, role, date_added) 
        VALUES (?, ?, 'member', ?)
    ");

        $stmt->bind_param("iis", $paper_id, $user_id, $date_added);
        $stmt->execute();

        $stmt->close();
        $mysqli->close();
    }

    public static function searchPapers($keyword, $author, $conference, $time, $field, $page = 1, $limit = 10)
    {
        $mysqli = connect(); 
        $mysqli->query("SET NAMES utf8"); 

        $offset = ($page - 1) * $limit;

        $query = "SELECT p.*, a.full_name AS author_name, c.name AS conference_name,c.start_date, t.topic_name
                  FROM PAPERS p
                  INNER JOIN AUTHORS a ON p.user_id = a.user_id
                  INNER JOIN CONFERENCES c ON p.conference_id = c.conference_id
                  INNER JOIN TOPICS t ON p.topic_id = t.topic_id
                  WHERE 1=1";

        // Thêm điều kiện dựa trên tiêu chí tìm kiếm
        if (!empty($keyword)) {
            $query .= " AND p.title LIKE '%" . $mysqli->real_escape_string($keyword) . "%'";
        }
        if (!empty($author)) {
            $query .= " AND a.full_name LIKE '%" . $mysqli->real_escape_string($author) . "%'";
        }
        if (!empty($conference)) {
            $query .= " AND (c.name LIKE '%" . $mysqli->real_escape_string($conference) . "%'
                             OR c.abbreviation LIKE '%" . $mysqli->real_escape_string($conference) . "%')";
        }
        if (!empty($time)) {
            $query .= " AND DATE(p.created_at) = '" . $mysqli->real_escape_string($time) . "'";
        }
        if (!empty($field)) {
            $query .= " AND t.topic_name LIKE '%" . $mysqli->real_escape_string($field) . "%'";
        }

        $result = $mysqli->query($query);

        $papers = [];
        if ($result) {
            while ($row = $result->fetch_object()) {
                $papers[] = $row;
            }
            $result->free();
        }
        $mysqli->close();
        return $papers;
    }
    public static function updateAuthorStringList($paperId, $newAuthorStringList) {
        $mysqli = connect();
        $mysqli->query("SET NAMES utf8");
    
        $query = "UPDATE PAPERS SET author_string_list = ? WHERE paper_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("si", $newAuthorStringList, $paperId);
    
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }
    

}
