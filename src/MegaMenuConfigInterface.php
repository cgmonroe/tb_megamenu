<?php
namespace Drupal\tb_megamenu;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining an megamenu config entity.
 */
interface MegaMenuConfigInterface extends ConfigEntityInterface {

  /**
   * Sets both the menu property and the first part of the id is it is not set.
   *
   * @param string $menuName
   */
  public function setMenu($menuName);

  /**
   * Sets both the theme property and the second part of the id if it is not set.
   *
   * @param string $themeName
   */
  public function setTheme($themeName);

  /**
   * Gets the json decoded block configuration
   *
   * @return \stdClass
   * A class with properties for the block configuration settings.
   */
  public function getBlockConfig();

  /**
   * Converts the stdClass properties to json and sets the blockConfig property
   *
   * @param mixed $blockConfig
   */
  public function setBlockConfig($blockConfig);

  /**
   * Gets the json decoded menu configuration
   *
   * @return \stdClass
   * A class with properties for the menu configuration settings.
   */
  public function getMenuConfig();

  /**
   * Converts the stdClass config to json and sets the menu property.
   *
   * @param mixed $menuConfig
   */
  public function setMenuConfig($menuConfig);

  /**
   * Loads the configuration info for the specified menu and theme
   *
   * @param string $menu
   * @param string $theme
   *
   * @return MegaMenuConfigInterface
   * Returns the config object or NULL if not found.
   */
  public static function loadMenu($menu, $theme);
}
