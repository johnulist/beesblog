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

class BeesBlogCategory extends BeesBlogObjectModel
{
    public $id_bees_blog_category;
    public $id_parent;
    public $position;
    public $desc_limit;
    public $active = 1;
    public $date_add;
    public $date_upd;
    public $meta_title;
    public $meta_keyword;
    public $meta_description;
    public $description;
    public $link_rewrite;

    public static $definition = array(
        'table' => 'bees_blog_category',
        'primary' => 'id_bees_blog_category',
        'multilang_shop' => true,
        'fields' => array(
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'default' => '0', 'db_type' => 'INT(11) UNSIGNED'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'default' => '1', 'db_type' => 'INT(11) UNSIGNED'),
            'desc_limit' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'default' => '160', 'db_type' => 'INT(11) UNSIGNED'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true, 'default' => '1', 'db_type' => 'TINYINT(1)'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isString', 'required' => true, 'default' => '1970-01-01 00:00:00', 'db_type' => 'DATETIME'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isString', 'required' => true, 'default' => '1970-01-01 00:00:00', 'db_type' => 'DATETIME'),
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'db_type' => 'VARCHAR(255)'),
            'meta_keyword' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => false, 'db_type' => 'VARCHAR(255)'),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => false, 'db_type' => 'VARCHAR(512)'),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isHtml', 'required' => true, 'db_type' => 'TEXT'),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'db_type' => 'VARCHAR(256)'),
        ),
    );

    /**
     * @param int|null $idLang Language ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getRootCategory($idLang = null)
    {
        if ($idLang == null) {
            $idLang = (int) Context::getContext()->language->id;
        }
        $idShop = (int) Context::getContext()->shop->id;

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('bees_blog_category', 'sbc');
        $sql->innerJoin(
            'bees_blog_category_lang',
            'sbcl',
            'sbc.`id_bees_blog_category` = sbcl.`id_bees_blog_category`'
        );
        $sql->innerJoin(
            'bees_blog_category_shop',
            'sbcs',
            'sbc.`id_bees_blog_category` = sbcs.`id_bees_blog_category`'
        );
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        $sql->where('sbcs.`id_shop` = '.(int) $idShop);
        $sql->where('sbc.`active` = 1');
        $sql->where('sbc.`id_parent` = 0');
        $rootCategory = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $rootCategory;
    }

    /**
     * @param int $id BeesBlogCategory ID
     *
     * @return array|bool|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getNameCategory($id)
    {
        $idLang = (int) Context::getContext()->language->id;
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('bees_blog_category_lang', 'sbcl');
        $sql->innerJoin('bees_blog_category', 'sbc', 'sbc.`id_bees_blog_category` = sbcl.`id_bees_blog_category`');
        $sql->where('sbc.`id_bees_blog_category` = '.(int) $id);
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    /**
     * @param int $id BeesBlogCategory ID
     *
     * @return bool
     */
    public static function getCatName($id)
    {
        $idLang = (int) Context::getContext()->language->id;
        $sql = new DbQuery();
        $sql->select('sbcl.`meta_title`');
        $sql->from('bees_blog_category_lang', 'sbcl');
        $sql->innerJoin('bees_blog_category', 'sbc', 'sbc.`id_bees_blog_category` = sbcl.`id_bees_blog_category`');
        $sql->where('sbc.`id_bees_blog_category` = '.(int) $id);
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result[0]['meta_title'];
    }

    /**
     * @param int $id BeesBlogCategory ID
     *
     * @return bool
     */
    public static function getCatLinkRewrite($id)
    {
        $idLang = (int) Context::getContext()->language->id;
        $sql = new DbQuery();
        $sql->select('sbcl.`link_rewrite`');
        $sql->from('bees_blog_category_lang', 'sbcl');
        $sql->innerJoin('bees_blog_category', 'sbc', 'sbc.`id_bees_blog_category` = sbcl.`id_bees_blog_category`');
        $sql->where('sbc.`id_bees_blog_category` = '.(int) $id);
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result[0]['link_rewrite'];
    }

    /**
     * @return array|bool|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getCatImage()
    {
        $sql = new DbQuery();
        $sql->select('`id_bees_blog_category`');
        $sql->from('bees_blog_category');
        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    /**
     * Get BeesBlogCategory
     *
     * @param int      $active Active
     * @param int|null $idLang Language ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getCategory($active = 1, $idLang = null)
    {
        if (empty($idLang)) {
            $idLang = (int) Context::getContext()->language->id;
        }
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('bees_blog_category', 'sbc');
        $sql->innerJoin(
            'bees_blog_category_lang',
            'sbcl',
            'sbc.`id_bees_blog_category` = sbcl.`id_bees_blog_category`'
        );
        $sql->innerJoin(
            'bees_blog_category_shop',
            'sbcs',
            'sbc.`id_bees_blog_category` = sbcs.`id_bees_blog_category`'
        );
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        $sql->where('sbcs.`id_shop` = '.(int) Context::getContext()->shop->id);
        $sql->where('sbc.`active` = '.(int) $active);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;
    }

    /**
     * Get BeesBlogCategory name by BeesBlogPost ID
     *
     * @param int $idPost BeesBlogPost ID
     *
     * @return false|null|string
     */
    public static function getCategoryNameByPost($idPost)
    {
        $sql = new DbQuery();
        $sql->select('sbp.`id_category`');
        $sql->from('bees_blog_post', 'sbp');
        $sql->where('sbp.`id_bees_blog_post` = '.(int) $idPost);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /**
     * Get BeesBlogPost count in BeesBlogCategory
     *
     * @param int $idBeesBlogCategory BeesBlogCategory ID
     *
     * @return bool
     */
    public static function getPostCountInCategory($idBeesBlogCategory)
    {
        $sql = new DbQuery();
        $sql->select('count(`id_bees_blog_post` as count');
        $sql->from('bees_blog_post');
        $sql->where('`id_category` = '.(int) $idBeesBlogCategory);
        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result[0]['count'];
    }

    /**
     * Get category meta data
     *
     * @param int      $idBeesBlogCategory BeesBlogCategory ID
     * @param int|null $idLang             Language ID
     *
     * @return mixed
     */
    public static function getCategoryMeta($idBeesBlogCategory, $idLang = null)
    {
        if ($idLang == null) {
            $idLang = (int) Context::getContext()->language->id;
        }
        $idShop = (int) Context::getContext()->shop->id;
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('bees_blog_category', 'sbc');
        $sql->innerJoin(
            'bees_blog_category_lang',
            'smbcl',
            'sbc.`id_bees_blog_category` = sbcl.`id_bees_blog_category`'
        );
        $sql->innerJoin(
            'bees_blog_category_shop',
            'sbcs',
            'sbc.`id_bees_blog_category` = sbcs.`id_bees_blog_category`'
        );
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        $sql->where('sbcs.`id_shop` = '.(int) $idShop);
        $sql->where('sbc.`active` = 1');
        $sql->where('sbc.`id_bees_blog_category` = '.(int) $idBeesBlogCategory);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($result[0]['meta_title'] == '' && $result[0]['meta_title'] == null) {
            $meta['meta_title'] = Configuration::get('beesblogmetatitle');
        } else {
            $meta['meta_title'] = $result[0]['meta_title'];
        }

        if ($result[0]['meta_description'] == '' && $result[0]['meta_description'] == null) {
            $meta['meta_description'] = Configuration::get('beesblogmetadescrip');
        } else {
            $meta['meta_description'] = $result[0]['meta_description'];
        }

        if ($result[0]['meta_keyword'] == '' && $result[0]['meta_keyword'] == null) {
            $meta['meta_keywords'] = Configuration::get('beesblogmetakeyword');
        } else {
            $meta['meta_keywords'] = $result[0]['meta_keyword'];
        }

        return $meta;
    }

    /**
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
            $idLang = (int) Context::getContext()->language->id;
        }
        if (empty($idShop)) {
            $idShop = (int) Context::getContext()->shop->id;
        }
        $sql = new DbQuery();
        $sql->select('sbc.`id_bees_blog_category`');
        $sql->from('bees_blog_category', 'sbc');
        $sql->innerJoin('bees_blog_category_lang', 'sbcl', 'sbc.`id_bees_blog_category` = sbcl.`id_bees_blog_category`');
        $sql->innerJoin('bees_blog_category_shop', 'sbcs', 'sbc.`id_bees_blog_category` = sbcs.`id_bees_blog_category`');
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        $sql->where('sbcs.`id_shop` = '.(int) $idShop);
        $sql->where('sbc.`active` = '.(int) $active);
        $sql->where('sbcl.`link_rewrite` = \''.pSQL($rewrite).'\'');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
}
