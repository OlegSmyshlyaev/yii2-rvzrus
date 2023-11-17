<?php

namespace frontend\controllers;

use Imagick;
use Yii;
use yii\web\UploadedFile;
use yii\web\HttpException;
use yii\web\ForbiddenHttpException;
use yii\helpers\BaseFileHelper;
use yii\web\Response;

use common\models\Favorites;
use common\models\News;
use common\models\Blogs;
use common\models\SimilarBlogs;
use common\models\SimilarNews;
use frontend\models\PostForm;
use common\models\Categories;
use common\models\Tags;
use common\models\NewsTags;
use common\models\User;
use common\models\Catalog;
use common\models\CatSubcategories;
use common\models\Notifications;
use common\models\Company;
use common\models\CompanyDescription;
use common\models\CompanyRating;

use frontend\models\NaturalPerson;
use common\models\Companies;
use common\models\Suppliers;
use common\models\Associations;
use common\models\Comments;
use common\models\Mail;

use common\models\ServiceUrl;

use frontend\models\FormCompanyCallback;

use yii\helpers\Url;

class CompanyController extends \yii\web\Controller {
    
    const COMPANY_PARAM_SORT = "csort";
    const COMPANY_PARAM_ORDER = "corder";
    const COMPANY_PARAM_SORT_NAME = "name";
    const COMPANY_PARAM_SORT_STAFF = "staff";
    const COMPANY_PARAM_SORT_PROFIT = "profit";
    const COMPANY_PARAM_SORT_RATING = "rating";
    
    const COUNT_ITEMS = 24; 
    //const COUNT_ITEMS = 3; 
    
    /**
     * Home page of a site, list of all categories and news
     * @return [type] [description]
     */
    public function actionIndex() {
        
        $data['company_list'] = array();
        
        $list = $this->getItems(0, self::COUNT_ITEMS);
        $data['company_list'] = $list;
        
        $data['btn_show_more'] = count($list) < $this->getCntItems();
        
        $data['list'] = ($_GET['view'] == 'list');
        $data['company_view_switch'] = $this->getViewSwitcher();
        
        $data['meta_title'] = "Каталог компаний";
        
        $data['filter']['search']['q'] = isset($_GET['q']) ? htmlentities(urldecode($_GET['q'])) : "";
        $data['filter']['type'] = $this->getFilterTypes();
        $data['filter']['sort'] = $this->getFilterSorts();
        
        return $this->render('index', $data);
    }
    
    public function actionView ($company_id) {
        
        $data = [];
        $data['title'] = "";
        $data['company'] = $this->getItem($company_id);
        $company = $data['company'];
        
        $data['company']['show_fin_2019'] = $company['revenue_2019'] || $company['profit_2019'] || $company['debt_obligations_2019'] || $company['investments_2019'];
        $data['company']['show_fin_2020'] = $company['revenue_2020'] || $company['profit_2020'] || $company['debt_obligations_2020'] || $company['investments_2020'];
        $data['company']['show_fin_2022'] = $company['revenue_2022'] || $company['profit_2022'] || $company['debt_obligations_2022'] || $company['investments_2022'];
        
        $data['meta_title'] = "Каталог компаний - " . $company['name'];
        
        return $this->render('view', $data);
    }
    
    public function actionCallback ($company_id) {
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $company_id;
        
        $json = array();
        
        $json["success-description"] = "Заявка отправлена";
        
        $obForm = new FormCompanyCallback();
        
        if ($obForm->validate()) {
            
            $param['company_id'] = $company_id;
            
            $obForm->sendMailAdmin($param);
            
            $obForm->sendMailCompany($param);
            
        } else {
            $json["errors"] = $obForm->getErrors();
        }
        
        echo json_encode($json);
    }
    
    public function actionGetajaxplate ($offset = 0) {
        
        $json = array();
        
        $countAll = $this->getCntItems();
        $json["offset"] = $offset + self::COUNT_ITEMS;
        $json["isend"] = ($offset + self::COUNT_ITEMS) >= $countAll;
        
        $json["html"] = $this->getHtmlItemsPlate($offset);
        
        echo json_encode($json);
    }
    
    public function actionGetajaxlist ($offset = 0) {
        
        $json = array();
        
        $countAll = $this->getCntItems();
        $json["offset"] = $offset + self::COUNT_ITEMS;
        $json["isend"] = ($offset + self::COUNT_ITEMS) >= $countAll;
        
        $json["html"] = $this->getHtmlItemsList($offset);
        
        echo json_encode($json);
    }
    
