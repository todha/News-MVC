<?php

class HomeController
{
    public function index()
    {
        // $cate_data = LoaiCongViecModel::listAll();
        $data = BaiBaoModel::listLatestPapersByTopic();
        $VIEW = "./view/TrangChu.phtml";
        require("./template/template.phtml");
    }
}
