<?php

/**
 *  ==================================================================
 *        文 件 名: Pagination.class.php
 *        概    要: 经典分页类
 *        作    者: IT小强
 *        创建时间: 2016-08-11
 *        修改时间: 2016-09-11
 *        copyright (c)2016 admin@xqitw.com
 *  ==================================================================
 */
class Pagination {
    /* 总记录数*/
    private $total_rows;
    /* 总页数*/
    private $page_num;
    /* 每页显示记录数 */
    private $row_cont;
    /* 当前页*/
    private $cur_page;
    /* 需要显示的页码数量*/
    private $p_num;
    /* GET提交的参数名称 */
    private $act_name;
    /* 记录偏移值 */
    public $offset;
    /* 页面url */
    private $url;
    /* ajax翻页 */
    private $ajax;
    
    /**
     * Pagination 构造函数.
     * @param $total_rows ,总记录数
     * @param $row_cont , 每页显示的记录条数
     * @param $url_arg ,页面参数，以？开始
     * @param $p_num ,需要显示的页码数量
     * @param $act_name ,GET提交的参数名称,默认为cur_page
     * @param bool $ajax ,是否开启ajax翻页
     */
    function __construct($total_rows, $row_cont = 10, $ajax = false, $url_arg = NULL, $p_num = 3, $act_name = 'cur_page') {
        $this->ajax = $ajax;
        $this->total_rows = $total_rows;
        $this->act_name = $act_name;
        $this->row_cont = $row_cont;
        $this->p_num = $p_num;
        $this->url = $this->getUrl($url_arg);
        $this->page_num = $this->getPageNum();
        $this->cur_page = $this->getCurPage();
        $this->offset = (($this->cur_page) - 1) * ($this->row_cont);
    }
    
    /** 分页类唯一调用接口
     * @param bool $fist ,是否显示 首页 和 上一页,默认为true
     * @param bool $mid ,是否显示 中间数字页码 ,默认为true
     * @param bool $last ,是否显示 尾页 下一页 和 共...页 ,默认为true
     * @param bool $post ,是否显示 手动 输入跳转表单 ,默认为true
     * @return null|string
     */
    function outPagination($fist = true, $mid = true, $last = true, $post = true) {
        $list = NULL;
        if ($this->total_rows > $this->row_cont) {
            $list .= '<div id="pagination-nav" class="pagination-nav">';
            $list .= $fist == true ? $this->getFirstPage() : NULL;
            $list .= $mid == true ? $this->getMiddlePage() : NULL;
            $list .= $last == true ? $this->getLastPage() : NULL;
            $list .= $post == true ? $this->getPostPage() : NULL;
            $list .= '</div>';
        }
        return $list;
    }
    
    /**
     * 获取页面URL参数
     * @param $url_arg ,URL参数
     * @return string
     */
    private function getUrl($url_arg = NULL) {
        $url = strpos($url_arg, '?') === false ? $url_arg . '?' : $url_arg . '&';
        return $url;
    }
    
    /**
     * 获取总页数
     * @return float
     */
    private function getPageNum() {
        $page_num = ceil($this->total_rows / $this->row_cont);
        return $page_num;
    }
    
    /**
     * 获取当前页
     * @return int
     */
    private function getCurPage() {

        if ($this->ajax == true) {
            if ($_POST && isset($_POST['cur'])) {
                $_POST['cur'] = substr($_POST['cur'], strrpos($_POST['cur'], '_') + 1);
                if ($_POST['cur'] < 1) {
                    $cur_page = 1;
                } else if ($_POST['cur'] > $this->page_num) {
                    $cur_page = $this->page_num;
                } else {
                    $cur_page = $_POST['cur'];
                }
            } else {
                $cur_page = 1;
            }
        } else {
            if (isset($_POST[$this->act_name])) {
                if ($_POST[$this->act_name] < 1) {
                    $cur_page = 1;
                } else if ($_POST[$this->act_name] <= $this->page_num) {
                    $cur_page = $_POST[$this->act_name];
                } else {
                    $cur_page = $this->page_num;
                }
            } else if (isset($_GET[$this->act_name])) {
                if ($_GET[$this->act_name] < 1) {
                    $cur_page = 1;
                } else if ($_GET[$this->act_name] <= $this->page_num) {
                    $cur_page = $_GET[$this->act_name];
                } else {
                    $cur_page = $this->page_num;
                }
            } else {
                $cur_page = 1;
            }
        }
        return $cur_page;
    }
    
    /**
     * 输出 首页,上一页
     * @return null|string
     */
    private function getFirstPage() {
        $list = NULL;
        if ($this->cur_page > 1) {
            $href1 = $this->ajax == false ? ' href="' . $this->url . $this->act_name . '=1"' : '';
            $href2 = $this->ajax == false ? ' href="' . $this->url . $this->act_name . '=' . ($this->cur_page - 1) . '"' : '';
            $list = '<a class="cur_p_1" ' . $href1 . '>首页</a>';
            $list .= '<a class="cur_p_' . ($this->cur_page - 1) . '" ' . $href2 . '>上一页</a>';
        }
        return $list;
    }
    
    /**
     * 输出中间数字页码
     * @return null|string
     */
    private function getMiddlePage() {
        $list = NULL;
        for ($i = 1; $i <= $this->page_num; $i++) {
            if ($this->cur_page == $i) {
                $list .= '<span class="current">' . $i . '</span>';
            } else {
                if ($i > ($this->cur_page - ($this->p_num)) && $i < ($this->cur_page + ($this->p_num))) {
                    $href = ' href="' . $this->url . $this->act_name . '=' . $i . '"' ;
                    $list .= '<a class="cur_p_' . $i . '" ' . $href . '>' . $i . '</a>';
                }
            }
        }
        return $list;
    }
    
    /**
     * 输出 尾页,下一页,共..页
     * @return null|string
     */
    private function getLastPage() {
        $list = NULL;
        $href1 = $this->ajax == false ? ' href="' . $this->url . $this->act_name . '=' . ($this->cur_page + 1) . '"' : '';
        $href2 = $this->ajax == false ? ' href="' . $this->url . $this->act_name . '=' . $this->page_num . '"' : '';
        if ($this->cur_page < $this->page_num) {
            $list = '<a class="cur_p_' . ($this->cur_page + 1) . '" ' . $href1 . '>下一页</a>';
            $list .= '<a class="cur_p_' . $this->page_num . '" ' . $href2 . '>尾页</a>';
        }
        $list .= '<a class="cur_p_' . $this->page_num . '"' . $href2 . '>共' . $this->page_num . '页</a>';
        return $list;
    }
    
    /**
     * 完成手动输入页码,跳转到指定页功能
     * @return string
     */
    private function getPostPage() {
        $btn_type = $this->ajax == false ? 'submit' : 'button';
        $list = '<form class="pag-form" action="' . substr($this->url, 0, -1) . '" method="post">';
        $list .= '<input class="cur_p_post" title="请输入页码" type="text" name="' . $this->act_name . '" value="' . mt_rand(1, $this->page_num) . '">';
        $list .= '<input  class="cur_p_btn btn btn-submit" type="' . $btn_type . '" value="GO">';
        $list .= '</form>';
        return $list;
    }
}