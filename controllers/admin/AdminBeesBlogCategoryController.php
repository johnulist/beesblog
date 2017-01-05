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
 * Class AdminBeesBlogCategoryController
 */
class AdminBeesBlogCategoryController extends ModuleAdminController
{
    public $module;

    /**
     * AdminBeesBlogCategoryController constructor.
     */
    public function __construct()
    {
        $this->table = 'bees_blog_category';
        $this->className = 'BeesBlogCategory';
        $this->module = 'beesblog';
        $this->lang = true;
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->context = Context::getContext();
        if (Shop::isFeatureActive()) {
            Shop::addTableAssociation($this->table, array('type' => 'shop'));
        }
        parent::__construct();
        $this->fields_list = array(
            'id_bees_blog_category' => array(
                'title' => $this->l('Id'),
                'width' => 100,
                'type' => 'text',
            ),
            'meta_title' => array(
                'title' => $this->l('Title'),
                'width' => 440,
                'type' => 'text',
                'lang' => true,
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'width' => '70',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
            ),
        );

        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'bees_blog_category_shop sbs ON a.id_bees_blog_category=sbs.id_bees_blog_category && sbs.id_shop IN('.implode(',', Shop::getContextListShopID()).')';

        $this->_select = 'sbs.id_shop';
        $this->_defaultOrderBy = 'a.id_bees_blog_category';
        $this->_defaultOrderWay = 'DESC';

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->_group = 'GROUP BY a.id_bees_blog_category';
        }

