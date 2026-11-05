#!/bin/sh
set -e

echo "=== Verificando variables de entorno críticas ==="
if [ -z "$APP_KEY" ]; then
  echo "ERROR: APP_KEY no está definida. Define esta variable de entorno en Render."
  exit 1
fi

echo "=== Ejecutando migraciones de base de datos ==="
php artisan migrate --force

echo "=== Creando enlace simbólico del storage público ==="
php artisan storage:link || true

echo "=== Limpiando cachés previos ==="
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "=== Optimizando cachés de Laravel ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Iniciando servidor PHP en puerto ${PORT:-10000} ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
