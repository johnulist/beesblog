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
 * Class AdminBeesBlogImageTypeController
 */
class AdminBeesBlogImageTypeController extends ModuleAdminController
{
    /**
     * AdminBeesBlogImageTypeController constructor.
     */
    public function __construct()
    {
        $this->table = 'bees_blog_imagetype';
        $this->className = 'BeesBlogImageType';
        $this->module = 'beesblog';
        $this->lang = false;
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->fields_list = array(
            'id_bees_blog_imagetype' => array(
                'title' => $this->l('Id'),
                'width' => 100,
                'type' => 'text',
            ),
            'type_name' => array(
                'title' => $this->l('Type Name'),
                'width' => 350,
                'type' => 'text',
            ),
            'width' => array(
                'title' => $this->l('Width'),
                'width' => 60,
                'type' => 'text',
            ),
            'height' => array(
                'title' => $this->l('Height'),
                'width' => 60,
                'type' => 'text',
            ),
            'type' => array(
                'title' => $this->l('Type'),
                'width' => 220,
                'type' => 'text',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'width' => 60,
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
            ),
        );
        parent::__construct();
    }

    /**
     * Render form
     *
     * @return string|void
     */
    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Blog Category'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Image Type Name'),
                    'name' => 'type_name',
                    'size' => 60,
                    'required' => true,
                    'desc' => $this->l('Enter Your Image Type Name Here'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('width'),
                    'name' => 'width',
                    'size' => 15,
                    'required' => true,
                    'desc' => $this->l('Image height in px'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Height'),
                    'name' => 'height',
                    'size' => 15,
                    'required' => true,
                    'desc' => $this->l('Image height in px'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 'post',
                                'name' => 'Post',
                            ),
                            array(
                                'id_option' => 'Category',
                                'name' => 'category',
                            ),
                            array(
                                'id_option' => 'Author',
                                'name' => 'author',
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
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

        if (!($blogImageType = $this->loadObject(true))) {
            return;
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
            'class' => 'button',
        );

        return parent::renderForm();
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
}
