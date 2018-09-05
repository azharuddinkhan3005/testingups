<?php
  // Installing Drupal.
  passthru('drush si standard --db-url=mysql://' . $_ENV['DB_USER'] . ':' . $_ENV['DB_PASSWORD'] . '@' . $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'] . '/' . $_ENV['DB_NAME'] . ' --db-su=' . $_ENV['DB_USER'] . ' --db-su-pw=' . $_ENV['DB_PASSWORD'] . ' --site-name="Curemint" --account-name="curemint" --account-pass="curemint" --site-mail="azharuddin.khan3005@gmail.com" --account-mail="azharuddin.khan3005@gmail.com" -y');
  //Changing the system uuid.
  passthru('drush config-set "system.site" uuid "f6a064fd-7bb6-4c91-84a8-e1a2a9478585" -y');
  // Deleting the default shortcuts.
  passthru('echo "\Drupal::entityManager()->getStorage(\'shortcut_set\')->load(\'default\')->delete();" | drush php-script -');
  // Uninstalling shortcut module.
  passthru('drush pm-uninstall shortcut -y');
  // Performing cache rebuild.
  passthru('drush cr');
  // Performing config import.
  //passthru('drush cim -y');
  // Performing config import.
  //passthru('drush cim -y');
  // Performing cache rebuild.
  //passthru('drush cr');
