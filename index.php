<?php

if (session_id() === '') {
    session_start();
}

require_once("./controller/Login.php");
require_once("./controller/Home.php");
require_once("./controller/Paper.php");
require_once("./controller/Author.php");

require_once("./model/User.php");
require_once("./model/BaiBao.php");
require_once("./model/TacGia.php");
require_once("./model/ChuDe.php");
require_once("./model/HoiNghi.php");
require_once("./config/dbconnect.php");

$action = "";
if (isset($_REQUEST["action"])) {
    $action = $_REQUEST["action"];
}

switch ($action) {
    case "error":
        $controller = new LoginController();
        $controller->unauthorized_page();
        break;
    case "logout":
        $controller = new LoginController();
        $controller->logout();
        break;
    case "login":
        $controller = new LoginController();
        $controller->login();
        break;
    case "papers":
        $controller = new PaperController();
        $controller->latestPapersByTopic();
        break;
    case "profile":
        $controller = new AuthorController();
        $controller->showInfo();
        break;
    case "paperDetail":
        $controller = new PaperController();
        $controller->paperDetail();
        break;
    case "updateProfile":
        $controller = new AuthorController();
        $controller->updateProfile();
        break;
    case "myPapers":
        $controller = new AuthorController();
        $controller->showPaper();
        break;
    case "addPaperForm":
        $controller = new PaperController();
        $controller->addPaperForm();
        break;
    case "addPaper":
        $controller = new PaperController();
        $controller->addPaper();
        break;
    case "search":
        $controller = new PaperController();
        $controller->search();
        break;
    case "removeAuthor":
        $controller = new PaperController();
        $controller->removeAuthor();
        break;
    default:
        $controller = new HomeController();
        $controller->index();
        break;
}
