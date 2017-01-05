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

define('_MODULE_BEESBLOG_DIR_', _PS_MODULE_DIR_.'beesblog/images/');
require_once(_PS_MODULE_DIR_.'beesblog/classes/autoload.php');

/**
 * Class BeesBlog
 */
class BeesBlog extends Module
{
    protected $coolShopId;
    protected $secureKey;

    protected $fieldsForm;

    const POSTS_PER_PAGE = 'BEESBLOG_POSTS_PER_PAGE';
    const SHOW_AUTHOR_STYLE = 'BEESBLOG_SHOW_AUTHOR_STYLE';
    const MAIN_SLUG = 'BEESBLOG_MAIN_SLUG';
    const USE_HTML = 'BEESBLOG_USE_HTML';
    const ENABLE_COMMENT = 'BEESBLOG_ENABLE_COMMENT';
    const SHOW_AUTHOR = 'BEESBLOG_SHOW_AUTHOR';
    const SHOW_VIEWED = 'BEESBLOG_SHOW_VIEWED';
    const SHOW_NO_IMAGE = 'BEESBLOG_SHOW_NO_IMAGE';
    const SHOW_COLUMN = 'BEESBLOG_SHOW_COLUMN';
    const CUSTOM_CSS = 'BEESBLOG_CUSTOM_CSS';
    const DISABLE_CATEGORY_IMAGE = 'BEESBLOG_DISABLE_CATEGORY_IMAGE';
    const META_TITLE = 'BEESBLOG_META_TITLE';
    const META_KEYWORDS = 'BEESBLOG_META_KEYWORDS';
    const META_DESCRIPTION = 'BEESBLOG_META_DESCRIPTION';
    const DISQUS_USERNAME = 'BEESBLOG_DISQUS_USERNAME';

    /**
     * BeesBlog constructor.
     */
    public function __construct()
    {
        $this->name = 'beesblog';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Thirty Bees';

        $this->controllers = array('archive', 'category', 'details', 'search', 'tagpost');
        $this->secureKey = Tools::encrypt($this->name);
        $this->coolShopId = Context::getContext()->shop->id;
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('Bees Blog');
        $this->description = $this->l('Thirty Bees blog module');
    }

    /**
     * Install this module
     *
     * @return bool Whether the module has been successfully installed
     */
    public function install()
    {
        Configuration::updateGlobalValue(self::POSTS_PER_PAGE, '5');
        Configuration::updateGlobalValue(self::SHOW_AUTHOR, '1');
        Configuration::updateGlobalValue(self::SHOW_AUTHOR_STYLE, '1');
        Configuration::updateGlobalValue(self::MAIN_SLUG, 'beesblog');
        Configuration::updateGlobalValue(self::USE_HTML, '1');
        Configuration::updateGlobalValue(self::SHOW_VIEWED, '1');

        Configuration::updateGlobalValue(self::SHOW_NO_IMAGE, '1');
        Configuration::updateGlobalValue(self::SHOW_COLUMN, '3');
        Configuration::updateGlobalValue(self::CUSTOM_CSS, '');
        Configuration::updateGlobalValue(self::DISABLE_CATEGORY_IMAGE, '1');
        Configuration::updateGlobalValue(self::META_TITLE, 'Cool! Blog Title');
        Configuration::updateGlobalValue(self::META_KEYWORDS, 'cool,blog,beesblog,prestashop blog,prestashop,blog');
        Configuration::updateGlobalValue(self::META_DESCRIPTION, 'The coolest blog for PrestaShop, provided for free by Thirty Bees');

        if (!parent::install() || !$this->registerHook('displayHeader') || !$this->insertBlogHooks()
            || !$this->registerHook('moduleRoutes') || !$this->registerHook('displayBackOfficeHeader')) {
            return false;
        }

        if (!(BeesBlogPost::createDatabase()
            && BeesBlogCategory::createDatabase()
            && BeesBlogPostCategory::createDatabase()
            && BeesBlogImageType::createDatabase()
            && BeesBlogTag::createDatabase())) {
            return false;
        }

        $this->createBeesBlogTabs();

        return true;
    }

