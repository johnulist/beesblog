<?php
/**
 * 2017 Thirty Bees
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@thirtybees.com so we can send you a copy immediately.
 *
 *  @author    Thirty Bees <modules@thirtybees.com>
 *  @copyright 2017 Thirty Bees
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class BeesBlogPostCategory
 */
class BeesBlogPostCategory extends BeesBlogObjectModel
{
    public $id_bees_blog_post_category;
    public $id_author;
    public static $definition = array(
        'table' => 'bees_blog_post_category',
        'primary' => 'id_bees_blog_post_category',
        'fields' => array(
            'id_bees_blog_post_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true, 'db_type' => 'INT(11) UNSIGNED'),
            'id_bees_blog_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true, 'db_type' => 'INT(11) UNSIGNED'),
        ),
    );

    /**
     * Get total by category
     *
     * @param $idLang
     * @param $idCategory
     * @param $limitStart
     * @param $limit
     *
     * @return array|bool
     */
    public static function getTotalByCategory($idLang, $idCategory, $limitStart, $limit)
    {
        $results = array();
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('bees_blog_post_lang', 'sbpl');
        $sql->innerJoin('bees_blog_post', 'sbp', 'sbp.`id_bees_blog_post` = sbpl.`id_bees_blog_post`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbp.`active` = 1');
        $sql->where('sbp.`id_category` = '.(int) $idCategory);
        $sql->orderBy('sbp.`id_bees_blog_post` DESC');
        $sql->limit((int) $limit, (int) $limitStart);
        if (!$posts = Db::getInstance()->executeS($sql)) {
            return false;
        }

        $i = 0;
        $blogCategory = new BeesBlogCategory();
        foreach ($posts as $post) {
            $result = array(
                'id_post' => $post['id_bees_blog_post'],
                'viewed' => $post['viewed'],
                'meta_title' => $post['meta_title'],
                'meta_description' => $post['meta_description'],
                'short_description' => $post['short_description'],
                'content' => $post['content'],
                'meta_keyword' => $post['meta_keyword'],
                'id_category' => $post['id_category'],
                'link_rewrite' => $post['link_rewrite'],
                'cat_link_rewrite' => $blogCategory->getCatLinkRewrite($post['id_category']),
            );
            $employee = new  Employee($post['id_author']);

            $result['lastname'] = $employee->lastname;
            $result['firstname'] = $employee->firstname;
            if (file_exists(_PS_MODULE_DIR_.'beesblog/images/'.$post['id_bees_blog_post'].'.jpg')) {
                $image = $post['id_bees_blog_post'];
                $result['post_img'] = $image;
            } else {
                $result['post_img'] = 'no';
            }
            $result['created'] = $post['created'];
            $results[$i] = $result;
            $i++;
        }

        return $results;
    }
}
