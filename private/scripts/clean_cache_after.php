<?php
echo "Rebuilding cache after.\n";
passthru('drush cr');
echo "Rebuilding cache complete.\n";
