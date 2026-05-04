# 🏫 Sistema de Gestión Escolar - C.E. Candelario Cuellar

Este repositorio contiene el código fuente del **Sistema de Gestión Escolar** desarrollado para el Centro Escolar Candelario Cuellar. Es una plataforma web diseñada para agilizar y digitalizar los procesos administrativos y académicos de la institución.

🌍 **Sitio web en vivo (Demo):** [ce-candelariocuellar.rf.gd](http://ce-candelariocuellar.rf.gd)

---

## 🚀 Características Principales

El sistema está compuesto por los siguientes módulos transaccionales:

* **Autenticación y Seguridad:** Control de acceso mediante roles (Administrador y Docente) con contraseñas encriptadas (bcrypt).
* **Gestión de Matrícula:** Registro de alumnos, asignación a grados y almacenamiento seguro de documentos (Partidas de Nacimiento en PDF/Imagen).
* **Gestión Académica:** Administración de personal docente, grados académicos (1° a 9°) y materias.
* **Gestión de Horarios:** Creación y visualización de matrices de horarios escolares.
* **Registro de Calificaciones:** Consolidación de notas por trimestre (actividades y exámenes) con cálculo automático de promedios.

---

## 💻 Tecnologías Utilizadas

El proyecto fue desarrollado utilizando el siguiente stack tecnológico:

* **Backend:** PHP 8.0.30
* **Base de Datos:** MySQL (Cliente libmysql - mysqlnd 8.0.30)
* **Gestor de BD:** phpMyAdmin
* **Servidor Web:** Apache/2.4.58 (Win64) con soporte OpenSSL/3.1.3
* **Frontend:** HTML5, CSS3, Bootstrap 5.3.2
* **Arquitectura:** Programación Estructurada (vistas) y Programación Orientada a Objetos (gestión de base de datos con PDO y Transacciones).

---

## ⚙️ Requisitos Previos

Para ejecutar este proyecto en un entorno local, necesitarás:

1.  Un entorno de servidor local como **XAMPP**, **WAMP** o **Laragon**.
2.  PHP versión 8.0 o superior.
3.  Servidor MySQL activo.
4.  Git instalado en tu equipo.

---

## 🛠️ Instalación y Configuración Local

Sigue estos pasos para desplegar el proyecto en tu máquina local:

## 1. Clonar el repositorio
Abre tu terminal, dirígete a la carpeta pública de tu servidor local (ej. `htdocs` en XAMPP o `www` en WAMP) y clona el proyecto:
```bash
git clone [https://github.com/BryanEsc846/LIS-Proyecto-de-Catedra.git](https://github.com/BryanEsc846/LIS-Proyecto-de-Catedra.git) CECandelarioCuellar
```

## 2. Configurar la Base de Datos

Abre phpMyAdmin en tu navegador (usualmente http://localhost/phpmyadmin).

Crea una nueva base de datos llamada **c.e.c.c** (con cotejamiento `utf8mb4_unicode_ci`).

Ve a la pestaña **Importar** y selecciona el archivo principal de la base de datos ubicado en la carpeta del proyecto: /database/base.sql


**(Opcional)** Si deseas datos de prueba adicionales, puedes importar luego:

- `alumnos.sql`
- `maestros.sql`

---

## 3. Configurar la conexión

Asegúrate de que las credenciales de conexión a la base de datos coincidan con tu entorno local. Revisa el archivo: /config/conexion.php

```php
$host = 'localhost';
$db   = 'c.e.c.c';
$user = 'root'; // Tu usuario de MySQL
$pass = '';     // Tu contraseña de MySQL