    private function getItems ($offset = 0 , $limit = 12) {
        
        $sort = isset($_GET[self::COMPANY_PARAM_SORT]) ? $_GET[self::COMPANY_PARAM_SORT] : self::COMPANY_PARAM_SORT_PROFIT;
        $order = ('desc' == $_GET[self::COMPANY_PARAM_ORDER]) ? "desc" : "asc";
        $search = strlen($_GET['q']) ? $_GET['q'] : "";
        
        $params = [];
        $items = array();
        //$companies = Company::find()->where(['active' => 1])->offset($start)->limit($offset)->orderby('name asc')->all();
        
        $sql = "SELECT `c`.`company_id`, `c`.`active`, `c`.`date_modified`, `c`.`date_created`, `cd`.`revenue_2022`, `cd`.`profit_2022`, `cd`.`rvzrus_rating`, `cr`.`rating`
        FROM `company` `c`
        LEFT JOIN `company_description` `cd`
        ON (`cd`.`company_id` = `c`.`company_id`)
        LEFT JOIN `company_rating` `cr`
        ON (`cr`.`company_id` = `c`.`company_id`)
        WHERE ";
        
        $whereBlock = $this->getSqlWhereItems();
        
        $sql .= $whereBlock["sql"];
        $params = array_merge($params, $whereBlock['params']);
        
        if (!isset($_GET[self::COMPANY_PARAM_SORT])) {
            $sql .= "ORDER BY `cd`.`profit_2022` desc ";
        } elseif ($sort == self::COMPANY_PARAM_SORT_NAME) {
            $sql .= "ORDER BY `cd`.`name` " . $order . " ";
        } elseif ($sort == self::COMPANY_PARAM_SORT_PROFIT) {
            $sql .= "ORDER BY `cd`.`profit_2022` " . $order . " ";
        } elseif ($sort == self::COMPANY_PARAM_SORT_STAFF) {
            $sql .= "ORDER BY `cd`.`amount_staff` " . $order . " ";
        } elseif ($sort == self::COMPANY_PARAM_SORT_RATING) {
            $sql .= "ORDER BY `cr`.`rating` " . $order . " ";
        } else {
            $sql .= "ORDER BY `cd`.`profit_2022` desc ";
        }
        
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;
        $sql .= "LIMIT :limit OFFSET :offset";
        
        $companies = Yii::$app->db->createCommand($sql, $params)->queryAll();
        
        foreach ($companies as $company) {
            
            $item = array();
            $item['company_id'] = $company['company_id'];
            $item['active'] = $company['active'];
            $item['date_modified'] = $company['date_modified'];
            $item['date_created'] = $company['date_created'];
            $item['rating'] = $company['rating'];
            
            $description =  $this->getItem($item['company_id']);
            
            $item = array_merge($item, $description);
            
            $items[] = $item;
        }
        
        return $items;
    }
    
    private function getCntItems () {
        
        $params = [];
        
        $sql = "SELECT COUNT(*) AS `cnt`, `c`.`active` FROM `company` `c` LEFT JOIN `company_description` `cd` ON (`c`.company_id = `cd`.company_id) WHERE ";
        
        $arWhere = $this->getSqlWhereItems();
        
        $sql .= $arWhere["sql"];
        $params = array_merge($params, $arWhere['params']);
        
        $query = Yii::$app->db->createCommand($sql, $params)->queryOne();
        
        return $query['cnt'];
    }
    
    private function getSqlWhereItems () {
        
        $search = strlen($_GET['q']) ? $_GET['q'] : "";
        
        $sql .= "`c`.active = '1' AND ";
        
        if (strlen($search)) {
            $sql .= "(`cd`.`name` LIKE :search OR `cd`.`name_short` LIKE :search) AND ";
            $params[':search'] = "%{$search}%";
        }
        
        $sql .= "`c`.`type` = :type AND "; 
        $params[':type'] = $this->getType();
        
        $sql .= "1 ";
        
        return array("sql"=>$sql, "params"=>(array)$params);
    }
    
