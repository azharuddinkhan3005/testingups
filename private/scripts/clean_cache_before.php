<?php
//Clear all cache
echo "Rebuilding cache before.\n";
passthru('drush cr');
echo "Rebuilding cache complete.\n";
