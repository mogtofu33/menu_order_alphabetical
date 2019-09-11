<?php

namespace Drupal\Tests\menu_order_alphabetical\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Test module form.
 *
 * @group menu_order_alphabetical
 */
class MenuOrderAlphabeticalFormTest extends BrowserTestBase {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'menu_link_content',
    'menu_ui',
    'menu_order_alphabetical',
  ];

  /**
   * An user with admin permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->adminUser = $this
      ->drupalCreateUser([
        'administer menu',
        'link to any page',
      ]);
  }

  /**
   * Save, edit and delete a menu using the user interface.
   */
  public function testMenuReorder() {
    // Login.
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/structure/menu/manage/main');

    // Verify that the order alphabetical button is here.
    $this->assertSession()->buttonExists('Reset to alphabetical');
    
    // Create menus in a random order.
    $menu_link1 = $this->createMenuLink('A menu', 3);
    $menu_link2 = $this->createMenuLink('B menu', 4);
    $menu_link3 = $this->createMenuLink('C menu', 1);
    $menu_link4 = $this->createMenuLink('D menu', 0);
    $menu_link5 = $this->createMenuLink('E menu', 2);

    $this->drupalPostForm('admin/structure/menu/manage/main', [], t('Reset to alphabetical'));
    // Submit confirmation form.
    $this->drupalPostForm(NULL, [], t('Reset to alphabetical'));
    // Ensure form redirected back to overview.
    $this->assertUrl('admin/structure/menu/manage/main');

    // Check that all links are with weight 0.
    $links = $this->xpath('//tr/select[@class="menu-weight"]');
    foreach ($links as $link) {
      $this->assertTrue($link->getWeight() == 0);
    }
  }

  /**
   * Returns a new menu link content.
   *
   * @param string $title
   *   The menu title.
   * @param int $weight
   *   (optional) The menu weight, default 0.
   * @param string $uri
   *   (optional) The menu uri, default front page.
   * @param string $menu
   *   (optional) The menu name, default main.
   *
   * @return \Drupal\menu_link_content\Entity\MenuLinkContent
   *   The new menu link content object.
   */
  private function createMenuLink($title, $weight = 0, $uri = 'internal:/', $menu = 'main') {
    $menu_link = MenuLinkContent::create([
      'title' => $title,
      'provider' => 'menu_link_content',
      'menu_name' => $menu,
      'link' => ['uri' => $uri],
      'weight' => $weight,
    ]);
    $menu_link->save();
    return $menu_link;
  }

}
