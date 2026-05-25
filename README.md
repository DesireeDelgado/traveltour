# Guía de despliegue

Para poner en marcha el proyecto, siga estos pasos:

1. Copiar .env.example a .env.
2. Ejecutar docker compose up -d.
3. Ejecutar composer install.
4. Ejecutar npm install
5. Ejecutar php bin/console doctrine:migrations:migrate y php bin/console doctrine:fixtures:load.


---

## Acceso a la aplicación
Una vez completados los pasos anteriores, la aplicación estará disponible en:
**http://localhost:8000**