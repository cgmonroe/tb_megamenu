<?php
namespace Drupal\tb_megamenu\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\tb_megamenu\MegaMenuConfigInterface;

/**
 * Defines the Mega Menu Configuration entity.
 *
 * @ConfigEntityType(
 *   id = "tb_megamenu",
 *   label = @Translation("TB Mega Menu"),
 *   handlers = {
 *     "list_builder" = "Drupal\tb_megamenu\Controller\MegaMenuList",
 *     "form" = {
 *       "add" = "Drupal\tb_megamenu\Form\MegaMenuAdd",
 *       "edit" = "Drupal\tb_megamenu\Form\MegaMenuAdd",
 *       "delete" = "Drupal\tb_megamenu\Form\MegaMenuDelete",
 *     }
 *   },
 *   config_prefix = "menu_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "menu" = "menu",
 *   },
 *   links = {
 *     "edit-form" = "/admin/structure/tb-megamenu/{tb_megamenu}",
 *     "delete-form" = "/admin/structure/tb-megamenu/{tb_megamenu}/delete",
 *   }
 * )
 */
class MegaMenuConfig extends ConfigEntityBase implements MegaMenuConfigInterface {

  /**
   * The MegaMenu ID
   *
   * @var string
   */
  public $id;

  /**
   * The related menu machine name.
   *
   * @var string
   */
  public $menu;

  /**
   * The related theme name
   *
   * @var string
   */
  public $theme;

  /**
   * The json encoded string of block settings
   *
   * @var string
   */
  public $block_config;

  /**
   * The json encoded string of menu settings.
   *
   * @var string
   */
  public $menu_config;

  /**
   *
   * {@inheritDoc}
   * @see \Drupal\tb_megamenu\MegaMenuConfigInterface::setMenu()
   */
  public function setMenu($menuName) {
    if (!isset($this->id)) {
      $this->id = $menuName;
    }
    $this->menu = $menuName;
  }

  /**
   *
   * {@inheritDoc}
   * @see \Drupal\tb_megamenu\MegaMenuConfigInterface::setTheme()
   */
  public function setTheme($themeName) {
    if (isset($this->id) && isset($this->menu) && $this->id == $this-menu ) {
      $this->id = $this->id . '__' . $themeName;
    }
    $this->theme = $themeName;
  }

  /**
   *
   * {@inheritDoc}
   * @see \Drupal\tb_megamenu\MegaMenuConfigInterface::getBlockConfig()
   */
  public function getBlockConfig() {
    return isset($this->block_config) ? json_decode($this->block_config, true) : array();
  }

  /**
   *
   * {@inheritDoc}
   * @see \Drupal\tb_megamenu\MegaMenuConfigInterface::getMenuConfig()
   */
  public function getMenuConfig() {
    return isset($this->menu_config) ? json_decode($this->menu_config, true) : array();
  }

  /**
   *
   * {@inheritDoc}
   * @see \Drupal\tb_megamenu\MegaMenuConfigInterface::setBlockConfig()
   */
  public function setBlockConfig(\stdClass $blockConfig) {
    $this->block_config = json_encode($blockConfig);
  }

  /**
   *
   * {@inheritDoc}
   * @see \Drupal\tb_megamenu\MegaMenuConfigInterface::setMenuConfig()
   */
  public function setMenuConfig(\stdClass $menuConfig) {
    $this->menu_config = json_encode($menuConfig);
  }

  /**
   *
   * {@inheritDoc}
   * @see \Drupal\tb_megamenu\MegaMenuConfigInterface::loadMenu()
   */
  public static function loadMenu($menu, $theme) {
    $id = "{$menu}__{$theme}";
    $config = self::load($id);
    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();
    $this->addDependency('menu', $this->menu);
    $this->addDependency('theme', $this->theme);
    return $this;
  }
}