        parent::__construct();
    }

    /**
     * @return string|void
     */
    public function renderForm()
    {
        $imageDescription = '';
        $imageDescription .= $this->l('Upload an avatar from your computer.<br/>N.B : Only jpeg images are allowed');
        if (Tools::getValue('id_bees_blog_category') != '' && Tools::getValue('id_bees_blog_category') != null) {
            $imageDescription .= '<br/><img style="height:auto;width:300px;clear:both;border:1px solid black;" alt="" src="'.__PS_BASE_URI__.'modules/beesblog/images/category/'.Tools::getValue('id_bees_blog_category').'.jpg" /><br />';
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Blog Category'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Title'),
                    'name' => 'meta_title',
                    'size' => 60,
                    'required' => true,
                    'desc' => $this->l('Enter Your Category Name'),
                    'lang' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'lang' => true,
                    'rows' => 10,
                    'cols' => 62,
                    'class' => 'rte',
                    'autoload_rte' => true,
                    'required' => false,
                    'desc' => $this->l('Enter Your Category Description'),
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Category Image:'),
                    'name' => 'category_image',
                    'display_image' => false,
                    'desc' => $imageDescription,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Keyword'),
                    'name' => 'meta_keyword',
                    'lang' => true,
                    'size' => 60,
                    'required' => false,
                    'desc' => $this->l('Enter Your Category Meta Keyword. Separated by comma(,)'),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta Description'),
                    'name' => 'meta_description',
                    'rows' => 10,
                    'cols' => 62,
                    'lang' => true,
                    'required' => false,
                    'desc' => $this->l('Enter Your Category Meta Description'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link Rewrite'),
                    'name' => 'link_rewrite',
                    'size' => 60,
                    'lang' => true,
                    'required' => true,
                    'desc' => $this->l('Enetr Your Category Slug. Use In SEO Friendly URL'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Parent Category'),
                    'name' => 'id_parent',
                    'options' => array(
                        'query' => BeesBlogCategory::getCategory(),
                        'id' => 'id_bees_blog_category',
                        'name' => 'meta_title',
                    ),
                    'desc' => $this->l('Select Your Parent Category'),
                ),
                array(
                    'type' => 'radio',
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

        if (!($blogCategory = $this->loadObject(true))) {
            return;
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        return parent::renderForm();
    }

    /**
     * @return false|string
     */
    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    /**
     *
     */
    public function postProcess()
    {
        if (Tools::isSubmit('deletebees_blog_category') && Tools::getValue('id_bees_blog_category') != '') {
            $idLang = (int) Context::getContext()->language->id;
            $catpost = (int) BeesBlogPost::getPostCountByCategory($idLang, Tools::getValue('id_bees_blog_category'));
            if ((int) $catpost != 0) {
                $this->errors[] = Tools::displayError('You need to delete all posts associate with this category .');
            } else {
                $blogCategory = new BeesBlogCategory((int)Tools::getValue('id_bees_blog_category'));
                if (!$blogCategory->delete()) {
                    $this->errors[] = Tools::displayError('An error occurred while deleting the object.')
                        .' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {
                    Hook::exec('actionsbdeletecat', array('BlogCategory' => $blogCategory));
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminBeesBlogCategory'));
                }
            }
        } elseif (Tools::isSubmit('submitAddbees_blog_category')) {
            parent::validateRules();
            if (count($this->errors)) {
                return false;
            }
            if (!$idBeesBlogCategory = (int)Tools::getValue('id_bees_blog_category')) {
                $blogCategory = new BeesBlogCategory();

                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $title = str_replace('"', '',
                        htmlspecialchars_decode(html_entity_decode(Tools::getValue('meta_title_'.$language['id_lang']))));
                    $blogCategory->meta_title[$language['id_lang']] = $title;
                    $blogCategory->meta_keyword[$language['id_lang']] = Tools::getValue('meta_keyword_'.$language['id_lang']);
                    $blogCategory->meta_description[$language['id_lang']] = Tools::getValue('meta_description_'.$language['id_lang']);
                    $blogCategory->description[$language['id_lang']] = Tools::getValue('description_'.$language['id_lang']);
                    if (Tools::getValue('link_rewrite_'.$language['id_lang']) == '' && Tools::getValue('link_rewrite_'.$language['id_lang']) == null) {
                        $blogCategory->link_rewrite[$language['id_lang']] = str_replace(array(
                            ' ',
                            ':',
                            '\\',
                            '/',
                            '#',
                            '!',
                            '*',
                            '.',
                            '?'
                        ), '-', Tools::getValue('meta_title_'.$language['id_lang']));
                    } else {
                        $blogCategory->link_rewrite[$language['id_lang']] = str_replace(array(
                            ' ',
                            ':',
                            '\\',
                            '/',
                            '#',
                            '!',
                            '*',
                            '.',
                            '?'
                        ), '-', Tools::getValue('link_rewrite_'.$language['id_lang']));
                    }
                }
                $blogCategory->id_parent = Tools::getValue('id_parent');
                $blogCategory->position = Tools::getValue('position');
                $blogCategory->desc_limit = Tools::getValue('desc_limit');
                $blogCategory->active = Tools::getValue('active');
                $blogCategory->date_add = date('Y-m-d H:i:s');
                $blogCategory->date_upd = date('Y-m-d H:i:s');

                if (!$blogCategory->save()) {
                    $this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
                } else {
                    Hook::exec('actionsbnewcat', array('BlogCategory' => $blogCategory));
                    $this->processImageCategory($_FILES, $blogCategory->id);
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminBeesBlogCategory'));
                }
            } elseif ($idBeesBlogCategory = Tools::getValue('id_bees_blog_category')) {
                $blogCategory = new BeesBlogCategory($idBeesBlogCategory);
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $title = str_replace('"', '', htmlspecialchars_decode(html_entity_decode(Tools::getValue('meta_title_'.$language['id_lang']))));
                    $blogCategory->meta_title[$language['id_lang']] = $title;
                    $blogCategory->meta_keyword[$language['id_lang']] = Tools::getValue('meta_keyword_'.$language['id_lang']);
                    $blogCategory->meta_description[$language['id_lang']] = Tools::getValue('meta_description_'.$language['id_lang']);
                    $blogCategory->description[$language['id_lang']] = Tools::getValue('description_'.$language['id_lang']);
                    $blogCategory->link_rewrite[$language['id_lang']] = str_replace(array(
                        ' ',
                        ':',
                        '\\',
                        '/',
                        '#',
                        '!',
                        '*',
                        '.',
                        '?',
                    ), '-', Tools::getValue('link_rewrite_'.$language['id_lang']));
                }

                $blogCategory->id_parent = Tools::getValue('id_parent');
                $blogCategory->position = Tools::getValue('position');
                $blogCategory->desc_limit = Tools::getValue('desc_limit');
                $blogCategory->active = Tools::getValue('active');
                $blogCategory->date_upd = date('y-m-d H:i:s');
                if (!$blogCategory->update()) {
                    $this->errors[] = Tools::displayError('An error occurred while updating an object.')
                        .' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {
                    Hook::exec('actionsbupdatecat', array('BlogCategory' => $blogCategory));
                }
                $this->processImageCategory($_FILES, $blogCategory->id_bees_blog_category);
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminBeesBlogCategory'));
            }
        } elseif (Tools::isSubmit('statusbees_blog_category') && Tools::getValue($this->identifier)) {
            if ($this->tabAccess['edit'] === '1') {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    if ($object->toggleStatus()) {
                        Hook::exec('actionsbtogglecat', array('BeesBlogCat' => $this->object));
                        $identifier = ((int) $object->id_parent ? '&id_bees_blog_category='.(int) $object->id_parent : '');
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminBeesBlogCategory'));
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred while updating the status.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('bees_blog_categoryOrderby') && Tools::isSubmit('bees_blog_categoryOrderway')) {
            $this->_defaultOrderBy = Tools::getValue('bees_blog_categoryOrderby');
            $this->_defaultOrderWay = Tools::getValue('bees_blog_categoryOrderway');
        }
    }

    /**
     * Process category image
     *
     * @param array $files
     * @param int   $id
     *
     * @return string
     */
    public function processImageCategory($files, $id)
    {

        if (isset($files['category_image']) && isset($files['category_image']['tmp_name']) && !empty($files['category_image']['tmp_name'])) {
            if ($error = ImageManager::validateUpload($files['category_image'], 4000000)) {
                return $this->errors[] = $this->l('Invalid image');
            } else {
                $ext = substr($files['category_image']['name'], strrpos($files['category_image']['name'], '.') + 1);
                $fileName = $id.'.'.$ext;
                $path = _PS_MODULE_DIR_.'beesblog/images/category/'.$fileName;
                if (!move_uploaded_file($files['category_image']['tmp_name'], $path)) {
                    return $this->errors[] = $this->l('An error occurred while attempting to upload the file.');
                } else {
                    if (Configuration::hasContext('category_image', null, Shop::getContext())
                        && Configuration::get('BLOCKBANNER_IMG') != $fileName) {
                        @unlink(dirname(__FILE__).'/'.Configuration::get('BLOCKBANNER_IMG'));
                    }

                    $imageTypes = BeesBlogImageType::getAllImagesFromType('category');
                    foreach ($imageTypes as $imageType) {
                        $dir = _PS_MODULE_DIR_.'beesblog/images/category/'.$id.'-'.stripslashes($imageType['type_name']).'.jpg';
                        if (file_exists($dir)) {
                            unlink($dir);
                        }
                    }
                    foreach ($imageTypes as $imageType) {
                        ImageManager::resize(
                            $path,
                            _PS_MODULE_DIR_.'beesblog/images/category/'.$id.'-'.stripslashes($imageType['type_name']).'.jpg',
                            (int) $imageType['width'],
                            (int) $imageType['height']
                        );
                    }
                }
            }
        }
    }
}
