# Instalación en entorno de desarrollo

## Prerrequisitos

* PHP 7.4
* MariaDB o MySQL
* Symfony 5
* Composer

Las instrucciones a continuación son para una estación de trabajo GNU/Linux. Deberían
funcionar también en una estación macOS.

## Descargar código e iniciar proyecto

```bash
git clone (URL del repo)
composer install
```

## Crear la base de datos e incorporar datos iniciales

```bash
mysql
```
```sql
CREATE DATABASE cryptodash_dev;
GRANT ALL ON cryptodash_dev.* TO 'cryptodash_dev'@'localhost' IDENTIFIED BY 'contrasenia';
exit
```
```bash
mysql < data/*.sql
cat data/*.sql | mysql --database=cryptodash_dev
```

## Iniciar el servidor de desarrollo

```bash
symfony local:server:start
```

La aplicación estará disponible en https://localhost:8000
