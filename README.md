Drush Maintenance Mode
====

Restricts list of drush commands that are allowed to run when the site is in maintenance mode.

# Installation

```bash
composer require zaporylie/drush-maintenance-mode
```

# Configuration

Create a file `.drush-maintenance-mode.yml` in project's root directory (composer root). List all commands (by command
name) under `commands.allowed` property. Command names should be used as keys.

Example:
```yaml
commands:
  allowed:
    // Preview site state.
    'core:status':
    // Clear cache.
    'cache:clear':
    // Take the site out of maintenance mode using drush.
    'state:set':
    // Update database.
    'updatedb':
    'updatedb:status':
    // Export configuration split.
    'config-split:export':
    // Import configuration.
    'config-split:import':
    'config:import':
    // Import translations.
    'locale:update':
    // Used internally by bunch of commands above.
    'batch:process':
```
