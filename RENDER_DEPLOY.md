# TecnoSoluciones S.A. - Guía de Despliegue

## 🚀 Desplegar en Render.com

### Paso 1: Preparar el código

```bash
# Asegúrate de tener git
git init
git add .
git commit -m "Preparar para Render"
```

### Paso 2: Subir a GitHub

1. Crea un repositorio en GitHub
2. Sube el código:
```bash
git remote add origin https://github.com/TU_USUARIO/tecnosoluciones.git
git branch -M main
git push -u origin main
```

### Paso 3: Crear servicio en Render.com

1. **Ir a** https://render.com
2. **Crear cuenta** (gratis)
3. **Nuevo servicio** → "Web Service"
4. **Conectar GitHub**
5. **Seleccionar repositorio**
6. **Configurar:**
   - **Name:** `tecnosoluciones`
   - **Runtime:** PHP
   - **Build Command:** `composer install`
   - **Start Command:** `php -S 0.0.0.0:10000 -t public`
   - **Plan:** Free

### Paso 4: Crear Base de Datos

En Render.com:
1. **Nuevo servicio** → "PostgreSQL"
2. **Name:** `tecnosoluciones-db`
3. **Plan:** Free
4. Copiar la URL de conexión (CONNECTION STRING)

### Paso 5: Configurar Variables de Entorno

En el servicio Web, ir a **"Environment"** y agregar:

```
DB_HOST=tu-db.c.render.com
DB_USUARIO=tecnosoluciones_user
DB_PASSWORD=tu_contraseña
DB_NOMBRE=tecnosoluciones_db
DB_PUERTO=5432
URL_RAIZ=https://tecnosoluciones.onrender.com/
ENVIRONMENT=production
```

### Paso 6: Ejecutar migrations

Conectarse a PostgreSQL y ejecutar:

```sql
-- Crear tabla de sesiones
CREATE TABLE IF NOT EXISTS sesiones (
    id_sesion VARCHAR(64) PRIMARY KEY,
    datos TEXT NOT NULL,
    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expira_en TIMESTAMP NOT NULL,
    INDEX idx_expira_en (expira_en)
);

-- Importar rest de tecnosoluciones.sql
```

### 🔧 Troubleshooting

**Error: "Cookies are not enabled"**
- Esto NO sucede en Render.com
- Significa que aún estás usando ifastnet
- Copia la URL de Render cuando esté listo

**Error: "Connection refused"**
- Espera a que PostgreSQL esté listo (3-5 minutos)
- Verifica las credenciales en Environment

**Aplicación vacía**
- Render tarda en compilar (5-10 minutos)
- Recarga la página

### ✅ Una vez funcionando

Tu aplicación estará en:
```
https://tecnosoluciones.onrender.com
```

Sin problemas de cookies ✨

---

**Preguntas?** Contacta al soporte de Render.com o revisa la documentación:
https://render.com/docs
