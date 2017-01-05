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
 * Class BeesBlogModuleFrontController
 */
class BeesBlogModuleFrontController extends ModuleFrontController
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
        if (Tools::isSubmit('blog_rewrite') && $idCategory = BeesBlogCategory::getIdByRewrite(Tools::getValue('blog_rewrite'))) {
            $this->context->smarty->assign(BeesBlogCategory::getCategoryMeta((int) $idCategory));
        }
        if (Tools::isSubmit('blog_rewrite') && $idPost = BeesBlogPost::getIdByRewrite(Tools::getValue('blog_rewrite'))) {
            $this->context->smarty->assign(BeesBlogPost::getMeta((int) $idPost));
        }
        if (empty($idCategory) && empty($idPost)) {
            $meta['meta_title'] = Configuration::get('beesblogmetatitle');
            $meta['meta_description'] = Configuration::get('beesblogmetadescrip');
            $meta['meta_keywords'] = Configuration::get('beesblogmetakeyword');
            $this->context->smarty->assign($meta);
        }
        if (Configuration::get('coolshowcolumn') == 0) {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => Hook::exec('displayBeesBlogLeft'),
                'HOOK_RIGHT_COLUMN' => Hook::exec('displayBeesBlogRight'),
            ));
        } elseif (Configuration::get('coolshowcolumn') == 1) {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => Hook::exec('displayBeesBlogLeft'),
            ));
        } elseif (Configuration::get('coolshowcolumn') == 2) {
            $this->context->smarty->assign(array(
                'HOOK_RIGHT_COLUMN' => Hook::exec('displayBeesBlogRight'),
            ));
        } elseif (Configuration::get('coolshowcolumn') == 3) {
            $this->context->smarty->assign(array());
        } else {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => Hook::exec('displayBeesBlogLeft'),
                'HOOK_RIGHT_COLUMN' => Hook::exec('displayBeesBlogRight'),
            ));
        }
    }
}
