<?php

namespace Drush\Commands\drush_maintenance_mode;

use Consolidation\AnnotatedCommand\CommandData;
use Drush\Commands\DrushCommands;
use Drush\Drush;
use Symfony\Component\Yaml\Yaml;

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
  public function validateCommand(CommandData $commandData) {
    // Drupal must be fully bootstraped in order to use this validation.
    $boot_manager = Drush::bootstrapManager();
    if (!$boot_manager->hasBootstrapped(DRUSH_BOOTSTRAP_DRUPAL_FULL)) {
      return TRUE;
    }

    // Validation callback must be run in context of Drupal instance.
    $drupalFinder = new \DrupalFinder\DrupalFinder();
    if (!$drupalFinder->locateRoot(getcwd())) {
      return TRUE;
    }

    // Validation callback must be able to locate project/composer root.
    if (!$composerRoot = $drupalFinder->getComposerRoot()) {
      return TRUE;
    }

    // Initialize member variables.
    $this->initVariables();

    // Validation only runs when the site is in the maintenance mode.
    if (!$this->state->get('system.maintenance_mode')) {
      return TRUE;
    }

    // Validation callback must be able to locate configuration file.
    if (!file_exists($filename = $composerRoot . '/.drush-maintenance-mode.yml')) {
      throw new \Exception(sprintf('%s file is missing, you will not be able to use drush during maintenance mode', $filename));
    }

    $config = Yaml::parseFile($filename);
    if (!isset($config['commands']['allowed']) || !is_array($config['commands']['allowed'])) {
      throw new \Exception(sprintf('%s config file is missing required property commands.allowed', $filename));
    }

    $allowedCommands = array_keys($config['commands']['allowed']);
    if ($commandData->output()->isVerbose()) {
      $this->logger()->debug('Only following commands are allowed in the maintenance mode: {list}', ['list' => implode(',', $allowedCommands)]);
    }

    if (!in_array($commandName = $commandData->annotationData()->get('command'), $allowedCommands)) {
      throw new \Exception(sprintf("You cannot use command %s when site is in maintenance mode", $commandName));
    }
  }

}
