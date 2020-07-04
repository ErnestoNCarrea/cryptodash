# Universidad Siglo 21
## Licenciatura en ifnromática
## Arbitraje de Criptomonedas

Alumno: Ernesto Nicolás Carrea
Legajo VINF6439
Año 2020

# Instalación en entorno de desarrollo

## Prerrequisitos

* PHP 7.4
* MariaDB o MySQL
* Symfony 5
* Composer
* Servidor o estación de trabajo GNU/Linux

Las instrucciones a continuación son para una estación de trabajo GNU/Linux. Pueden funcionar
también en macOS si se dispone de las herramientas apropiadas (por ejemplo Brew).

## Descargar código e iniciar proyecto

```bash
git clone https://gitlab.com/ErnestoNicolasCarrea/cryptodash.git
cd cryptodash
composer install
```

## Crear la base de datos e incorporar datos iniciales

```bash
mysql
```
```sql
CREATE DATABASE cryptodash_dev;
GRANT ALL ON cryptodash_dev.* TO 'cryptodash_dev'@'localhost' IDENTIFIED BY '123456';
exit
```
```bash
mysql --database=cryptodash_dev < data/cryptodash_dev.sql
```

## Ejecutar el proceso principal

El proceso principal puede ejecutarse manualmente. En un entorno de producción dicho proceso debería ejecutarse de forma programada a intervalos regulares o mediante un disparador.

Para ejecutar manualmente el proceso principal, utilice la siguiente sintáxis:

```bash
bin/console [-vv] arbitrar [par]
```

Donde `par` es el par a arbitrar, indicando los símbolos de dos divisas separados por una barra (por ejemplo BTC/ARS).

Para ver información de depuración sobre el proceso durante la ejecución agregue la opción -vv.

Por ejemplo:

```bash
bin/console -vv arbitrar BTC/ARS
```

![Captura de pantalla](https://cryptodash.vsign.com.ar/img/screenshot1.png)

### Archivo de registro

La aplicación asienta información sobre el proceso realizado en el archivo de registro ubicado en `var/log/dev.log` (`var/log/prod.log` si la aplicación estuviera en modo de producción).

## Vista web

1. Iniciar el servidor de desarrollo

```bash
symfony local:server:start
```

2. La aplicación estará disponible en https://localhost:8000