    /**
     * @param $params
     *
     * @return string
     */
    public function hookdisplayBackOfficeHeader($params)
    {
        $this->context->smarty->assign(array(
            'coolmodules_dir' => __PS_BASE_URI__,
        ));
    }

    /**
     * @return bool
     */
    public function insertBlogHooks()
    {
        // FIXME: describe the remaining hooks
        $hookValues = array(
            array(
                'name' => 'displayBeesBlogLeft',
                'title' => 'displayBeesBlogLeft',
                'description' => 'Display in the left column on the Cool! Blog',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'displayBeesBlogRight',
                'title' => 'displayBeesBlogRight',
                'description' => 'Display in the right column on the Cool! Blog',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'displayCoolBeforePost',
                'title' => 'displayCoolBeforePost',
                'description' => 'Display before a blog post on the Cool! Blog',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'displayCoolAfterPost',
                'title' => 'displayCoolAfterPost',
                'description' => 'Display after a blog post on the Cool! Blog',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionBeesBlogPostAdd',
                'title' => 'actionBeesBlogPostAdd',
                'description' => 'Called after a new Cool! Blog post has been added',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionBeesBlogPostUpdate',
                'title' => 'actionBeesBlogPostUpdate',
                'description' => 'Called after a Cool! Blog post has been updated',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionBeesBlogPostDelete',
                'title' => 'actionBeesBlogDelete',
                'description' => 'Called after a Cool! Blog post has been deleted',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionBeesBlogPostToggle',
                'title' => 'actionBeesBlogPostToggle',
                'description' => 'Called after the visibility of a Cool! Blog post has been toggled',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionBeesBlogPostCategoryAdd',
                'title' => 'actionBeesBlogPostCategoryAdd',
                'description' => 'Called after a new Cool! Blog post category has been added',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionBeesBlogPostCategoryUpdate',
                'title' => 'actionBeesBlogPostCategoryUpdate',
                'description' => 'Called after a new Cool! Blog post category has been updated',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionBeesBlogPostCategoryDelete',
                'title' => 'actionBeesBlogPostCategoryDelete',
                'description' => 'Called after a new Cool! Blog post category has been deleted',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionBeesBlogPostCategoryToggle',
                'title' => 'actionBeesBlogPostCategoryToggle',
                'description' => 'Called after the visibility of a Cool! Blog post category has been toggled',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbsingle',
                'title' => 'actionsbsingle',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbcat',
                'title' => 'actionsbcat',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbsearch',
                'title' => 'actionsbsearch',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbheader',
                'title' => 'actionsbheader',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
        );

        foreach ($hookValues as $hookValue) {
            $hookId = Hook::getIdByName($hookValue['name']);
            if (!$hookId) {
                $addHook = new Hook();
                $addHook->name = pSQL($hookValue['name']);
                $addHook->title = pSQL($hookValue['title']);
                $addHook->description = pSQL($hookValue['description']);
                $addHook->position = pSQL($hookValue['position']);
                $addHook->live_edit = $hookValue['live_edit'];
                $addHook->add();
                $hookId = $addHook->id;
                if (!$hookId) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Uninstall this module
     *
     * @return bool Whether the module has been successfully uninstalled
     */
    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName(self::META_TITLE) ||
            !Configuration::deleteByName(self::META_KEYWORDS) ||
            !Configuration::deleteByName(self::META_DESCRIPTION) ||
            !Configuration::deleteByName(self::POSTS_PER_PAGE) ||
            !Configuration::deleteByName(self::USE_HTML) ||
            !Configuration::deleteByName(self::SHOW_VIEWED) ||
            !Configuration::deleteByName(self::DISABLE_CATEGORY_IMAGE) ||
            !Configuration::deleteByName(self::MAIN_SLUG) ||
            !Configuration::deleteByName(self::SHOW_COLUMN) ||
            !Configuration::deleteByName(self::SHOW_AUTHOR_STYLE) ||
            !Configuration::deleteByName(self::CUSTOM_CSS) ||
            !Configuration::deleteByName(self::SHOW_NO_IMAGE) ||
            !Configuration::deleteByName(self::SHOW_AUTHOR)) {
            return false;
        }

        $idtabs = array(
            Tab::getIdFromClassName('AdminBeesBlog'),
            Tab::getIdFromClassName('AdminBlogCategory'),
            Tab::getIdFromClassName('AdminBlogcomment'),
            Tab::getIdFromClassName('AdminBlogPost'),
            Tab::getIdFromClassName('AdminImageType'),
            Tab::getIdFromClassName('AdminAboutUs'),
        );
        foreach ($idtabs as $tabid) {
            if ($tabid) {
                $tab = new Tab($tabid);
                $tab->delete();
            }
        }

        if (!(BeesBlogPost::dropDatabase()
            && BeesBlogCategory::dropDatabase()
            && BeesBlogPostCategory::dropDatabase()
            && BeesBlogImageType::dropDatabase()
            && BeesBlogTag::dropDatabase())) {
            return false;
        }

        $this->coolHookDelete();

        return true;
    }

    public function coolHookDelete()
    {
        // TODO: update hook names
        $hookvalue = array(
            array(
                'name' => 'displayBeesBlogLeft',
                'title' => 'displayBeesBlogLeft',
                'description' => 'This is blog page left column',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'displayBeesBlogRight',
                'title' => 'displayBeesBlogRight',
                'description' => 'This is blog page Right column',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'displayCoolBeforePost',
                'title' => 'displayCoolBeforePost',
                'description' => 'This is blog Single page before blog post',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'displayCoolAfterPost',
                'title' => 'displayCoolAfterPost',
                'description' => 'This is blog Single page after blog post',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbnewpost',
                'title' => 'actionsbnewpost',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbupdatepost',
                'title' => 'actionsbupdatepost',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbdeletepost',
                'title' => 'actionsbdeletepost',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbtogglepost',
                'title' => 'actionsbtogglepost',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbnewcat',
                'title' => 'actionsbnewcat',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbupdatecat',
                'title' => 'actionsbupdatecat',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbdeletecat',
                'title' => 'actionsbdeletecat',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbtogglecat',
                'title' => 'actionsbtogglecat',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbpostcomment',
                'title' => 'actionsbpostcomment',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbappcomment',
                'title' => 'actionsbappcomment',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbsingle',
                'title' => 'actionsbsingle',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbcat',
                'title' => 'actionsbcat',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbsearch',
                'title' => 'actionsbsearch',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
            array(
                'name' => 'actionsbheader',
                'title' => 'actionsbheader',
                'description' => 'this is action hook.',
                'position' => 1,
                'live_edit' => 0,
            ),
        );

        foreach ($hookvalue as $hkv) {
            $hookid = Hook::getIdByName($hkv['name']);
            if ($hookid) {
                $dlt_hook = new Hook($hookid);
                $dlt_hook->delete();
            }
        }
    }

    /**
     * Register the module routes
     *
     * @param array $params Parameters
     * @return array Array with routes
     */
    public function hookModuleRoutes($params)
    {
        $alias = Configuration::get('coolmainblogurl');
        $usehtml = (int)Configuration::get('coolusehtml');
        if ($usehtml != 0) {
            $html = '.html';
        } else {
            $html = '';
        }
        return array(
            'beesblog' => array(
                'controller' => 'category',
                'rule' => $alias.$html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_list' => array(
                'controller' => 'category',
                'rule' => $alias.'/cat'.$html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_list_module' => array(
                'controller' => 'category',
                'rule' => 'module/'.$alias.'/category'.$html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_list_pagination' => array(
                'controller' => 'category',
                'rule' => $alias.'/cat/page/{page}'.$html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_pagination' => array(
                'controller' => 'category',
                'rule' => $alias.'/page/{page}'.$html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_category' => array(
                'controller' => 'category',
                'rule' => $alias.'/cat/{cat_rewrite}'.$html,
                'keywords' => array(
                    'cat_rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'cat_rewrite'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_category_pagination' => array(
                'controller' => 'category',
                'rule' => $alias.'/cat/{cat_rewrite}/page/{page}'.$html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                    'cat_rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'cat_rewrite'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_cat_page_mod' => array(
                'controller' => 'category',
                'rule' => 'module/'.$alias.'/cat/{blog_rewrite}/page/{page}'.$html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                    'blog_rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_post' => array(
                'controller' => 'details',
                'rule' => $alias.'/{blog_rewrite}'.$html,
                'keywords' => array(
                    'blog_rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]+', 'param' => 'blog_rewrite'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_tag' => array(
                'controller' => 'tagpost',
                'rule' => $alias.'/tag/{tag}'.$html,
                'keywords' => array(
                    'tag' => array('regexp' => '[_a-zA-Z0-9-\pL\+\s\-]*', 'param' => 'tag'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_search_pagination' => array(
                'controller' => 'search',
                'rule' => $alias.'/search/page/{page}'.$html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_archive' => array(
                'controller' => 'archive',
                'rule' => $alias.'/archive'.$html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_archive_pagination' => array(
                'controller' => 'archive',
                'rule' => $alias.'/archive/page/{page}'.$html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_month' => array(
                'controller' => 'archive',
                'rule' => $alias.'/archive/{year}/{month}'.$html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_month_pagination' => array(
                'controller' => 'archive',
                'rule' => $alias.'/archive/{year}/{month}/page/{page}'.$html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_year' => array(
                'controller' => 'archive',
                'rule' => $alias.'/archive/{year}'.$html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
            'beesblog_year_pagination' => array(
                'controller' => 'archive',
                'rule' => $alias.'/archive/{year}/page/{page}'.$html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'beesblog',
                ),
            ),
        );
    }

    /**
     * Add links to Google Sitemap
     * Hook provided by gsitemap module
     *
     * @param array $params Hook parameters
     *
     * @return array Sitemap links
     */
    public function hookGSitemapAppendUrls($params)
    {
        // Blog posts
        $idLang = (int) $params['lang']['id_lang'];
        $idShop = (int) Context::getContext()->shop->id;
        $sql = new DbQuery();
        $sql->select('sbp.`id_bees_blog_post`, sbpl.`link_rewrite`, sbp.`modified`');
        $sql->from('bees_blog_post', 'sbp');
        $sql->innerJoin('bees_blog_post_lang', 'sbpl', 'sbp.`id_bees_blog_post` = sbpl.`id_bees_blog_post`');
        $sql->innerJoin('bees_blog_post_shop', 'sbps', 'sbp.`id_bees_blog_post` = sbps.`id_bees_blog_post`');
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->where('sbps.`id_shop` = '.(int) $idShop);
        $sql->where('sbpl.`lang_active` = 1');
        $sql->where('sbp.`active` = 1');
        // FIXME: detect admin user
        // Unpublished articles should not go into the sitemap!
        if (!Context::getContext()->customer->email !== 'info@mijnpresta.nl') {
            $sql->where('sbp.`created` < \''.date('Y-m-d H:i:s').'\'');
        }

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $links = array();
        if (!empty($results)) {
            foreach ($results as $result) {
                $link = array();
                $link['link'] = BeesBlog::getBeesBlogLink('beesblog_post', array('blog_rewrite' => $result['link_rewrite']));
                $link['lastmod'] = $result['modified'];
                $link['type'] = 'module';

                if (file_exists(_PS_MODULE_DIR_.'beesblog/images/'.(int) $result['id_bees_blog_post'].'.jpg')) {
                    $link['image'] = array('link' => Tools::getHttpHost(true).'/modules/beesblog/images/'.(int) $result['id_bees_blog_post'].'.jpg');
                } elseif (file_exists(_PS_MODULE_DIR_.'beesblog/images/'.(int) $result['id_bees_blog_post'].'.png')) {
                    $link['image'] = array('link' => Tools::getHttpHost(true).'/modules/beesblog/images/'.(int) $result['id_bees_blog_post'].'.png');
                }

                $links[] = $link;
            }
        }

        // Categories
        $sql = new DbQuery();
        $sql->select('sbc.`id_bees_blog_category`, sbcl.`link_rewrite`, sbc.`modified`');
        $sql->from('bees_blog_category', 'sbc');
        $sql->innerJoin('bees_blog_category_lang', 'sbcl', 'sbc.`id_bees_blog_category` = sbcl.`id_bees_blog_category`');
        $sql->innerJoin('bees_blog_category_shop', 'sbcs', 'sbc.`id_bees_blog_category` = sbcs.`id_bees_blog_category`');
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        $sql->where('sbcs.`id_shop` = '.(int) $idShop);
        $sql->where('sbc.`active` = 1');

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!empty($results)) {
            foreach ($results as $result) {
                $link = array();
                $link['link'] = BeesBlog::getBeesBlogLink('beesblog_category', array('cat_rewrite' => $result['link_rewrite']));
                $link['lastmod'] = $result['modified'];
                $link['type'] = 'module';

                if (file_exists(_PS_MODULE_DIR_.'beesblog/images/category/'.(int) $result['id_bees_blog_category'].'.jpg')) {
                    $link['image'] = array('link' => Tools::getHttpHost(true).'/modules/beesblog/images/category/'.(int) $result['id_bees_blog_category'].'.jpg');
                } elseif (file_exists(_PS_MODULE_DIR_.'beesblog/images/category/'.(int) $result['id_bees_blog_category'].'.png')) {
                    $link['image'] = array('link' => Tools::getHttpHost(true).'/modules/beesblog/images/category/'.(int) $result['id_bees_blog_category'].'.png');
                }

                $links[] = $link;
            }
        }

        return $links;
    }

    /**
     * Hook display header
     *
     * @param array $params Hook parameters
     */
    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path.'css/beesblogstyle.css', 'all');
    }

    /**
     * Create Cool! Blog tabs
     *
     * @return bool Whether the tabs have been successfully added
     */
    private function createBeesBlogTabs()
    {
        $langs = Language::getLanguages();
        $cooltab = new Tab();
        $cooltab->class_name = 'AdminBeesBlog';
        $cooltab->module = '';
        $cooltab->id_parent = 0;
        foreach ($langs as $l) {
            $cooltab->name[$l['id_lang']] = $this->l('Blog');
        }
        $cooltab->save();
        @copy(dirname(__FILE__).'/AdminBeesBlog.gif', _PS_ROOT_DIR_.'/img/t/AdminBeesBlog.gif');
        $tabs = array(
            array(
                'class_name' => 'AdminBeesBlogCategory',
                'id_parent' => $cooltab->id,
                'module' => 'beesblog',
                'name' => 'Categories',
            ),
            array(
                'class_name' => 'AdminBeesBlogcomment',
                'id_parent' => $cooltab->id,
                'module' => 'beesblog',
                'name' => 'Comments',
            ),
            array(
                'class_name' => 'AdminBeesBlogPost',
                'id_parent' => $cooltab->id,
                'module' => 'beesblog',
                'name' => 'Blog Posts',
            ),
            array(
                'class_name' => 'AdminBeesBlogImageType',
                'id_parent' => $cooltab->id,
                'module' => 'beesblog',
                'name' => 'Image Types',
            ),
        );
        foreach ($tabs as $tab) {
            $newtab = new Tab();
            $newtab->class_name = $tab['class_name'];
            $newtab->id_parent = $tab['id_parent'];
            $newtab->module = $tab['module'];
            foreach ($langs as $l) {
                $newtab->name[$l['id_lang']] = $this->l($tab['name']);
            }
            $newtab->save();
        }

        return true;
    }

    /**
     * Get module configuration page
     *
     * @return string HTML
     */
    public function getContent()
    {
        // TODO: refactor to separate function
        $this->postProcess();
        $html = '';
        if (Tools::isSubmit('submit'.$this->name)) {
            Configuration::updateValue(self::META_TITLE, Tools::getValue(self::META_TITLE));
            Configuration::updateValue(self::META_KEYWORDS, Tools::getValue(self::META_KEYWORDS));
            Configuration::updateValue(self::META_DESCRIPTION, Tools::getValue(self::META_DESCRIPTION));
            Configuration::updateValue(self::POSTS_PER_PAGE, Tools::getValue(self::POSTS_PER_PAGE));
            Configuration::updateValue(self::SHOW_VIEWED, Tools::getValue(self::SHOW_VIEWED));
            Configuration::updateValue(self::DISABLE_CATEGORY_IMAGE, Tools::getValue(self::DISABLE_CATEGORY_IMAGE));
            Configuration::updateValue(self::SHOW_AUTHOR, Tools::getValue(self::SHOW_AUTHOR));
            Configuration::updateValue(self::SHOW_AUTHOR_STYLE, Tools::getValue(self::SHOW_AUTHOR_STYLE));
            Configuration::updateValue(self::SHOW_COLUMN, Tools::getValue(self::SHOW_COLUMN));
            Configuration::updateValue(self::MAIN_SLUG, Tools::getValue(self::MAIN_SLUG));
            Configuration::updateValue(self::USE_HTML, Tools::getValue(self::USE_HTML));
            Configuration::updateValue(self::SHOW_NO_IMAGE, Tools::getValue(self::SHOW_NO_IMAGE));
            Configuration::updateValue(self::CUSTOM_CSS, Tools::getValue(self::CUSTOM_CSS), true);
            $this->processImageUpload($_FILES);
            $html = $this->displayConfirmation($this->l('The settings have been updated successfully.'));
            $helper = $this->SettingForm();
            $html .= $helper->generateForm($this->fieldsForm);

            return $html;
        } elseif (Tools::isSubmit('generateimage')) {
            if (Tools::getValue('isdeleteoldthumblr') != 1) {
                BeesBlogImageType::ImageGenerate();
                $html = $this->displayConfirmation($this->l('Generate New Thumblr Succesfully.'));
                $helper = $this->SettingForm();
                $html .= $helper->generateForm($this->fieldsForm);

                return $html;
            } else {
                BeesBlogImageType::ImageDelete();
                BeesBlogImageType::ImageGenerate();
                $html = $this->displayConfirmation($this->l('Delete Old Image and Generate New Thumblr Succesfully.'));
                $helper = $this->SettingForm();
                $html .= $helper->generateForm($this->fieldsForm);

                return $html;
            }
        } else {
            $helper = $this->SettingForm();
            $html .= $helper->generateForm($this->fieldsForm);
            $html .= $this->renderDisqusOptions();

            return $html;
        }
    }

    /**
     * @return HelperForm
     */
    public function SettingForm()
    {
        // TODO: remove html
        $blogUrl = BeesBlog::getBeesBlogLink('beesblog');
        $imageDescription = '';
        $imageDescription .= ''.$this->l('Upload a Avatar from your computer.<br/>N.B : Only jpg image is allowed');
        $imageDescription .= '<br/><img style="clear:both;border:1px solid black;" alt="" src="'.__PS_BASE_URI__.'modules/beesblog/images/avatar/avatar.jpg" height="100" width="100"/><br />';
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->fieldsForm[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Setting'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Title'),
                    'name' => self::META_TITLE,
                    'size' => 70,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Keyword'),
                    'name' => self::META_KEYWORDS,
                    'size' => 70,
                    'required' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta Description'),
                    'name' => self::META_DESCRIPTION,
                    'rows' => 7,
                    'cols' => 66,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Main Blog Url'),
                    'name' => self::MAIN_SLUG,
                    'size' => 15,
                    'required' => true,
                    'desc' => '<p class="alert alert-info"><a href="'.$blogUrl.'">'.$blogUrl.'</a></p>',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use .html with Friendly Url'),
                    'name' => self::USE_HTML,
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Number of posts per page'),
                    'name' => self::POSTS_PER_PAGE,
                    'size' => 15,
                    'required' => true
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Show Author Name'),
                    'name' => self::SHOW_AUTHOR,
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Post Viewed'),
                    'name' => self::SHOW_VIEWED,
                    'required' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Author Name Style'),
                    'name' => self::SHOW_AUTHOR_STYLE,
                    'required' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('First Name, Last Name'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Last Name, First Name'),
                        ),
                    ),
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('AVATAR Image:'),
                    'name' => 'avatar',
                    'display_image' => false,
                    'desc' => $imageDescription,
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Show no image'),
                    'name' => self::SHOW_NO_IMAGE,
                    'required' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Show Category'),
                    'name' => self::DISABLE_CATEGORY_IMAGE,
                    'required' => false,
                    'class' => 't',
                    'desc' => 'Show category image and description on category page',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Blog Page Column Setting'),
                    'name' => self::SHOW_COLUMN,
                    'required' => false,
                    'values' => array(
                        array(
                            'id' => 'id_show_column',
                            'value' => 0,
                            'label' => $this->l('Use Both BeesBlog Column'),
                        ),
                        array(
                            'id' => 'id_show_column',
                            'value' => 1,
                            'label' => $this->l('Use Only BeesBlog Left Column'),
                        ),
                        array(
                            'id' => 'id_show_column',
                            'value' => 2,
                            'label' => $this->l('Use Only BeesBlog Right Column'),
                        ),
                        array(
                            'id' => 'id_show_column',
                            'value' => 3,
                            'label' => $this->l('Use Prestashop Column'),
                        ),
                    ),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Custom CSS'),
                    'name' => self::CUSTOM_CSS,
                    'rows' => 7,
                    'cols' => 66,
                    'required' => false,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($defaultLang == $lang['id_lang'] ? 1 : 0),
            );
        }
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'token='.Tools::getAdminTokenLite('AdminModules'),
            ),
        );
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;

        $helper->fields_value = $this->getFormValues();

        return $helper;
    }

    /**
     * Get form values
     *
     * @return array Form values
     */
    protected function getFormValues()
    {
        $configuration = Configuration::getMultiple(array(
            self::POSTS_PER_PAGE,
            self::SHOW_AUTHOR,
            self::SHOW_AUTHOR_STYLE,
            self::MAIN_SLUG,
            self::USE_HTML,
            self::SHOW_COLUMN,
            self::META_TITLE,
            self::META_KEYWORDS,
            self::META_DESCRIPTION,
            self::SHOW_VIEWED,
            self::SHOW_VIEWED,
            self::DISABLE_CATEGORY_IMAGE,
            self::CUSTOM_CSS,
            self::SHOW_NO_IMAGE,
        ));

        return $configuration;
    }

    /**
     * Render the General options form
     *
     * @return string HTML
     */
    protected function renderDisqusOptions()
    {
        $helper = new HelperOptions();
        $helper->id = 1;
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;

        return $helper->generateOptions(array_merge($this->getDisqusOptions()));
    }

    /**
     * Get available general options
     *
     * @return array General options
     */
    protected function getDisqusOptions()
    {
        return array(
            'locales' => array(
                'title' => $this->l('General Settings'),
                'icon' => 'icon-server',
                'fields' => array(
                    self::DISQUS_USERNAME => array(
                        'title' => $this->l('Disqus username'),
                        'type' => 'text',
                        'name' => self::DISQUS_USERNAME,
                        'value' => Configuration::get(self::DISQUS_USERNAME),
                        'validation' => 'isString',
                        'cast' => 'strval',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button',
                ),
            ),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $output = '';
        if (Tools::isSubmit('submitOptionsconfiguration') || Tools::isSubmit('submitOptions')) {
            $output .= $this->postProcessDisqusOptions();
        }

        return $output;
    }

    /**
     * Process General Options
     */
    protected function postProcessDisqusOptions()
    {
        $idShop = (int) $this->context->shop->id;

        $username = Tools::getValue(self::DISQUS_USERNAME);

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            if (Shop::getContext() == Shop::CONTEXT_ALL) {
                $this->updateAllValue(self::DISQUS_USERNAME, $username);
            } elseif (is_array(Tools::getValue('multishopOverrideOption'))) {
                $idShopGroup = (int) Shop::getGroupFromShop($idShop, true);
                $multishopOverride = Tools::getValue('multishopOverrideOption');
                if (Shop::getContext() == Shop::CONTEXT_GROUP) {
                    foreach (Shop::getShops(false, $idShop) as $idShop) {
                        if ($multishopOverride[self::DISQUS_USERNAME]) {
                            Configuration::updateValue(self::DISQUS_USERNAME, $username, false, $idShopGroup, $idShop);
                        }
                    }
                } else {
                    $idShop = (int) $idShop;
                    if ($multishopOverride[self::DISQUS_USERNAME]) {
                        Configuration::updateValue(self::DISQUS_USERNAME, $username, false, $idShopGroup, $idShop);
                    }
                }
            }
        } else {
            Configuration::updateValue(self::DISQUS_USERNAME, $username);
        }
    }

    /**
     * Proces image upload
     *
     * @param arary $files Uploaded files
     *
     * @return string Error messages
     */
    public function processImageUpload($files)
    {
        if (isset($files['avatar']) && isset($files['avatar']['tmp_name']) && !empty($files['avatar']['tmp_name'])) {
            if ($error = ImageManager::validateUpload($files['avatar'], 4000000)) {
                return $this->displayError($this->l('Invalid image'));
            } else {
                $ext = substr($files['avatar']['name'], strrpos($files['avatar']['name'], '.') + 1);
                $fileName = 'avatar.'.$ext;
                $path = _PS_MODULE_DIR_.'beesblog/images/avatar/'.$fileName;
                if (!move_uploaded_file($files['avatar']['tmp_name'], $path)) {
                    return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
                } else {
                    $authorTypes = BeesBlogImageType::getAllImagesFromType('author');
                    foreach ($authorTypes as $imageType) {
                        $dir = _PS_MODULE_DIR_.'beesblog/images/avatar/avatar-'.stripslashes($imageType['type_name']).'.jpg';
                        if (file_exists($dir)) {
                            unlink($dir);
                        }
                    }
                    $imageTypes = BeesBlogImageType::getAllImagesFromType('author');
                    foreach ($imageTypes as $imageType) {
                        ImageManager::resize(
                            $path,
                            _PS_MODULE_DIR_.'beesblog/images/avatar/avatar-'.stripslashes($imageType['type_name']).'.jpg',
                            (int) $imageType['width'],
                            (int) $imageType['height']
                        );
                    }
                }
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public static function getBeesBlogUrl()
    {
        $sslEnabled = Configuration::get('PS_SSL_ENABLED');
        $idLang = (int) Context::getContext()->language->id;
        $idShop = (int) Context::getContext()->shop->id;
        $rewriteSet = (int) Configuration::get('PS_REWRITING_SETTINGS');
        $ssl = null;
        static $forceSsl = null;
        if ($ssl === null) {
            if ($forceSsl === null) {
                $forceSsl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $forceSsl;
        }
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $idShop !== null) {
            $shop = new Shop($idShop);
        } else {
            $shop = Context::getContext()->shop;
        }
        $base = (($ssl && $sslEnabled) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        $langUrl = Language::getIsoById($idLang).'/';
        if ((!$rewriteSet && in_array($idShop, array((int) Context::getContext()->shop->id, null)))
            || !Language::isMultiLanguageActivated($idShop)
            || !(int) Configuration::get('PS_REWRITING_SETTINGS', null, null, $idShop)
        ) {
            $langUrl = '';
        }

        return $base.$shop->getBaseURI().$langUrl;
    }

    /**
     * Get link to BeesBlog item
     *
     * @param string $rewrite Rewrite
     * @param array  $params  Parameters
     * @param int    $idShop  Shop ID
     * @param int    $idLang  Language ID
     *
     * @return string URL to item
     * @throws PrestaShopException
     */
    public static function getBeesBlogLink($rewrite = 'beesblog', $params = null, $idShop = null, $idLang = null)
    {
        $url = BeesBlog::getBeesBlogUrl();
        $dispatcher = Dispatcher::getInstance();
        if (!empty($params)) {
            return $url.$dispatcher->createUrl($rewrite, $idLang, $params, false, '', $idShop);
        } else {
            return $url.$dispatcher->createUrl($rewrite, $idLang, array(), false, '', $idShop);
        }
    }

    /**
     * Update configuration value in ALL contexts
     *
     * @param string $key    Configuration key
     * @param mixed  $values Configuration values, can be string or array with id_lang as key
     * @param bool   $html   Contains HTML
     */
    public function updateAllValue($key, $values, $html = false)
    {
        foreach (Shop::getShops() as $shop) {
            Configuration::updateValue($key, $values, $html, $shop['id_shop_group'], $shop['id_shop']);
        }
        Configuration::updateGlobalValue($key, $values, $html);
    }
}
