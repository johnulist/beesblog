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

require_once _PS_MODULE_DIR_.'beesblog/classes/autoload.php';
if (!class_exists('BeesBlog')) {
    require_once _PS_MODULE_DIR_.'beesblog/beesblog.php';
}

/**
 * Class BeesBlogtagpostModuleFrontController
 */
class BeesBlogtagpostModuleFrontController extends BeesBlogModuleFrontController
{
    /**
     * Initialize content
     */
    public function initContent()
    {
        parent::initContent();

        $configuration = Configuration::getMultiple(array(
            BeesBlog::POSTS_PER_PAGE,
            BeesBlog::SHOW_AUTHOR,
            BeesBlog::SHOW_AUTHOR_STYLE,
            BeesBlog::CUSTOM_CSS,
            BeesBlog::SHOW_NO_IMAGE,
        ));

        $blogcomment = new BeesBlogComment();
        $titleCategory = '';
        $postsPerPage = $configuration[BeesBlog::POSTS_PER_PAGE];
        $limitStart = 0;
        $limit = $postsPerPage;

        if ((bool) Tools::getValue('page')) {
            $c = (int) Tools::getValue('page');
            $limitStart = $postsPerPage * ($c - 1);
        }

        $keyword = urldecode(Tools::getValue('tag'));
        $idLang = (int) $this->context->language->id;
        $result = BeesBlogPost::tagsPost($keyword, $idLang);
        $total = count($result);
        $totalpages = ceil($total / $postsPerPage);
        $i = 0;
        if (!empty($result)) {
            foreach ($result as $item) {
                $to[$i] = $blogcomment->getTotalComments($item['id_post']);
                $i++;
            }
            $j = 0;
            if (isset($to)) {
                foreach ($to as $item) {
                    if ($item == '') {
                        $result[$j]['totalcomment'] = 0;
                    } else {
                        $result[$j]['totalcomment'] = $item;
                    }
                    $j++;
                }
            }
        }

        $this->context->smarty->assign(array(
            'postcategory' => $result,
            'title_category' => $titleCategory,
            'coolshowauthorstyle' => $configuration[BeesBlog::SHOW_AUTHOR_STYLE],
            'limit' => isset($limit) ? $limit : 0,
            'limit_start' => isset($limitStart) ? $limitStart : 0,
            'c' => isset($c) ? $c : 1,
            'total' => $total,
            'coolshowviewed' => $configuration[BeesBlog::SHOW_VIEWED],
            'coolcustomcss' => $configuration[BeesBlog::CUSTOM_CSS],
            'coolshownoimg' => $configuration[BeesBlog::SHOW_NO_IMAGE],
            'coolshowauthor' => $configuration[BeesBlog::SHOW_AUTHOR],
            'beesblogliststyle' => Configuration::get('beesblogliststyle'),
            'post_per_page' => $postsPerPage,
            'pagenums' => $totalpages - 1,
            'totalpages' => $totalpages,
        ));

        $templateName = 'tagresult.tpl';

        $this->setTemplate($templateName);
    }
}
