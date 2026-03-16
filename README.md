# Examen Pasteles

Proyecto desarrollado con Vue, PHP y MySQL para la gestion de pasteles e ingredientes.

## Tecnologias utilizadas
- Vue 3
- Vite
- PHP
- MySQL
- PDO

## Estructura del proyecto
- `frontend/`: interfaz en Vue
- `backend/`: API en PHP
- `database/script.sql`: script de base de datos

## Pasos para ejecutar el proyecto

### 1. Base de datos
Ejecutar el archivo `database/script.sql` en MySQL para crear la base de datos y sus tablas.

### 2. Backend
Desde la raíz del proyecto ejecutar:

php -S localhost:8000 -t backend

### 3. Frontend
Ingresar a la carpeta de frontend y ejecutar:

npm install
npm run dev

### Funcionalidades 

CRUD de ingredientes
CRUD de pasteles
Asociacion y desasociacion de ingredientes
Reporte general de pasteles con ingredientes