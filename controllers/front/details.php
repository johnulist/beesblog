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
 * Class BeesBlogDetailsModuleFrontController
 */
class BeesBlogDetailsModuleFrontController extends BeesBlogModuleFrontController
{
    public $report = '';

    protected $idPost;

    /**
     * Initialize content
     *
     * @return void
     */
    public function initContent()
    {
        parent::initContent();
        $this->idPost = (int) BeesBlogPost::getIdByRewrite(Tools::getValue('blog_rewrite'));

        if (empty($this->idPost)) {
            return;

        }

        $configuration = Configuration::getMultiple(array(
            'coolshowauthorstyle',
            'coolshowauthor',
            'coolcustomcss',
            'coolshownoimg',
            'coolshowviewed',
            'PS_SHOP_NAME',
            'PS_SC_TWITTER',
            'PS_SC_GOOGLE',
            'PS_SC_FACEBOOK',
            'PS_SC_PINTEREST',
        ));

        $context = Context::getContext();
        $link = $context->link;

        // TODO: what does the hook name even mean? :S
        Hook::exec('actionsbsingle', array('id_post' => $this->idPost));
        $blogPost = new BeesBlogPost();
        $blogCategory = new BeesBlogCategory();
        $blogComment = new BeesBlogComment();

        $idLang = $this->context->language->id;

        $post = $blogPost->getRaw($this->idPost, $idLang);
        $tags = $blogPost->getTags($this->idPost);

        $comment = $blogComment->getComment($this->idPost, $idLang);
        $countcomment = $blogComment->getTotalComments($this->idPost, $idLang);
        $idCategory = $post['id_category'];
        $titleCategory = $blogCategory->getNameCategory($idCategory);
        if (file_exists(_PS_MODULE_DIR_.'beesblog/images/'.(int) $this->idPost.'.jpg')
            || file_exists(_PS_MODULE_DIR_.'beesblog/images/'.(int) $this->idPost.'.jpg')) {
            $postImage = $this->idPost;
        } else {
            $postImage = 'no';
        }

        BeesBlogPost::viewed($this->idPost);

        Media::addJsDef(array(
            'sharing_name' => addcslashes($post['meta_title'], "'"),
            'sharing_url' => addcslashes(Tools::getHttpHost(true).$_SERVER['REQUEST_URI'], "'"),
            'sharing_img' => addcslashes(Tools::getHttpHost(true).'/modules/beesblog/images/'.(int) $post['id_post'].'.jpg', "'"),
        ));
        $this->context->controller->addCss(_PS_MODULE_DIR_.'/socialsharing/socialsharing.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.'/socialsharing/socialsharing.js');

        $postProperties = array(
            'blogHome' => $link->getModuleLink('beesblog'),
            'post' => $post,
            'comments' => $comment,
            'tags' => $tags,
            'titleCategory' => $titleCategory[0]['meta_title'],
            'titlePost' => $post['meta_title'],
            'categoryLinkRewrite' => $titleCategory[0]['link_rewrite'],
            'metaTitle' => $post['meta_title'].' • '.$configuration['PS_SHOP_NAME'],
            'metaDescription' => (!empty($post['meta_description']) ? $post['meta_description'] : $post['short_description']),
            'metaKeywords' => $post['meta_keyword'],
            'postActive' => $post['active'],
            'content' => $post['content'],
            'idPost' => $post['id_post'],
            'coolshowauthorstyle' => $configuration['coolshowauthorstyle'],
            'coolshowauthor' => $configuration['coolshowauthor'],
            'created' => $post['created'],
            'firstname' => $post['firstname'],
            'lastname' => $post['lastname'],
            'coolcustomcss' => $configuration['coolcustomcss'],
            'coolshownoimg' => $configuration['coolshownoimg'],
            'coolshowviewed' => $configuration['coolshowviewed'],
            'viewed' => $post['viewed'],
            'commentStatus' => $post['comment_status'],
            'countcomment' => $countcomment,
            'PS_SC_TWITTER' => $configuration['PS_SC_TWITTER'],
            'PS_SC_GOOGLE' => $configuration['PS_SC_GOOGLE'],
            'PS_SC_FACEBOOK' => $configuration['PS_SC_FACEBOOK'],
            'PS_SC_PINTEREST' => $configuration['PS_SC_PINTEREST'],
            'postImage' => $postImage,
            'report' => $this->report,
            'idCategory' => $post['id_category'],
        );

        // TODO: hmmz, maybe the disqus comments should be hooked instead
        $this->context->smarty->assign($postProperties);
        $this->context->smarty->assign(
            'HOOK_SMART_BLOG_POST_FOOTER',
            Hook::exec('displaySmartAfterPost', $postProperties)
        );
        $this->setTemplate('post.tpl');
    }
}
