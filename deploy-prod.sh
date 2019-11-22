#!/usr/bin/env bash

# récupérer le code source
git pull origin master

# install les dépendances
composer install --no-dev

# vider le cache
drush cr

# màj de BDD
drush updb -y

# exporter la config prod
drush csex prod -y

# import les configs de dév
drush cim -y

# vider le cache
drush cr
