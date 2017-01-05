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
 * Class AdminBeesBlogCommentController
 */
class AdminBeesBlogCommentController extends ModuleAdminController
{
    public $assoType = 'shop';

    /**
     * AdminBeesBlogCommentController constructor.
     */
    public function __construct()
    {
        $this->table = 'bees_blog_comment';
        $this->className = 'BeesBlogComment';
        $this->module = 'beesblog';
        $this->context = Context::getContext();
        $this->bootstrap = true;
        if (Shop::isFeatureActive()) {
            Shop::addTableAssociation($this->table, array('type' => 'shop'));
        }
        parent::__construct();

        $this->fields_list = array(
            'id_bees_blog_comment' => array(
                'title' => $this->l('Id'),
                'width' => 100,
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 150,
                'type' => 'text',
            ),
            'content' => array(
                'title' => $this->l('Comment'),
                'width' => 340,
                'type' => 'text',
            ),
            'created' => array(
                'title' => $this->l('Date'),
                'width' => 60,
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

        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'bees_blog_comment_shop sbs ON a.id_bees_blog_comment=sbs.id_bees_blog_comment && sbs.id_shop IN('.implode(',', Shop::getContextListShopID()).')';

        $this->_select = 'sbs.id_shop';
        $this->_defaultOrderBy = 'a.id_bees_blog_comment';
        $this->_defaultOrderWay = 'DESC';

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->_group = 'GROUP BY a.id_bees_blog_comment';
        }

        parent::__construct();
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
     * @return string|void
     */
    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Blog Comment'),
            ),
            'input' => array(
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Comment'),
                    'name' => 'content',
                    'rows' => 10,
                    'cols' => 62,
                    'class' => 'rte',
                    'autoload_rte' => false,
                    'required' => false,
                    'desc' => $this->l('Enter Your Category Description'),
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

        if (!($Blogcomment = $this->loadObject(true))) {
            return;
        }

        return parent::renderForm();
    }
}
