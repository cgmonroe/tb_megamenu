<?php

/**
 * @file
 * Contains \Drupal\tb_megamenu\Controller\TBMegaMenuAdminController.
 */

namespace Drupal\tb_megamenu\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\tb_megamenu\TBMegaMenuBuilder;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\tb_megamenu\Entity\MegaMenuConfig;


class TBMegaMenuAdminController extends ControllerBase {

  /**
   * This is menu callback. Save configuration of TB Mega Menu.
   */
  public function saveConfiguration() {
    $action = isset($_POST['action']) ? $_POST['action'] : NULL;
    $result = '';
    switch ($action) {
      case 'load':
        $renderable_array = TBMegaMenuBuilder::renderBlock($_POST['menu_name'], $_POST['theme']);
        $result = \Drupal::service('renderer')
          ->render($renderable_array)
          ->__toString();
        break;

      case 'save':
        $menu_config = isset($_POST['menu_config']) ? $_POST['menu_config'] : NULL;
        $block_config = isset($_POST['block_config']) ? $_POST['block_config'] : NULL;
        $menu_name = isset($_POST['menu_name']) ? $_POST['menu_name'] : NULL;
        $theme = isset($_POST['theme']) ? $_POST['theme'] : NULL;
        if ($menu_config && $menu_name) {
          // This is parameter to load menu_tree with the enabled links.
          $menu_tree_parameters = (new MenuTreeParameters)->onlyEnabledLinks();
          // Load menu items with condition.
          $menu_items = \Drupal::menuTree()->load($menu_name, $menu_tree_parameters);
          // Sync mega menu before store.
          TBMegaMenuBuilder::syncConfigAll($menu_items, $menu_config, 'backend');
          TBMegaMenuBuilder::syncOrderMenus($menu_config);

          $config = MegaMenuConfig::loadMenu($menu_name, $theme);
          if ( $config === NULL ) {
            drupal_set_message("Cannot create a new config object in save!");
            return;
          }
          $config->setBlockConfig($block_config);
          $config->setMenuConfig($menu_config);
          $result = $config->save();
        }
        break;

      case 'load_block':
        $block_id = isset($_POST['block_id']) ? $_POST['block_id'] : NULL;
        $id = isset($_POST['id']) ? $_POST['id'] : NULL;
        $showblocktitle = isset($_POST['showblocktitle']) ? $_POST['showblocktitle'] : NULL;
        if ($block_id) {
          $render = array(
            '#theme' => 'tb_megamenu_block',
            '#block_id' => $block_id,
            '#section' => 'backend',
            '#showblocktitle' => $showblocktitle
          );
          $content = \Drupal::service('renderer')
            ->render($render)
            ->__toString();
          $result = json_encode(array('content' => $content, 'id' => $id));
        }
        break;

      default:
        break;
    }

    return new Response($result);
  }

  /**
   * This is a menu page. To edit Mega Menu.
   */
  public function configMegaMenu(ConfigEntityInterface $tb_megamenu, Request $request) {
    // Add font-awesome library.
    $page['#attached']['library'][] = 'tb_megamenu/form.font-awesome';
    // Add chosen library.
    $page['#attached']['library'][] = 'tb_megamenu/form.chosen';
    // Add a custom library.
    $page['#attached']['library'][] = 'tb_megamenu/form.configure-megamenu';

    $abs_url_config = \Drupal::url('tb_megamenu.admin.save', array(), array('absolute' => TRUE));
    $page['#attached']['drupalSettings']['TBMegaMenu']['saveConfigURL'] = $abs_url_config;
    if (!empty($tb_megamenu)) {
      $page['tb_megamenu'] = array(
        '#theme' => 'tb_megamenu_backend',
        '#menu_name' => $tb_megamenu->menu,
      );
    }
    return $page;
  }

}
