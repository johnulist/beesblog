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

namespace BeesBlogModule;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class BeesBlogPost
 */
class BeesBlogPost extends BeesBlogObjectModel
{
    // @codingStandardsIgnoreStart
    /** @var int $id_employee */
    public $id_employee;

    /** @var int $id_category */
    public $id_category;

    /** @var int $position */
    public $position = 0;

    /** @var bool $active */
    public $active = true;

    /** @var bool $available */
    public $available;

    /** @var string $date_add */
    public $date_add;

    /** @var string $date_upd */
    public $date_upd;

    /** @var string $short_description */
    public $short_description;

    /** @var int $viewed */
    public $viewed;

    /** @var bool $comments_allowed */
    public $comments_allowed = true;

    /** @var int $post_type */
    public $post_type;

    /** @var string $meta_title */
    public $meta_title;

    /** @var string $meta_keyword */
    public $meta_keyword;

    /** @var string $meta_description */
    public $meta_description;

    /** @var string $image */
    public $image;

    /** @var string $content */
    public $content;

    /** @var string $link_rewrite */
    public $link_rewrite;

    /** @var bool $lang_active */
    public $lang_active;

    /** @var bool $is_featured */
    public $is_featured;
    // @codingStandardsIgnoreEnd

    const PRIMARY = 'id_bees_blog_post';
    const TABLE = 'bees_blog_post';
    const LANG_TABLE = 'bees_blog_post_lang';
    const SHOP_TABLE = 'bees_blog_post_shop';

