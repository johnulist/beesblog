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
 * Class BeesBlogModuleFrontController
 */
class BeesBlogModuleFrontController extends \ModuleFrontController
{
    /**
     * Initialize content
     */
    public function initContent()
    {
        // TODO: find out if this file can be removed
        parent::initContent();
        $idCategory = 0;
        $idPost = 0;
        $this->context->smarty->assign('blogHome', \BeesBlog::getBeesBlogLink());
        if (\Tools::isSubmit(\BeesBlog::BLOG_REWRITE) && $idCategory = BeesBlogCategory::getIdByRewrite(\Tools::getValue(\BeesBlog::BLOG_REWRITE))) {
            $this->context->smarty->assign(BeesBlogCategory::getCategoryMeta((int) $idCategory));
        }
        if (\Tools::isSubmit(\BeesBlog::BLOG_REWRITE) && $idPost = BeesBlogPost::getIdByRewrite(\Tools::getValue(\BeesBlog::BLOG_REWRITE))) {
            $this->context->smarty->assign(BeesBlogPost::getMeta((int) $idPost));
        }
        if (empty($idCategory) && empty($idPost)) {
            $meta['meta_title'] = \Configuration::get(\BeesBlog::META_TITLE);
            $meta['meta_description'] = \Configuration::get(\BeesBlog::META_DESCRIPTION);
            $meta['meta_keywords'] = \Configuration::get(\BeesBlog::META_KEYWORDS);
            $this->context->smarty->assign($meta);
        }

        switch (\Configuration::get(\BeesBlog::SHOW_COLUMN)) {
            case 0:
                $this->context->smarty->assign([
                    'HOOK_LEFT_COLUMN' => \Hook::exec('displayBeesBlogLeft'),
                    'HOOK_RIGHT_COLUMN' => \Hook::exec('displayBeesBlogRight'),
                ]);
                break;
            case 1:
                $this->context->smarty->assign([
                    'HOOK_LEFT_COLUMN' => \Hook::exec('displayBeesBlogLeft'),
                ]);
                break;
            case 2:
                $this->context->smarty->assign([
                    'HOOK_RIGHT_COLUMN' => \Hook::exec('displayBeesBlogRight'),
                ]);
                break;
            case 3:
                $this->context->smarty->assign([]);
                break;
            default:
                $this->context->smarty->assign([
                    'HOOK_LEFT_COLUMN' => \Hook::exec('displayBeesBlogLeft'),
                    'HOOK_RIGHT_COLUMN' => \Hook::exec('displayBeesBlogRight'),
                ]);
                break;
        }
    }
}