    private function getType () {
        
        if (isset($_GET['ctype'])) {
            return (int)$_GET['ctype'];
        }
        
        $sql = "SELECT * FROM company_type WHERE 1 ORDER BY `sort` ASC";
        
        $params = [];
        $query = Yii::$app->db->createCommand($sql, $params)->queryOne();
        
        return $query['company_type_id'];
    }

    private function getHtmlItemsPlate ($offset = 0 , $limit = self::COUNT_ITEMS) {
        
        $this->layout = 'none';
        
        $data = [];
        $data['company_list'] = $this->getItems((int)$offset, (int)$limit);
        
        return $this->render("list-plate", $data);
    }
    
    private function getHtmlItemsList ($offset = 0 , $limit = self::COUNT_ITEMS) {
        
        $this->layout = 'none';
        
        $data = [];
        $data['company_list'] = $this->getItems((int)$offset, (int)$limit);
        
        return $this->render("list", $data);
    }
    
    private function getItem ($company_id) {
        
        $model = CompanyDescription::findOne(['company_id'=>$company_id]);
        
        $company['company_id'] = $model->company_id;
        $company['name'] = $model->name;
        $company['name_short'] = $model->name_short;
        $company['image'] = $model->logo;
        $company['detail_text'] = $model->company_info;
        $company['phone'] = $model->phone;
        $company['email'] = $model->email;
        $company['website'] = $model->website;
        $company['inn'] = $model->inn;
        $company['ogrn'] = $model->ogrn;
        $company['sro'] = (bool)$model->member_sro;
        $company['fssp'] = (bool)$model->member_fssp;
        $company['okved'] = $model->okved;
        $company['member_napki_2022'] = $model->member_napki_2022;
        $company['amount_staff'] = $model->amount_staff;
        $company['revenue_2019'] = $model->revenue_2019;
        $company['revenue_2022'] = $model->revenue_2022;
        $company['profit_2019'] = $model->profit_2019;
        $company['profit_2022'] = $model->profit_2022;
        $company['rvzrus_rating'] = $model->rvzrus_rating;
        $company['debt_obligations_2019'] = $model->debt_obligations_2019;
        //$company['fin_investments_2019'] = $model->cost_2019;
        
        $model = CompanyRating::findOne(['company_id'=>$company_id]);
        $company['rating'] = $model->rating;
        
        // получить список постов.
            $sql = '
            SELECT 
                cp.`id`,
                cp.`company_id` `company_id`,
                cp.`post_id` `post_id`,
                cp.`post_type` `post_type`,
                b.`post_id` `blog_post_id`,
                b.`title` `blog_title`,
                b.`thumbnail` `blog_thumbnail`,
                b.`date` `blog_date`,
                n.`post_id` `new_post_id`,
                n.`title` `new_title`,
                n.`thumbnail` `new_thumbnail`,
                n.`date` `new_date`
            FROM 
                `company_post` `cp`
            LEFT JOIN `blogs` `b`
            ON (b.`post_id` = cp.`post_id` AND cp.`post_type` = "blog" AND b.`status` = "1")
            LEFT JOIN `news` `n`
            ON (n.`post_id` = cp.`post_id` AND cp.`post_type` = "new" AND n.`status` = "1")            
            WHERE 
                `company_id` = :company_id AND 
                1
            ';
            
            $params = array("company_id" => (int)$company_id);
            //$params = array("post_id" => 0);
            
            $query = Yii::$app->db->createCommand($sql, $params)->queryAll();
        
            $timestamp = [];
            $company_posts = [];
            
            foreach ($query as $post) {
                
                if ($post["post_type"] == "blog") {
                    $companyPost = [
                        'post_id' => $post["post_id"],
                        'title' => $post["blog_title"],
                        'thumbnail' => $post["blog_thumbnail"],
                        'timestamp' => strtotime($post["blog_date"]),
                        'date' => ArticleController::formatDateToRussian(date('d m Y, H:i',strtotime($post["blog_date"]))),
                        'url' => Url::toRoute(['blog/view', 'id'=>$post["post_id"],]),
                    ];
                } elseif ($post["post_type"] == "new") {
                    $companyPost = [
                        'post_id' => $post["post_id"],
                        'title' => $post["new_title"],
                        'thumbnail' => $post["new_thumbnail"],
                        'timestamp' => strtotime($post["new_date"]),
                        'date' => ArticleController::formatDateToRussian(date('d m Y, H:i',strtotime($post["new_date"]))),
                        'url' => Url::toRoute(['article/view', 'id'=>$post["post_id"],]),
                    ];
                }
                
                $timestamp[] = $companyPost['timestamp'];
                $company_posts[] = $companyPost;
            }
            
            array_multisort($timestamp, $company_posts);
            $company_posts = array_reverse($company_posts);
            
            $company['posts'] = $company_posts;
            
        return $company;
    }
    
