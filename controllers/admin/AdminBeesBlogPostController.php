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


/**
 * Class AdminBeesBlogPostController
 */
class AdminBeesBlogPostController extends ModuleAdminController
{
    public $assoType = 'shop';
    protected $blogPost = null;

    /**
     * AdminBeesBlogPostController constructor.
     */
    public function __construct()
    {
        $this->table = 'bees_blog_post';
        $this->className = 'BeesBlogPost';
        $this->lang = true;
        $this->image_dir = '../modules/beesblog/images';
        $this->context = Context::getContext();
        $this->_defaultOrderBy = 'created';
        $this->_defaultorderWay = 'DESC';
        $this->bootstrap = true;
        if (Shop::isFeatureActive()) {
            Shop::addTableAssociation($this->table, array('type' => 'shop'));
        }
        parent::__construct();
        $this->fields_list = array(
            'id_bees_blog_post' => array(
                'title' => $this->l('ID'),
                'width' => 50,
                'type' => 'text',
                'orderby' => true,
                'filter' => true,
                'search' => true,
            ),
            'viewed' => array(
                'title' => $this->l('View'),
                'width' => 50,
                'type' => 'text',
                'lang' => true,
                'orderby' => true,
                'filter' => false,
                'search' => false,
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'image' => $this->image_dir,
                'orderby' => false,
                'search' => false,
                'width' => 200,
                'align' => 'center',
                'filter' => false,
            ),
            'meta_title' => array(
                'title' => $this->l('Title'),
                'width' => 440,
                'type' => 'text',
                'lang' => true,
                'orderby' => true,
                'filter' => true,
                'search' => true,
            ),
            'created' => array(
                'title' => $this->l('Posted Date'),
                'width' => 100,
                'type' => 'date',
                'lang' => true,
                'orderby' => true,
                'filter' => true,
                'search' => true,
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'width' => '70',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => true,
                'filter' => true,
                'search' => true,
            ),
        );
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'bees_blog_post_shop sbs ON a.id_bees_blog_post=sbs.id_bees_blog_post && sbs.id_shop IN('.implode(',', Shop::getContextListShopID()).')';
        $this->_select = 'sbs.id_shop';
        $this->_defaultOrderBy = 'a.id_bees_blog_post';
        $this->_defaultOrderWay = 'DESC';
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->_group = 'GROUP BY a.bees_blog_post';
        }
        parent::__construct();
    }

    /**
     * Render list
     *
     * @return false|string
     */
    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    /**
     * Post process
     */
    public function postProcess()
    {
        $coolBlogPost = new BeesBlogPost();

        if (Tools::isSubmit('deletebees_blog_post') && Tools::getValue('id_bees_blog_post') != '') {
            $coolBlogPost = new BeesBlogPost((int) Tools::getValue('id_bees_blog_post'));

            if (!$coolBlogPost->delete()) {
                $this->errors[] = Tools::displayError('An error occurred while deleting the object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            } else {
                Hook::exec('actionsbdeletepost', array('BeesBlogPost' => $coolBlogPost));
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminBeesBlogPost'));
            }
        } elseif (Tools::getValue('deleteImage')) {
            $this->processForceDeleteImage();
            if (Tools::isSubmit('forcedeleteImage')) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminBeesBlogPost').'&conf=7');
            }
        } elseif (Tools::isSubmit('submitAddbees_blog_post')) {
            if (!$idBeesBlogPost = (int) Tools::getValue('id_bees_blog_post')) {
                $coolBlogPost = new $coolBlogPost();
                $idLangDefault = Configuration::get('PS_LANG_DEFAULT');
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $title = Tools::getValue('meta_title_'.$language['id_lang']);
                    $coolBlogPost->meta_title[$language['id_lang']] = $title;
                    $coolBlogPost->meta_keyword[$language['id_lang']] = (string) Tools::getValue('meta_keyword_'.$language['id_lang']);
                    $coolBlogPost->meta_description[$language['id_lang']] = Tools::getValue('meta_description_'.$language['id_lang']);
                    $coolBlogPost->short_description[$language['id_lang']] = (string) Tools::getValue('short_description_'.$language['id_lang']);
                    $coolBlogPost->content[$language['id_lang']] = Tools::getValue('content_'.$language['id_lang']);
                    if (Tools::getValue('link_rewrite_'.$language['id_lang']) == '' && Tools::getValue('link_rewrite_'.$language['id_lang']) == null) {
                        $coolBlogPost->link_rewrite[$language['id_lang']] = Tools::link_rewrite(Tools::getValue('meta_title_'.$idLangDefault));
                    } else {
                        $coolBlogPost->link_rewrite[$language['id_lang']] = Tools::link_rewrite(Tools::getValue('link_rewrite_'.$language['id_lang']));
                    }
                    $coolBlogPost->lang_active[$language['id_lang']] = Tools::getValue('lang_active_'.(int) $language['id_lang']) == 'on';
                }
                $coolBlogPost->id_parent = Tools::getValue('id_parent');
                $coolBlogPost->position = 0;
                $coolBlogPost->active = Tools::getValue('active');

                $coolBlogPost->id_category = Tools::getValue('id_category');
                $coolBlogPost->comment_status = Tools::getValue('comment_status');
                $coolBlogPost->id_author = $this->context->employee->id;
                if (Tools::getValue('created')) {
                    $coolBlogPost->created = date('y-m-d H:i:s', strtotime(Tools::getValue('created')));
                } else {
                    $coolBlogPost->created = date('y-m-d H:i:s');
                }
                $coolBlogPost->modified = date('y-m-d H:i:s');
                $coolBlogPost->available = 1;
                $coolBlogPost->is_featured = Tools::getValue('is_featured');
                $coolBlogPost->viewed = 1;

                $coolBlogPost->post_type = Tools::getValue('post_type');

                if (!$coolBlogPost->save()) {
                    $this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
                } else {
                    Hook::exec('actionsbnewpost', array('BeesBlogPost' => $coolBlogPost));
                    $this->updateTags($languages, $coolBlogPost);
                    $this->processImage($_FILES, $coolBlogPost->id);
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminBeesBlogPost'));
                }
            } elseif ($idBeesBlogPost = Tools::getValue('id_bees_blog_post')) {
                $coolBlogPost = new BeesBlogPost($idBeesBlogPost);
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $title = Tools::getValue('meta_title_'.$language['id_lang']);
                    $coolBlogPost->meta_title[$language['id_lang']] = $title;
                    $coolBlogPost->meta_keyword[$language['id_lang']] = Tools::getValue('meta_keyword_'.$language['id_lang']);
                    $coolBlogPost->meta_description[$language['id_lang']] = Tools::getValue('meta_description_'.$language['id_lang']);
                    $coolBlogPost->short_description[$language['id_lang']] = Tools::getValue('short_description_'.$language['id_lang']);
                    $coolBlogPost->content[$language['id_lang']] = Tools::getValue('content_'.$language['id_lang']);
                    $coolBlogPost->link_rewrite[$language['id_lang']] = Tools::link_rewrite(Tools::getValue('link_rewrite_'.$language['id_lang']));
                    $coolBlogPost->lang_active[$language['id_lang']] = Tools::getValue('lang_active_'.(int) $language['id_lang']) == 'on';
                }
                $coolBlogPost->is_featured = Tools::getValue('is_featured');
                $coolBlogPost->id_parent = Tools::getValue('id_parent');
                $coolBlogPost->active = Tools::getValue('active');
                $coolBlogPost->id_category = Tools::getValue('id_category');
                $coolBlogPost->comment_status = Tools::getValue('comment_status');
                $coolBlogPost->id_author = $this->context->employee->id;
                if (Tools::getValue('created')) {
                    $coolBlogPost->created = date('y-m-d H:i:s', strtotime(Tools::getValue('created')));
                }
                $coolBlogPost->modified = date('y-m-d H:i:s');
                if (!$coolBlogPost->update()) {
                    $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {
                    Hook::exec('actionsbupdatepost', array('BeesBlogPost' => $coolBlogPost));
                }
                $this->updateTags($languages, $coolBlogPost);
                $this->processImage($_FILES, $coolBlogPost->id_bees_blog_post);

                Tools::redirectAdmin($this->context->link->getAdminLink('AdminBeesBlogPost'));
            }
        } elseif (Tools::isSubmit('statusbees_blog_post') && Tools::getValue($this->identifier)) {
            if ($this->tabAccess['edit'] === '1') {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    if ($object->toggleStatus()) {
                        Hook::exec('actionsbtogglepost', array('BeesBlogPost' => $this->object));
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminBeesBlogPost'));
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred while updating the status.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                        .' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('bees_blog_postOrderby') && Tools::isSubmit('bees_blog_postOrderway')) {
            $this->_defaultOrderBy = Tools::getValue('bees_blog_postOrderby');
            $this->_defaultOrderWay = Tools::getValue('bees_blog_postOrderway');
        }
    }

    /**
     *
     */
    public function processForceDeleteImage()
    {
        $blogPost = $this->loadObject(true);

        if (Validate::isLoadedObject($blogPost)) {

            $this->deleteImage($blogPost->id_bees_blog_post);
        }
    }

    /**
     * @param int $idBeesBlogPost
     *
     * @return bool
     */
    public function deleteImage($idBeesBlogPost = 1)
    {

        if (!$idBeesBlogPost) {
            return false;
        }

        // Delete base image
        if (file_exists(_MODULE_BEESBLOG_DIR_.'/'.$idBeesBlogPost.'.jpg')) {
            unlink($this->image_dir.'/'.$idBeesBlogPost.'.jpg');
        } else {
            return false;
        }

        // now we need to delete the image type of post

        $filesToDelete = array();

        // Delete auto-generated images
        $imageTypes = BeesBlogImageType::getAllImagesFromType('post');
        foreach ($imageTypes as $image_type) {
            $filesToDelete[] = $this->image_dir.'/'.$idBeesBlogPost.'-'.$image_type['type_name'].'.jpg';
        }

        // Delete tmp images
        $filesToDelete[] = _PS_TMP_IMG_DIR_.'bees_blog_post_'.$idBeesBlogPost.'.jpg';
        $filesToDelete[] = _PS_TMP_IMG_DIR_.'bees_blog_post_mini_'.$idBeesBlogPost.'.jpg';

        foreach ($filesToDelete as $file) {
            if (file_exists($file) && !@unlink($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $files
     * @param $id
     *
     * @return bool
     */
    public function processImage($files, $id)
    {
        if (isset($files['image']) && isset($files['image']['tmp_name']) && !empty($files['image']['tmp_name'])) {
            if ($error = ImageManager::validateUpload($files['image'], 4000000)) {
                return $this->errors[] = $this->l('Invalid image');
            } else {
                $path = _PS_MODULE_DIR_.'beesblog/images/'.$id.'.'.$this->imageType;

                $tempName = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                if (!$tempName) {
                    return false;
                }

                if (!move_uploaded_file($files['image']['tmp_name'], $tempName)) {
                    return false;
                }

                // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
                if (!ImageManager::checkImageMemoryLimit($tempName)) {
                    $this->errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ');
                }

                // FIXME: dimensions undefined
                // Copy new image
//                if (empty($this->errors) && !ImageManager::resize($tempName, $path, (int) $width, (int) $height,
//                        ($ext ? $ext : $this->imageType))
//                ) {
//                    $this->errors[] = Tools::displayError('An error occurred while uploading the image.');
//                }

                if (count($this->errors)) {
                    return false;
                }
                if ($this->afterImageUpload()) {
                    unlink($tempName);
                    //  return true;
                }

                $postTypes = BeesBlogImageType::getAllImagesFromType('post');
                foreach ($postTypes as $imageType) {
                    $dir = _PS_MODULE_DIR_.'beesblog/images/'.$id.'-'.stripslashes($imageType['type_name']).'.jpg';
                    if (file_exists($dir)) {
                        unlink($dir);
                    }
                }
                foreach ($postTypes as $imageType) {
                    ImageManager::resize(
                        $path,
                        _PS_MODULE_DIR_.'beesblog/images/'.$id.'-'.stripslashes($imageType['type_name']).'.jpg',
                        (int) $imageType['width'],
                        (int) $imageType['height']
                    );
                }
            }
        }
    }

    /**
     * @return string|void
     */
    public function renderForm()
    {
        // FIXME: what is this?
//        $img_desc = '';
//        $img_desc .= $this->l('Upload a Feature Image from your computer.<br/>N.B : Only jpg image is allowed');
//        if (Tools::getValue('id_bees_blog_post') != '' && Tools::getValue('id_bees_blog_post') != null) {
//            $img_desc .= '<br/><img style="height:auto;width:300px;clear:both;border:1px solid black;" alt="" src="'.__PS_BASE_URI__.'modules/beesblog/images/'.Tools::getValue('id_bees_blog_post').'.jpg" /><br />';
//        }
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $image = _MODULE_BEESBLOG_DIR_.$obj->id.'.jpg';

        $imageUrl = ImageManager::thumbnail($image, $this->table.'_'.Tools::getValue('id_bees_blog_post').'.jpg', 200, 'jpg', true, true);
        $imageSize = file_exists($image) ? filesize($image) / 1000 : false;

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Blog Post'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'post_type',
                    'default_value' => 0,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Blog Title'),
                    'name' => 'meta_title',
                    'id' => 'name',
                    'class' => 'copyMeta2friendlyURL',
                    'size' => 60,
                    'required' => true,
                    'desc' => $this->l('Enter Your Blog Post Title'),
                    'lang' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'content',
                    'lang' => true,
                    'rows' => 10,
                    'cols' => 62,
                    'class' => 'rte',
                    'autoload_rte' => true,
                    'required' => true,
                    'hint' => array(
                        $this->l('Enter Your Post Description'),
                        $this->l('Invalid characters:').' <>;=#{}',
                    ),
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Feature Image:'),
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $imageUrl ? $imageUrl : false,
                    'size' => $imageSize,
                    'delete_url' => self::$currentIndex.'&'.$this->identifier.'='.Tools::getValue('id_bees_blog_post').'&token='.$this->token.'&deleteImage=1',
                    'hint' => $this->l('Upload a feature image from your computer.'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Blog Category'),
                    'name' => 'id_category',
                    'options' => array(
                        'query' => BeesBlogCategory::getCategory(),
                        'id' => 'id_bees_blog_category',
                        'name' => 'meta_title',
                    ),
                    'desc' => $this->l('Select Your Parent Category'),
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'hint' => array(
                        $this->l('To add "tags" click in the field, write something, and then press "Enter."'),
                        $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Short Description'),
                    'name' => 'short_description',
                    'rows' => 10,
                    'cols' => 62,
                    'lang' => true,
                    'required' => true,
                    'hint' => array(
                        $this->l('Enter Your Post Short Description'),
                    ),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta Description'),
                    'name' => 'meta_description',
                    'rows' => 10,
                    'cols' => 62,
                    'lang' => true,
                    'required' => false,
                    'desc' => $this->l('Enter Your Post Meta Description'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link Rewrite'),
                    'name' => 'link_rewrite',
                    'size' => 60,
                    'lang' => true,
                    'required' => false,
                    'hint' => $this->l('Only letters and the hyphen (-) character are allowed.'),
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Tag'),
                    'name' => 'tags',
                    'size' => 60,
                    'lang' => true,
                    'required' => false,
                    'hint' => array(
                        $this->l('To add "tags" click in the field, write something, and then press "Enter."'),
                        $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Comment Status'),
                    'name' => 'comment_status',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                    'desc' => $this->l('You can enable or disable comments'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Available for these languages'),
                    'name' => 'lang_active',
                    'multiple' => true,
                    'values' => array(
                        'query' => Language::getLanguages(false),
                        'id' => 'id_lang',
                        'name' => 'name',
                    ),
                    'expand' => (count(Language::getLanguages(false)) > 10) ? array(
                        'print_total' => count(Language::getLanguages(false)),
                        'default' => 'show',
                        'show' => array('text' => $this->l('Show'), 'icon' => 'plus-sign-alt'),
                        'hide' => array('text' => $this->l('Hide'), 'icon' => 'minus-sign-alt'),
                    ) : null,
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Publish date'),
                    'name' => 'created',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Featured'),
                    'name' => 'is_featured',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'is_featured',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'is_featured',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }

        if (!($coolBlogPost = $this->loadObject(true))) {
            return;
        }

        $image = ImageManager::thumbnail(
            _MODULE_BEESBLOG_DIR_.$coolBlogPost->id_bees_blog_post.'.jpg',
            $this->table.'_'.(int) $coolBlogPost->id_bees_blog_post.'.'.$this->imageType,
            350,
            $this->imageType,
            true
        );

        $this->fields_value = array(
            'image' => $image ? $image : false,
            'size' => $image ? filesize(_MODULE_BEESBLOG_DIR_.$coolBlogPost->id_bees_blog_post.'.jpg') / 1000 : false
        );

        if (Tools::getValue('id_bees_blog_post') != '' && Tools::getValue('id_bees_blog_post') != null) {
            foreach (Language::getLanguages(false) as $lang) {
                $this->fields_value['tags'][(int)$lang['id_lang']] = BeesBlogPost::getTagsByLang((int) Tools::getValue('id_bees_blog_post'),
                    (int) $lang['id_lang']);
            }
        }

        foreach (Language::getLanguages(true) as $language) {
            $this->fields_value['lang_active_'.(int) $language['id_lang']] = (bool) BeesBlogPost::getLangActive(Tools::getValue('id_bees_blog_post'), $language['id_lang']);
        }

        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int) Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');

        return parent::renderForm();
    }

    /**
     *
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUI('ui.widget');
        $this->addJqueryPlugin('tagify');
    }

    /**
     * Update tags
     *
     * @param array        $languages
     * @param BeesBlogPost $post
     *
     * @return bool
     */
    public function updateTags($languages, $post)
    {
        $tagSuccess = true;

        if (!BeesBlogPost::deleteTags((int) $post->id)) {
            $this->errors[] = Tools::displayError('An error occurred while attempting to delete previous tags.');
        }
        foreach ($languages as $language) {
            if ($value = Tools::getValue('tags_'.$language['id_lang'])) {
                $tagSuccess &= BeesBlogPost::addTags($language['id_lang'], (int) $post->id, $value);
            }
        }

        if (!$tagSuccess) {
            $this->errors[] = Tools::displayError('An error occurred while adding tags.');
        }

        return $tagSuccess;
    }
}
