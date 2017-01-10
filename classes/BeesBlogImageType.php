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
 * Class BeesBlogImageType
 */
class BeesBlogImageType extends BeesBlogObjectModel
{
    // @codingStandardsIgnoreStart
    /** @var string $type_name */
    public $type_name;

    /** @var int $width */
    public $width;

    /** @var int $height */
    public $height;

    /** @var string $type */
    public $type;

    /** @var bool $active */
    public $active;
    // @codingStandardsIgnoreEnd

    const PRIMARY = 'id_bees_blog_imagetype';
    const TABLE = 'bees_blog_imagetype';

    public static $definition = [
        'table' => self::TABLE,
        'primary' => self::PRIMARY,
        'fields' => [
            'width' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'db_type' => 'INT(11) UNSIGNED'],
            'height' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'db_type' => 'INT(11) UNSIGNED'],
            'type_name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'db_type' => 'VARCHAR(45)'],
            'type' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'db_type' => 'VARCHAR(45)'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true, 'default' => '1', 'db_type' => 'TINYINT(1)'],
        ],
    ];

    /**
     * @param string $type
     *
     * @return array|false|\mysqli_result|null|\PDOStatement|resource
     */
    public static function getAllImagesFromType($type)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE);
        $sql->where('`active` = 1');
        $sql->where('`type` = \''.pSQL($type).'\'');
        $imageType = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $imageType;
    }

    /**
     * @return array|false|\mysqli_result|null|\PDOStatement|resource
     */
    public static function getAllImageTypes()
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE);
        $sql->where('`active` = 1');
        $imageTypes = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $imageTypes;
    }

    /**
     * @param string $type
     *
     * @return array|false|\mysqli_result|null|\PDOStatement|resource
     */
    public static function getImageByType($type)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(self::TABLE);
        $sql->where('`active` = 1');
        $sql->where('`type_name` = \''.pSQL($type).'\'');
        $imageType = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $imageType;
    }

    /**
     * Generate images
     */
    public static function ImageGenerate()
    {
        $getBlogImage = BeesBlogPost::getBlogImage();
        $getCategoryImage = BeesBlogCategory::getCatImage();
        $getAuthorImage = _PS_MODULE_DIR_.'beesblog/images/avatar/avatar.jpg';
        $categoryTypes = BeesBlogImageType::getAllImagesFromType('Category');
        $postTypes = BeesBlogImageType::getAllImagesFromType('post');
        $authorTypes = BeesBlogImageType::getAllImagesFromType('Author');

        foreach ($categoryTypes as $imageType) {
            foreach ($getCategoryImage as $categoryImage) {
                $path = _PS_MODULE_DIR_.'beesblog/images/category/'.$categoryImage['id_bees_blog_category'].'.jpg';
                \ImageManager::resize(
                    $path,
                    _PS_MODULE_DIR_.'beesblog/images/category/'.$categoryImage['id_bees_blog_category'].'-'.stripslashes($imageType['type_name']).'.jpg',
                    (int) $imageType['width'],
                    (int) $imageType['height']
                );
            }
        }
        foreach ($postTypes as $imageType) {
            foreach ($getBlogImage as $blogImage) {
                $path = _PS_MODULE_DIR_.'beesblog/images/'.$blogImage['id_bees_blog_post'].'.jpg';
                \ImageManager::resize(
                    $path,
                    _PS_MODULE_DIR_.'beesblog/images/'.$blogImage['id_bees_blog_post'].'-'.stripslashes($imageType['type_name']).'.jpg',
                    (int) $imageType['width'],
                    (int) $imageType['height']
                );
            }
        }
        foreach ($authorTypes as $authorType) {
            \ImageManager::resize(
                $getAuthorImage,
                _PS_MODULE_DIR_.'beesblog/images/avatar/avatar-'.stripslashes($authorType['type_name']).'.jpg',
                (int) $authorType['width'],
                (int) $authorType['height']
            );
        }
    }

    /**
     * Delete images
     */
    public static function ImageDelete()
    {
        $getBlogImage = BeesBlogPost::getBlogImage();
        $getCategoryImage = BeesBlogCategory::getCatImage();
        $categoryTypes = BeesBlogImageType::getAllImagesFromType('category');
        $postTypes = BeesBlogImageType::getAllImagesFromType('post');
        $authorTypes = BeesBlogImageType::getAllImagesFromType('author');
        foreach ($categoryTypes as $imageType) {
            foreach ($getCategoryImage as $categoryImage) {
                $dir = _PS_MODULE_DIR_.'beesblog/images/category/'.$categoryImage['id_bees_blog_category'].'-'.stripslashes($imageType['type_name']).'.jpg';
                if (file_exists($dir)) {
                    unlink($dir);
                }
            }
        }
        foreach ($postTypes as $imageType) {
            foreach ($getBlogImage as $blogImage) {
                $dir = _PS_MODULE_DIR_.'beesblog/images/'.$blogImage['id_bees_blog_post'].'-'.stripslashes($imageType['type_name']).'.jpg';
                if (file_exists($dir)) {
                    unlink($dir);
                }
            }
        }
        foreach ($authorTypes as $imageType) {
            $dir = _PS_MODULE_DIR_.'beesblog/images/avatar/avatar-'.stripslashes($imageType['type_name']).'.jpg';
            if (file_exists($dir)) {
                unlink($dir);
            }
        }
    }
}
