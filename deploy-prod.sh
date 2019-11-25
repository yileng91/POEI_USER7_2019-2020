#!/usr/bin/env bash

#mysqldump -h host -u user -ppass -rfichier base_de_donnees [tables]

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
git add config/prod
git commit -m 'Ajout des configs de prod'
git push origin master

# import les configs de dév
drush cim -y

# vider le cache
drush cr

#sudo bash fix-permissions.sh --drupal_path=your/drupal/path --drupal_user=your_user_name
bash fix-permissions.sh --drupal_path=web --drupal_user=www-data