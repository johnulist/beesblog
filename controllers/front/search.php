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

require_once dirname(__FILE__).'/../../classes/autoload.php';

use BeesBlogModule\BeesBlogModuleFrontController;
use BeesBlogModule\BeesBlogPost;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class BeesBlogsearchModuleFrontController
 */
class BeesBlogsearchModuleFrontController extends BeesBlogModuleFrontController
{
    /**
     * Initialize content
     */
    public function initContent()
    {
        parent::initContent();

        $configuration = \Configuration::getMultiple([
            \BeesBlog::POSTS_PER_PAGE,
            \BeesBlog::SHOW_AUTHOR,
            \BeesBlog::SHOW_AUTHOR_STYLE,
            \BeesBlog::SHOW_NO_IMAGE,
        ]);

        $titleCategory = '';
        $postsPerPage = $configuration[\BeesBlog::POSTS_PER_PAGE];
        $limitStart = 0;
        $limit = $postsPerPage;

        if ((bool) \Tools::getValue('page')) {
            $c = (int) \Tools::getValue('page');
            $limitStart = $postsPerPage * ($c - 1);
        }

        $keyword = \Tools::getValue('beessearch');
        $idLang = (int) $this->context->language->id;
        $result = BeesBlogPost::beesBlogSearchPost($keyword, $idLang, $limitStart, $limit);

        $total = BeesBlogPost::beesBlogSearchPostCount($keyword, $idLang);
        $totalpages = ceil($total / $postsPerPage);

        $this->context->smarty->assign([
            'postcategory' => $result,
            'title_category' => $titleCategory,
            'beesshowauthorstyle' => $configuration[\BeesBlog::SHOW_AUTHOR_STYLE],
            'limit' => isset($limit) ? $limit : 0,
            'limit_start' => isset($limitStart) ? $limitStart : 0,
            'c' => isset($c) ? $c : 1,
            'total' => $total,
            'beesshowviewed' =>    $configuration[\BeesBlog::SHOW_VIEWED],
            'beescustomcss' =>     $configuration[\BeesBlog::CUSTOM_CSS],
            'beesshownoimg' =>     $configuration[\BeesBlog::SHOW_NO_IMAGE],
            'beesshowauthor' =>    $configuration[\BeesBlog::SHOW_AUTHOR],
            'beesblogliststyle' => \Configuration::get('beesblogliststyle'),
            'post_per_page' => $postsPerPage,
            'beessearch' => \Tools::getValue('beessearch'),
            'pagenums' => $totalpages - 1,
            'totalpages' => $totalpages,
        ]);

        $templateName = 'searchresult.tpl';

        $this->setTemplate($templateName);
    }
}
