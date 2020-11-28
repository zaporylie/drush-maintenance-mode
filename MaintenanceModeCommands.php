<?php

namespace Drush\Commands\drush_maintenance_mode;

use Consolidation\AnnotatedCommand\CommandData;
use Drush\Commands\DrushCommands;

/**
 * Class MaintenanceModeCommands.
 *
 * @package Drush\Commands\drush_maintenance_mode
 */
class MaintenanceModeCommands extends DrushCommands {

  /**
   * The state.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs an allowed in maintenance mode validation service.
   */
  public function initVariables() {
    $this->state = \Drupal::state();
  }

  /**
   * @hook validate *
   */
  public function validateCommandDeployment(CommandData $commandData) {

    $this->initVariables();

    if (!$this->state->get('system.maintenance_mode')) {
      return TRUE;
    }
    if (!in_array($commandName = $commandData->annotationData()->get('command'), ['core:status'])) {
      throw new \Exception(sprintf("You cannot use command %s when site is in maintenance mode", $commandName));
    }
  }

}