    public static $definition = [
        'table' => self::TABLE,
        'primary' => self::PRIMARY,
        'multishop' => true,
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true, 'default' => '1', 'db_type' => 'TINYINT(1)'],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'default' => '1', 'db_type' => 'INT(11) UNSIGNED'],
            'id_category' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'db_type' => 'INT(11) UNSIGNED'],
            'id_employee' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'db_type' => 'INT(11) UNSIGNED'],
            'available' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true, 'default' => '1', 'db_type' => 'TINYINT(1)'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isString', 'required' => true, 'default' => '1970-01-01 00:00:00', 'db_type' => 'DATETIME'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isString', 'required' => true, 'default' => '1970-01-01 00:00:00', 'db_type' => 'DATETIME'],
            'viewed' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'default' => '0', 'db_type' => 'INT(20) UNSIGNED'],
            'comments_allowed' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true, 'db_type' => 'TINYINT(1)'],
            'post_type' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'db_type' => 'VARCHAR(45)', 'size' => 45],
            'image' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false, 'db_type' => 'VARCHAR(255)'],
            'meta_title' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true, 'required' => false, 'db_type' => 'VARCHAR(255)'],
            'meta_keyword' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => false, 'db_type' => 'VARCHAR(255)'],
            'meta_description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => false, 'db_type' => 'VARCHAR(512)'],
            'short_description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => false, 'db_type' => 'VARCHAR(512)'],
            'content' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'required' => false, 'db_type' => 'TEXT'],
            'link_rewrite' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'db_type' => 'VARCHAR(255)'],
            'lang_active' => ['type' => self::TYPE_BOOL, 'lang' => true, 'validate' => 'isBool', 'required' => true, 'default' => '1', 'db_type' => 'TINYINT(1)'],
            'is_featured' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'default' => '0', 'db_type' => 'TINYINT(1)'],
        ],
    ];

    /**
     * Get raw BeesBlogPost by ID and Language ID
     *
     * @param int      $idBeesBlogPost BeesBlogPost ID
     * @param int|null $idLang         Language ID
     *
     * @return bool|array
     */
    public static function getRaw($idBeesBlogPost, $idLang = null)
    {
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }

        $idShop = (int) \Context::getContext()->shop->id;
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`'.self::PRIMARY.'` = '.(int) $idBeesBlogPost);
        $sql->where('sbp.`active` = 1');
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    /**
     * Get all BeesBlogPosts (raw)
     *
     * @param int|null $idLang Language ID
     * @param int|bool $offset Offset
     * @param int|bool $limit  Limit
     *
     * @return array
     *
     * @TODO: change parameter order
     */
    public static function getAllPosts($idLang = null, $offset = false, $limit = false)
    {
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        $idShop = (int) \Context::getContext()->shop->id;

        if (!$offset) {
            $offset = 0;
        }

        if (!$limit) {
            $limit = 5;
        }

        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
//        $sql->where('sbp.`active` = 1');
//        $sql->where('sbpl.`lang_active` = 1');

        // FIXME: check admin accounts
//        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
//            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
//        }

        $sql->orderBy('sbp.`'.self::PRIMARY.'` DESC');
//        $sql->limit((int) $limit, (int) $offset);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Get BeesBlogPost count
     *
     * @param int|null $idLang Language ID
     *
     * @return int BeesBlogPost count
     */
    public static function getPostCount($idLang = null)
    {
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        $idShop = (int) \Context::getContext()->shop->id;
        $sql = new \DbQuery();
        $sql->select('count(*)');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`active` = 1');
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }

        return (int) \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /**
     * Get BeesBlogPost count by BeesBlogCategory
     *
     * @param int|null $idLang             Language ID
     * @param int|null $idBeesBlogCategory BeesBlogCategory ID
     *
     * @return bool|int
     *
     * @TODO: change parameter order
     */
    public static function getPostCountByCategory($idLang = null, $idBeesBlogCategory = null)
    {
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        if ($idBeesBlogCategory == null) {
            $idBeesBlogCategory = 1;
        }
        $idShop = (int) \Context::getContext()->shop->id;
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`active` = 1');
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }
        $sql->where('sbp.`id_category` = '.(int) $idBeesBlogCategory);
        if (!$posts = \Db::getInstance()->executeS($sql)) {
            return false;
        }

        return count($posts);
    }

    /**
     * Add tags
     *
     * @param int      $idPost
     * @param array    $tagList
     * @param int|null $idLang
     * @param string   $separator
     *
     * @return bool
     */
    public static function addTags($idPost, $tagList, $idLang = null, $separator = ',')
    {
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        if (!\Validate::isUnsignedId($idLang)) {
            return false;
        }

        if (!is_array($tagList)) {
            $tagList = array_filter(array_unique(array_map('trim', preg_split('#\\'.$separator.'#', $tagList, null, PREG_SPLIT_NO_EMPTY))));
        }

        $list = [];
        if (is_array($tagList)) {
            foreach ($tagList as $tag) {
                $idTag = BeesBlogTag::tagExists($tag, (int) $idLang);
                if (!$idTag) {
                    $tagObj = new BeesBlogTag(null, $tag, (int) $idLang);
                    if (!\Validate::isLoadedObject($tagObj)) {
                        $tagObj->name = $tag;
                        $tagObj->id_lang = (int) $idLang;
                        $tagObj->add();
                    }
                    if (!in_array($tagObj->id, $list)) {
                        $list[] = $tagObj->id;
                    }
                } else {
                    if (!in_array($idTag, $list)) {
                        $list[] = $idTag;
                    }
                }

            }
        }
        $data = [];
        foreach ($list as $tag) {
            $data[] = [
                self::TABLE => (int) $tag,
                'id_post' => (int) $idPost,
            ];

        }

        return \Db::getInstance()->insert(self::TABLE, $data);
    }

    /**
     * @param bool $autodate
     * @param bool $nullValues
     *
     * @return bool
     */
    public function add($autodate = true, $nullValues = false)
    {
        if (!parent::add($autodate, $nullValues)) {
            return false;
        } else {
            if (\Tools::getIsset('products')) {
                return $this->setProducts(\Tools::getValue('products'));
            }
        }

        return true;
    }

    /**
     * Increment view count
     *
     * @param int $idBeesBlogPost BeesBlogPost ID
     *
     * @return bool Whether view count has been succesfully incremented
     */
    public static function viewed($idBeesBlogPost)
    {
        $sql = 'UPDATE '._DB_PREFIX_.'bees_blog_post as p SET p.viewed = (p.viewed+1) where p.id_bees_blog_post = '.(int) $idBeesBlogPost;

        return \Db::getInstance()->execute($sql);
    }

    /**
     * @param $array
     *
     * @return bool
     */
    public function setProducts($array)
    {
        return true;
//        $result = \Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'bees_blog_post_tag WHERE id_tag = '.(int) $this->id);
//        if (is_array($array)) {
//            $array = array_map('intval', $array);
//            $result &= \ObjectModel::updateMultishopTable(
//                'bees_blog_post_tag',
//                ['indexed' => 0],
//                'a.id_post IN ('.implode(',', $array).')'
//            );
//            $ids = [];
//            foreach ($array as $idPost) {
//                $ids[] = '('.(int) $idPost.','.(int) $this->id.')';
//            }
//
//            if ($result) {
//                $result &= \Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'bees_blog_post_tag (id_post, id_tag) VALUES '.implode(',', $ids));
//
//                if (\Configuration::get('PS_SEARCH_INDEXATION')) {
//                    $result &= \Search::indexation(false);
//                }
//            }
//        }
//
//        return $result;
    }

    /**
     * Get Tags
     *
     * @param $idBeesBlogPost
     *
     * @return array|bool|false|\mysqli_result|null|\PDOStatement|resource
     */
    public static function getTags($idBeesBlogPost)
    {
        return false;
//        $idLang = (int) \Context::getContext()->language->id;
//        $sql = new \DbQuery();
//        $sql->select('sbt.`name`');
//        $sql->from('bees_blog_tag', 'sbt');
//        $sql->leftJoin('bees_blog_post_tag', 'sbpt', 'sbt.`id_tag` = sbpt.`id_tag`');
//        $sql->where('sbt.`id_lang` = '.(int) $idLang);
//        $sql->where('sbpt.`id_post` = '.(int) $idBeesBlogPost);
//        if (!$tmp = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
//            return false;
//        }
//
//        return $tmp;
    }

    /**
     * Delete tags
     *
     * @param int $idBeesBlogPost
     *
     * @return bool
     */
    public static function deleteTags($idBeesBlogPost)
    {
//        return \Db::getInstance()->delete(
//            'bees_blog_post_tag',
//            '`id_post` = '.(int) $idBeesBlogPost
//        );
    }

    /**
     * Get tags with lang restriction
     *
     * @param int      $idBeesBlogPost BeesBlogPost ID
     * @param int|null $idLang         Language ID
     *
     * @return bool|string
     *
     * @TODO: merge with getTags
     */
    public static function getTagsByLang($idBeesBlogPost, $idLang = null)
    {
        return false;
//        if ($idLang == null) {
//            $idLang = (int) \Context::getContext()->language->id;
//        }
//        $tags = '';
//        $sql = new \DbQuery();
//        $sql->select('*');
//        $sql->from('bees_blog_tag', 'sbt');
//        $sql->leftJoin('bees_blog_post_tag', 'sbpt', 'sbt.`id_tag` = sbpt.`id_tag`');
//        $sql->where('sbt.`id_lang` = '.(int) $idLang);
//        $sql->where('sbpt.`id_post` = '.(int) $idBeesBlogPost);
//
//        if (!$tmp = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
//            return false;
//        }
//        $i = 1;
//        foreach ($tmp as $val) {
//            if ($i >= count($tmp)) {
//                $tags .= $val['name'];
//            } else {
//                $tags .= $val['name'].',';
//            }
//            $i++;
//        }
//
//        return $tags;
    }

    /**
     * Get popular BeesBlogPosts
     *
     * @param int|null $idLang Language ID
     *
     * @return array|false|\mysqli_result|null|\PDOStatement|resource
     */
    public static function getPopularPosts($idLang = null)
    {
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        $idShop = (int) \Context::getContext()->shop->id;
        if (\Configuration::get('beesshowpopularpost') != '' && \Configuration::get('beesshowpopularpost') != null) {
            $limit = \Configuration::get('beesshowpopularpost');
        } else {
            $limit = 5;
        }
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`active` = 1');
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }
        $sql->orderBy('sbp.`viewed`');
        $sql->limit((int) $limit);
        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;
    }

    /**
     * Get related posts
     *
     * @param int|null $idLang             Language ID
     * @param int|null $idBeesBlogCategory BeesBlogCategory ID
     * @param int|null $idBeesBlogPost     BeesBlogPost ID
     *
     * @return array|bool|false|\mysqli_result|null|\PDOStatement|resource
     */
    public static function getRelatedPosts($idLang = null, $idBeesBlogCategory = null, $idBeesBlogPost = null)
    {
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        $idShop = (int) \Context::getContext()->shop->id;
        if (\Configuration::get('beesshowrelatedpost') != '' && \Configuration::get('beesshowrelatedpost') != null) {
            $limit = \Configuration::get('beesshowrelatedpost');
        } else {
            $limit = 5;
        }
        if ($idBeesBlogCategory == null) {
            $idBeesBlogCategory = 1;
        }
        if ($idBeesBlogPost == null) {
            $idBeesBlogPost = 1;
        }
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`active` = 1');
        // TODO: detect admin
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }
        $sql->where('sbp.`id_category` = '.(int) $idBeesBlogCategory);
        $sql->where('sbp.`'.self::PRIMARY.'` != '.(int) $idBeesBlogPost);
        $sql->orderBy('sbp.`'.self::PRIMARY.'` DESC');
        $sql->limit((int) $limit);

        if (!$posts = \Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $posts;
    }

    /**
     * Get recent posts
     *
     * @param int|null $idLang
     *
     * @return array|false|\mysqli_result|null|\PDOStatement|resource
     */
    public static function getRecentPosts($idLang = null)
    {
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        $idShop = (int) \Context::getContext()->shop->id;
        // TODO: switch to CONSTANTS
        if (\Configuration::get('beesshowrecentpost') != '' && \Configuration::get('beesshowrecentpost') != null) {
            $limit = \Configuration::get('beesshowrecentpost');
        } else {
            $limit = 5;
        }

        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');

        // FIXME: detect admin users properly
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`active` = 1');
        $sql->limit((int) $limit);
        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;
    }

    /**
     * @param array    $tags
     * @param null|int $idLang
     *
     * @return array|bool
     */
    public static function tagsPost($tags, $idLang = null)
    {
        return false;
//        $result = [];
//        if ($idLang == null) {
//            $idLang = (int) \Context::getContext()->language->id;
//        }
//        $idShop = (int) \Context::getContext()->shop->id;
//
//        $sql = new \DbQuery();
//        $sql->select('*');
//        $sql->from(self::TABLE, 'sbp');
//        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
//        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
//        $sql->innerJoin('bees_blog_post_tag', 'sbpt', 'sbp.`'.self::PRIMARY.'` = sbpt.`'.self::PRIMARY.'`');
//        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
//        $sql->where('sbpl.`lang_active` = 1');
//
//        // FIXME: detect admin users properly
//        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
//            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
//        }
//        $sql->where('sbps.`id_shop` = '.(int) $idShop);
//        $sql->where('sbp.`active` = 1');
//        if (!$posts = \Db::getInstance()->executeS($sql)) {
//            return false;
//        }
//
//        $blogCategory = new BeesBlogCategory();
//        $i = 0;
//        foreach ($posts as $post) {
//            $result[$i]['id_post'] = $post['id_bees_blog_post'];
//            $result[$i]['viewed'] = $post['viewed'];
//            $result[$i]['is_featured'] = $post['is_featured'];
//            $result[$i]['meta_title'] = $post['meta_title'];
//            $result[$i]['short_description'] = $post['short_description'];
//            $result[$i]['meta_description'] = $post['meta_description'];
//            $result[$i]['content'] = $post['content'];
//            $result[$i]['meta_keyword'] = $post['meta_keyword'];
//            $result[$i]['id_category'] = $post['id_category'];
//            $result[$i]['link_rewrite'] = $post['link_rewrite'];
//            $result[$i]['cat_name'] = $blogCategory->getCatName($post['id_category']);
//            $result[$i]['cat_link_rewrite'] = $blogCategory->getCatLinkRewrite($post['id_category']);
//            $employee = new \Employee($post['id_employee']);
//
//            $result[$i]['lastname'] = $employee->lastname;
//            $result[$i]['firstname'] = $employee->firstname;
//            if (file_exists(_PS_MODULE_DIR_.'beesblog/images/'.$post['id_bees_blog_post'].'.jpg')) {
//                $image = $post['id_bees_blog_post'];
//                $result[$i]['post_img'] = $image;
//            } else {
//                $result[$i]['post_img'] = 'no';
//            }
//            $result[$i]['date_add'] = $post['date_add'];
//            $i++;
//        }
//
//        return $result;
    }

    /**
     * Get archive result
     *
     * @param int|null  $month  Month
     * @param int| null $year   Year
     * @param int       $offset Offset
     * @param int       $limit  Limit
     *
     * @return bool|array
     *
     * @TODO: change parameter order
     */
    public static function getArchiveResult($month = null, $year = null, $offset = 0, $limit = 5)
    {
        $result = [];
        $idLang = (int) \Context::getContext()->language->id;
        $idShop = (int) \Context::getContext()->shop->id;
        if ($month != '' and $month != null and $year != '' and $year != null) {
            $sql = new \DbQuery();
            $sql->select('*');
            $sql->from(self::TABLE, 'sbp');
            $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
            $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
            $sql->where('sbpl.`id_lang` = '.(int) $idLang);
            $sql->where('sbpl.`lang_active` = 1');

            // FIXME: detect admin users properly
            if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
                $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
            }
            $sql->where('sbps.`id_shop` = '.(int) $idShop);
            $sql->where('sbp.`active` = 1');
            $sql->where('MONTH(sbp.`date_add`) = '.(int) $month);
            $sql->where('YEAR(sbp.`date_add` = '.(int) $year);
            $sql->orderBy('sbp.`'.self::PRIMARY.'` DESC');
        } elseif ($month == '' && $month == null && $year != '' && $year != null) {
            $sql = new \DbQuery();
            $sql->select('*');
            $sql->from(self::TABLE, 'sbp');
            $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
            $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
            $sql->where('sbpl.`id_lang` = '.(int) $idLang);
            $sql->where('sbpl.`lang_active` = 1');
            if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
                $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
            }
            $sql->where('sbps.`id_shop` = '.(int) $idShop);
            $sql->where('sbp.`active` = 1');
            $sql->where('YEAR(sbp.`date_add`) = '.(int) $year);
            $sql->orderBy('sbp.`'.self::PRIMARY.'` DESC');
        } elseif ($month != '' and $month != null and $year == '' and $year == null) {
            $sql = new \DbQuery();
            $sql->select('*');
            $sql->from(self::TABLE, 'sbp');
            $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
            $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
            $sql->where('sbpl.`id_lang` = '.(int) $idLang);
            $sql->where('sbpl.`lang_active` = 1');
            if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
                $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
            }
            $sql->where('sbps.`id_shop` = '.(int) $idShop);
            $sql->where('sbp.`active` = 1');
            $sql->where('MONTH(sbp.`date_add`) = '.(int) $month);
            $sql->orderBy('sbp.`'.self::PRIMARY.'` DESC');
        } else {
            $sql = new \DbQuery();
            $sql->select('*');
            $sql->from(self::TABLE, 'sbp');
            $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
            $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
            $sql->where('sbpl.`id_lang` = '.(int) $idLang);
            $sql->where('sbpl.`lang_active` = 1');
            if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
                $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
            }
            $sql->where('sbps.`id_shop` = '.(int) $idShop);
            $sql->where('sbp.`active` = 1');
            $sql->orderBy('sbp.`'.self::PRIMARY.'` DESC');
        }
        if (!$posts = \Db::getInstance()->executeS($sql)) {
            return false;
        }

        $blogCategory = new BeesBlogCategory();
        $i = 0;
        foreach ($posts as $post) {
            $result[$i]['id_post'] = $post['id_bees_blog_post'];
            $result[$i]['viewed'] = $post['viewed'];
            $result[$i]['is_featured'] = $post['is_featured'];
            $result[$i]['meta_title'] = $post['meta_title'];
            $result[$i]['short_description'] = $post['short_description'];
            $result[$i]['meta_description'] = $post['meta_description'];
            $result[$i]['content'] = $post['content'];
            $result[$i]['meta_keyword'] = $post['meta_keyword'];
            $result[$i]['id_category'] = $post['id_category'];
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
            $result[$i]['cat_name'] = $blogCategory->getCatName($post['id_category']);
            $result[$i]['cat_link_rewrite'] = $blogCategory->getCatLinkRewrite($post['id_category']);
            $employee = new \Employee($post['id_employee']);

            $result[$i]['lastname'] = $employee->lastname;
            $result[$i]['firstname'] = $employee->firstname;
            if (file_exists(_PS_MODULE_DIR_.'beesblog/images/'.$post['id_bees_blog_post'].'.jpg')) {
                $image = $post['id_bees_blog_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $result[$i]['date_add'] = $post['date_add'];
            $i++;
        }

        return $result;
    }

    /**
     * @param int $month Month
     * @param int $year Year
     *
     * @return array|bool|false|\mysqli_result|null|\PDOStatement|resource
     */
    public static function getArchiveD($month, $year)
    {
        $sql = new \DbQuery();
        $sql->select('DAY(sbp.`date_add`) AS day');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('MONTH(sbp.`date_add`) = '.(int) $month);
        $sql->where('YEAR(sbp.`date_add`) = '.(int) $year);
        $sql->groupBy('sbp.`date_add`');
        if (!$posts = \Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $posts;

    }

    /**
     * @param int $year Year
     *
     * @return array|bool|false|\mysqli_result|null|\PDOStatement|resource
     */
    public static function getArchiveM($year)
    {
        $sql = new \DbQuery();
        $sql->select('MONTH(sbp.`date_add`) AS month');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('YEAR(sbp.`date_add`) = '.(int) $year);
        $sql->groupBy('MONTH(sbp.`date_add`)');
        if (!$posts = \Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $posts;

    }

    /**
     * @return array|bool
     */
    public static function getArchive()
    {
        $result = [];
        $sql = new \DbQuery();
        $sql->select('YEAR(sbp.`date_add`) AS year');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->groupBy('YEAR(sbp.`date_add`)');
        if (!$posts = \Db::getInstance()->executeS($sql)) {
            return false;
        }
        $i = 0;
        foreach ($posts as $value) {
            $result[$i]['year'] = $value['year'];
            $result[$i]['month'] = BeesBlogPost::getArchiveM($value['year']);
            $months = BeesBlogPost::getArchiveM($value['year']);
            $j = 0;
            foreach ($months as $month) {
                $result[$i]['month'][$j]['day'] = BeesBlogPost::getArchiveD($month['month'], $value['year']);
                $j++;
            }
            $i++;
        }

        return $result;
    }

    /**
     * @param string|null $keyword Keyword
     * @param int|null    $idLang  Language ID
     * @param int         $offset  Offset
     * @param int         $limit   Limit
     *
     * @return array|bool
     */
    public static function beesBlogSearchPost($keyword = null, $idLang = null, $offset = 0, $limit = 5)
    {
        $result = [];
        if ($keyword == null) {
            return false;
        }
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        $idShop = (int) \Context::getContext()->shop->id;

        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`active` = 1');

        // FIXME: detect admin users properly
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }
        $sql->where(
            'sbpl.`meta_title` LIKE \'%'.pSQL($keyword).'\'
             OR sbpl.`meta_keyword` LIKE \'%'.pSQL($keyword).'\'
             OR sbpl.`meta_description` LIKE \'%'.pSQL($keyword).'\'
             OR sbpl.`meta_content` LIKE \'%'.pSQL($keyword).'\''
        );
        $sql->orderBy('sbp.`'.self::PRIMARY.'` DESC');
        $sql->limit($limit, $offset);
        if (!$posts = \Db::getInstance()->executeS($sql)) {
            return false;
        }

        $blogCategory = new BeesBlogCategory();
        $i = 0;
        foreach ($posts as $post) {
            $result[$i]['id_post'] = $post['id_bees_blog_post'];
            $result[$i]['viewed'] = $post['viewed'];
            $result[$i]['is_featured'] = $post['is_featured'];
            $result[$i]['meta_title'] = $post['meta_title'];
            $result[$i]['short_description'] = $post['short_description'];
            $result[$i]['meta_description'] = $post['meta_description'];
            $result[$i]['content'] = $post['content'];
            $result[$i]['meta_keyword'] = $post['meta_keyword'];
            $result[$i]['id_category'] = $post['id_category'];
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
            $result[$i]['cat_name'] = $blogCategory->getCatName($post['id_category']);
            $result[$i]['cat_link_rewrite'] = $blogCategory->getCatLinkRewrite($post['id_category']);
            $employee = new \Employee($post['id_employee']);

            $result[$i]['lastname'] = $employee->lastname;
            $result[$i]['firstname'] = $employee->firstname;
            if (file_exists(_PS_MODULE_DIR_.'beesblog/images/'.$post['id_bees_blog_post'].'.jpg')) {
                $image = $post['id_bees_blog_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $result[$i]['date_add'] = $post['date_add'];
            $i++;
        }

        return $result;
    }

    /**
     * @param string|null $keyword Keyword
     * @param int|null    $idLang  Langauge ID
     *
     * @return bool|int
     */
    public static function beesBlogSearchPostCount($keyword = null, $idLang = null)
    {
        if ($keyword == null) {
            return false;
        }
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        $idShop = (int) \Context::getContext()->shop->id;

        // Use same query as BeesBlogSearchPost as this query will be repeated and cached
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`active` = 1');
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }
        $sql->where(
            'sbpl.`meta_title` LIKE \'%'.pSQL($keyword).'\'
                     OR sbpl.`meta_keyword` LIKE \'%'.pSQL($keyword).'\'
                     OR sbpl.`meta_description` LIKE \'%'.pSQL($keyword).'\'
                     OR sbpl.`meta_content` LIKE \'%'.pSQL($keyword).'\''
        );
        $sql->orderBy('sbp.`'.self::PRIMARY.'` DESC');
        if (!$posts = \Db::getInstance()->executeS($sql)) {
            return false;
        }

        return count($posts);
    }

    /**
     * Get blog image
     *
     * @return false|null|string
     */
    public static function getBlogImage()
    {
        $sql = new \DbQuery();
        $sql->select('`'.self::PRIMARY.'`');
        $sql->from(self::TABLE);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /**
     * Get BeesBlogPost rewrite by ID
     *
     * @param int|null $idPost BeesBlogPost ID
     * @param int|null $idLang Language ID
     *
     * @return false|null|string
     */
    public static function getPostRewriteById($idPost, $idLang = null)
    {
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        $idShop = (int) \Context::getContext()->shop->id;

        $sql = new \DbQuery();
        $sql->select('sbp.`link_rewrite`');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`active` = 1');
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }
        $sql->where('sbp.`'.self::PRIMARY.'` = '.(int) $idPost);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /**
     * Get meta data
     *
     * @param int      $idPost BeesBlogPost ID
     * @param int|null $idLang Language ID
     *
     * @return bool
     */
    public static function getMeta($idPost, $idLang = null)
    {
        if ($idLang == null) {
            $idLang = (int) \Context::getContext()->language->id;
        }

        $sql = new \DbQuery();
        $sql->select('sbpl.`meta_title`, sbpl.`meta_description`, sbpl.`meta_keyword`');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbp.`'.self::PRIMARY.'` = '.(int) $idPost);
        $sql->where('sbp.`active` = 1');

        // FIXME: detect admin users properly
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }
        if (!$post = \Db::getInstance()->getRow($sql)) {
            return false;
        }

        if (empty($post['meta_title'])) {
            $meta['meta_title'] = \Configuration::get('beesblogmetatitle');
        } else {
            $meta['meta_title'] = $post['meta_title'];
        }

        if (empty($post['meta_description'])) {
            $meta['meta_description'] = \Configuration::get('beesblogmetadescrip');
        } else {
            $meta['meta_description'] = $post['meta_description'];
        }

        if (empty($post['meta_keyword'])) {
            $meta['meta_keywords'] = \Configuration::get('beesblogmetakeyword');
        } else {
            $meta['meta_keywords'] = $post['meta_keyword'];
        }

        return $meta;
    }

    /**
     * @param int $limit
     *
     * @return array|bool
     */
    public static function getLatestPostHome($limit)
    {
        if ($limit == '' && $limit == null) {
            $limit = 3;
        }
        $idLang = (int) \Context::getContext()->language->id;
        $idShop = (int) \Context::getContext()->shop->id;
        $result = [];
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`active` = 1');

        // FIXME: detect admin users properly
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }
        $sql->limit((int) $limit);
        $posts = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (empty($posts)) {
            return false;
        }
        $i = 0;
        foreach ($posts as $post) {
            $result[$i]['id'] = $post['id_bees_blog_post'];
            $result[$i]['title'] = $post['meta_title'];
            $result[$i]['meta_description'] = strip_tags($post['meta_description']);
            $result[$i]['short_description'] = strip_tags($post['short_description']);
            $result[$i]['content'] = strip_tags($post['content']);
            $result[$i]['category'] = $post['id_category'];
            $result[$i]['date_added'] = $post['date_add'];
            $result[$i]['viewed'] = $post['viewed'];
            $result[$i]['is_featured'] = $post['is_featured'];
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
            if (file_exists(_PS_MODULE_DIR_.'beesblog/images/'.$post['id_bees_blog_post'].'.jpg')) {
                $image = $post['id_bees_blog_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $i++;
        }

        return $result;
    }

    /**
     * Get BeesBlogPost ID by rewrite
     *
     * @param string   $rewrite Rewrite
     * @param bool     $active  Active
     * @param int|null $idLang  Language ID
     * @param int|null $idShop  Shop ID
     *
     * @return bool|false|null|string
     */
    public static function getIdByRewrite($rewrite, $active = true, $idLang = null, $idShop = null)
    {
        if (empty($rewrite)) {
            return false;
        }
        if (empty($idLang)) {
            $idLang = (int) \Context::getContext()->language->id;
        }
        if (empty($idShop)) {
            $idShop = (int) \Context::getContext()->shop->id;
        }
        $sql = new \DbQuery();
        $sql->select('sbp.`'.self::PRIMARY.'`');
        $sql->from(self::TABLE, 'sbp');
        $sql->innerJoin(self::LANG_TABLE, 'sbpl', 'sbp.`'.self::PRIMARY.'` = sbpl.`'.self::PRIMARY.'`');
        $sql->innerJoin(self::SHOP_TABLE, 'sbps', 'sbp.`'.self::PRIMARY.'` = sbps.`'.self::PRIMARY.'`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbp.`active` = '.(int) $active);
        if (\Context::getContext()->customer->email !== 'info@thirtybees.com') {
            $sql->where('sbp.`date_add` < \''.date('Y-m-d H:i:s').'\'');
        }
        $sql->where('sbpl.`link_rewrite` = \''.pSQL($rewrite).'\'');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /**
     * @param int $idBeesBlogPost BeesBlogPost ID
     * @param int $idLang         Language ID
     *
     * @return false|null|string
     */
    public static function getLangActive($idBeesBlogPost, $idLang)
    {
        $sql = new \DbQuery();
        $sql->select('sbpl.`lang_active`');
        $sql->from(self::LANG_TABLE, 'sbpl');
        $sql->where('sbpl.`'.self::PRIMARY.'` = '.(int) $idBeesBlogPost);
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
}