    private function getFilterTypes() {
        
        $sql = "SELECT * FROM company_type WHERE 1 ORDER BY `sort` ASC";
        
        $params = [];
        $query = Yii::$app->db->createCommand($sql, $params)->queryAll();
        
        // Добавить доп. поля. 3 поля.
        foreach ($query as $row) {
            
            $item = [];
            $item['type_id'] = $type_id = $row['company_type_id'];
            $item['value'] = $type_id;
            $item['name'] = $row['name'];
            
            $filter[$type_id] = $item;
        }
        
        $isset_default = false;
        if ($_GET['ctype']) {
            $type = $_GET['ctype'];
            
            foreach ($filter as &$item) {
                if ($item['type_id'] == $type) {
                    $item['default'] = 1;
                    $isset_default = 1;
                }
            }unset($item);
        }
        
        if (!$isset_default) {
            foreach ($filter as &$item) {
                $item['default'] = 1;
                break;
            }unset($item);
        }
        
        
        return $filter;
    }
    
    private function getFilterSorts() {
        
        $filter = [
            //["value"=>'name-asc', "name"=>"По названию ↑",],
            //["value"=>'name-desc', "name"=>"По названию ↓",],
            ["value"=>'profit-asc', "name"=>"По прибыли ↑",],
            ["value"=>'profit-desc', "name"=>"По прибыли ↓",],
            ["value"=>'staff-asc', "name"=>"По кол-ву персонала ↑",],
            ["value"=>'staff-desc', "name"=>"По кол-ву персонала ↓",],
            ["value"=>'rating-asc', "name"=>"По рейтингу ↓",],
            ["value"=>'rating-desc', "name"=>"По рейтингу ↑",],
        ];
        
        // Установить дефолт
        $sort = $_GET[self::COMPANY_PARAM_SORT];
        $order = $_GET[self::COMPANY_PARAM_ORDER];
        
        if ($sort == self::COMPANY_PARAM_SORT_NAME && $order == "asc") {
            $def_key = "name-asc";
        } elseif ($sort == self::COMPANY_PARAM_SORT_NAME && $order == "desc") {
            $def_key = "name-desc";
        } elseif ($sort == self::COMPANY_PARAM_SORT_PROFIT && $order == "asc") {
            $def_key = "profit-asc";
        } elseif ($sort == self::COMPANY_PARAM_SORT_PROFIT && $order == "desc") {
            $def_key = "profit-desc";
        } elseif ($sort == self::COMPANY_PARAM_SORT_STAFF && $order == "asc") {
            $def_key = "staff-asc";
        } elseif ($sort == self::COMPANY_PARAM_SORT_STAFF && $order == "desc") {
            $def_key = "staff-desc";
        } elseif ($sort == self::COMPANY_PARAM_SORT_RATING && $order == "asc") {
            $def_key = "rating-asc";
        } elseif ($sort == self::COMPANY_PARAM_SORT_RATING && $order == "desc") {
            $def_key = "rating-desc";
        } else {
            $def_key = "profit-desc";
        }
        
        foreach ($filter as &$option) {
            if ($option['value'] == $def_key) {
                $option['default'] = true;
            }
        }
        
        return $filter;
    }
    
    private function getViewSwitcher () {
        
        $data = [
            'list' => ['code'=>'list', 'param'=>'list', 'active'=>'',],
            'normal' => ['code'=>'normal', 'active'=>'',],
        ];
        
        $ServiceUrl = new ServiceUrl($_SERVER['REQUEST_URI']);
        
        // Обработка
        foreach ($data as &$item) {
            
            //Установить активность
            $param = strval($item['param']);
            if ($_GET['view'] == $param) 
                $item['active'] = true;
            
            if (strlen($param)) {
                $ServiceUrl->setParam("view", $param);
            } else {
                $ServiceUrl->deleteParam("view");
            }
            
            $item['url'] = $ServiceUrl->get();
            
        }unset($item);
        
        
        return $data;
    }
    
}