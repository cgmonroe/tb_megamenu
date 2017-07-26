<?php
namespace Drupal\tb_megamenu\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\tb_megamenu\Entity\MegaMenuConfig;

/**
 * Form handler for adding MegaMenuConfig entities.
 */
class MegaMenuAdd extends EntityForm {

  /**
   * Constructs an MegaMenuAdd object.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The entity query.
   */
  public function __construct(QueryFactory $entity_query) {
    $this->entityQuery = $entity_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('entity.query')
        );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $menus = menu_ui_get_menus();

    $info = \Drupal::service('theme_handler')->listInfo();
    $themes = [];
    foreach ($info as $name => $theme) {
      if ( !isset($theme->info['hidden']) ) {
        $themes[$name] = $theme->info['name'];
      }
    }
    $config = \Drupal::config('system.theme');
    $default = $config->get('default');

    $form['menu'] = array(
        '#type' => 'select',
        '#options' => $menus,
        '#title' => $this->t('Menu'),
        '#maxlength' => 255,
        '#default_value' => NULL,
        '#description' => $this->t("Drupal Menu to use for the Mega Menu."),
        '#required' => TRUE,
    );
    $form['theme'] = array(
        '#type' => 'select',
        '#options' => $themes,
        '#title' => $this->t('Theme'),
        '#maxlength' => 255,
        '#default_value' => $default,
        '#description' => $this->t("Drupal Theme associated with this Mega Menu."),
        '#required' => TRUE,
    );
    $form['id'] = array(
      '#type' => 'value',
      '#value' => '',
    );
    $form['block_config'] = [
      '#type' => 'value',
      '#value' => NULL,
    ];
    $form['menu_config'] = [
        '#type' => 'value',
        '#value' => NULL,
    ];

    // You will need additional form elements for your custom properties.
    return $form;
  }

  /**
   *
   * {@inheritDoc}
   *
   * @see \Drupal\Core\Form\FormBase::validateForm()
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if ( MegaMenuConfig::loadMenu($form_state->getValue('menu'), $form_state->getValue('theme')) !== NULL ) {
      $form_state->setErrorByName('menu', $this->t("A Mega Menu has already been created for @menu / @theme", [
       '@menu' => $form_state->getValue('menu'),
       '@theme' => $form_state->getValue('theme'),
      ]));
    }
  }

  /**
   *
   * {@inheritDoc}
   *
   * @see \Drupal\Core\Entity\EntityForm::submitForm()
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getValue('menu') . '__' . $form_state->getValue('theme');
    $form_state->setValue('id', $id );

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $megamenu = $this->entity;
    $status = $megamenu->save();

    if ($status) {
      drupal_set_message($this->t('Created the %label Mega Menu, edit it to configure.', [
        '%label' => $megamenu->menu
      ]));
    }
    else {
      drupal_set_message($this->t('The %label Example was not saved.', [
        '%label' => $megamenu->menu,
      ]));
    }

    $form_state->setRedirect('entity.tb_megamenu.edit_form', ['tb_megamenu' => $megamenu->id()]);
  }

  /**
   * Helper function to check whether an Example configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityQuery->get('example')
    ->condition('id', $id)
    ->execute();
    return (bool) $entity;
  }
}
