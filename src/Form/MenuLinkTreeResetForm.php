<?php

namespace Drupal\menu_order_alphabetical\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\system\Entity\Menu;

/**
 * Defines a confirmation form for resetting a menu.
 */
class MenuLinkTreeResetForm extends ConfirmFormBase {

  /**
   * The menu name.
   *
   * @var string
   */
  protected $menuName;

  /**
   * The parent menu id.
   *
   * @var string
   */
  protected $parent;

  /**
   * The menu entity.
   *
   * @var \Drupal\system\Entity\Menu
   */
  protected $menu;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'menu_tree_reset_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to reset the menu %title to alphabetical order?', ['%title' => $this->menu->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.menu.edit_form', [
      'menu' => $this->menuName,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('Resetting a menu will discard all custom ordering and sort items alphabetically.');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Reset to alphabetical');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $route_match = $this->getRouteMatch();
    if (($route_match->getRouteName() == 'entity.menu_ui.reset_form') && $menu = $route_match->getParameter('menu')) {
      $this->menuName = $menu;
      $this->menu = Menu::load($menu);
    }
    if ($parent = $route_match->getParameter('menu_link')) {
      $this->parent = $parent;
    }

    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Simple function to set all weights to 0.
    menu_order_alphabetical_reset($this->menuName, $this->parent);

    drupal_set_message($this->t('Reset menu %name to alphabetical order.', ['%name' => $this->menuName]));
    $this->logger('menu_ui')->notice('Reset menu %name to alphabetical order.', ['%name' => $this->menuName]);
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
