<?php

namespace Drupal\Tests\scheduler_content_moderation_integration\Kernel;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\system\Entity\Action;
use Drupal\workflows\Entity\Workflow;

/**
 * Base class for the Scheduler Content Moderation tests.
 */
abstract class SchedulerContentModerationTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'action',
    'content_moderation',
    'datetime',
    'field',
    'language',
    'node',
    'options',
    'scheduler',
    'scheduler_content_moderation_integration',
    'system',
    'user',
    'workflows',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installSchema('node', 'node_access');
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('content_moderation_state');
    $this->installConfig('content_moderation');

    // Scheduler calls some config entity, instead of installing whole modules
    // default config just create the ones we need..
    DateFormat::create([
      'id' => 'long',
      'label' => 'Custom long date',
      'pattern' => 'l, F j, Y - H:i',
    ])->save();
    Action::create([
      'id' => 'node_publish_action',
      'label' => 'Custom node_publish_action',
      'type' => 'node',
      'plugin' => 'entity:publish_action:node',
    ])->save();
    Action::create([
      'id' => 'node_unpublish_action',
      'label' => 'Custom node_unpublish_action',
      'type' => 'node',
      'plugin' => 'entity:unpublish_action:node',
    ])->save();

    $this->configureExampleNodeType();
    $this->configureEditorialWorkflow();
  }

  /**
   * Configure example node type.
   */
  protected function configureExampleNodeType() {
    $node_type = NodeType::create([
      'type' => 'example',
    ]);
    $node_type->setThirdPartySetting('scheduler', 'publish_enable', TRUE);
    $node_type->setThirdPartySetting('scheduler', 'unpublish_enable', TRUE);
    $node_type->save();
  }

  /**
   * Configures the editorial workflow for the example node type.
   */
  protected function configureEditorialWorkflow() {
    $workflow = Workflow::load('editorial');
    $workflow->getTypePlugin()->addEntityTypeAndBundle('node', 'example');
    $workflow->save();
  }

}
