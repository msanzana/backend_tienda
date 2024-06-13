php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:cache
php artisan optimize
php artisan event:clear
@echo off
echo ###########################################
echo ##       Se ha limpiado la cache         ##
echo ##     Ahora se levantara el servidor    ##
echo ###########################################
php artisan serve