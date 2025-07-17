# Run as root to ensure permissions
docker-compose exec -u root app bash -c '
  chown -R www-data:www-data /var/www
  chmod -R 755 /var/www
  chmod -R 775 /var/www/storage
  chmod -R 775 /var/www/bootstrap/cache
  chmod -R 775 /var/www/vendor
'

# Now run composer as www-data
docker-compose exec -u www-data app composer install

