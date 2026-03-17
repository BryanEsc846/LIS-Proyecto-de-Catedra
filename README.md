# LIS-Proyecto-de-Catedra
Repositorio para gestionar el backend del proyecto de catedra
### 1- Cargar el script de la base de datos en myphpadmin
### 2- Ingresar en la tabla "usuario" un usuario con el rol de administrador. Para la contraseña utilizar la funcion password_hash()funcion de php que se encuentra en el desplegable de funciones
### 3- Ingresar al login (de la carpeta auth) desde el apache de xampp con los datos que ingreso en la base de datos.
### 4- Registre a un estudiante 

# Guía de Despliegue: Aplicación Web PHP en Kubernetes

## Requisitos Previos

Antes de comenzar, asegúrate de tener instaladas las siguientes herramientas:
* **Docker Desktop:** Activo y ejecutándose.
* **Kind:** Para la creación del clúster multinodo local.
* **kubectl:** Herramienta de línea de comandos para interactuar con el clúster.

---

## Pasos de Ejecución

### 1. Empaquetar la Aplicación (Docker)
Construye la imagen de Docker a partir del código fuente y el `Dockerfile` ubicado en la raíz del proyecto.

```bash
docker build -t imagen_proyecto_catedra:latest .
```

### 2. Crear el Clúster Multinodo (Kind)
Crea un clúster local con múltiples nodos (1 maestro, 2 trabajadores) para garantizar la alta disponibilidad y la distribución de réplicas.

```bash
kind create cluster --config kind-config.yaml --name proyecto-catedra
```

### 3. Cargar la Imagen al Clúster
Inyecta la imagen de Docker recién creada directamente en los nodos del clúster para que esté disponible localmente.

```bash
kind load docker-image imagen_proyecto_catedra:latest --name proyecto-catedra
```
### 4. Configurar el Servidor de Métricas (Requisito para HPA)
Para que el Autoescalador Horizontal (HPA) funcione, es necesario habilitar la recolección de métricas de CPU y aplicar un parche de seguridad.

```bash
# Instalar el Metrics Server
kubectl apply -f [https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml](https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml)

# Aplicar parche para ignorar certificados TLS locales
kubectl patch deployment metrics-server -n kube-system --type=json --patch-file parche.json
```

### 5. Desplegar la Infraestructura en Kubernetes
Aplica el archivo de configuración maestro que contiene el Deployment, el Service (LoadBalancer) y el HorizontalPodAutoscaler.

```bash
kubectl apply -f k8s-config.yaml
```

### 6. Exponer la Aplicación (Port-Forward)
Abre un puente de red para conectar el balanceador de carga del clúster con tu entorno local. **Este comando debe mantenerse en ejecución en la terminal.**

```bash
kubectl port-forward service/php-app-loadbalancer 8080:80
```
