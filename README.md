# UrbanHoops 🏀

**UrbanHoops** es una tienda en línea especializada en artículos de baloncesto que combina **rendimiento deportivo** con **estilo urbano**, ofreciendo zapatillas, ropa técnica, accesorios y objetos de moda inspirados en la cultura del básquet callejero. Nuestra misión es empoderar tanto a los jugadores serios como a los entusiastas del estilo urbano con productos de calidad y una experiencia de compra optimizada.

---

## 📚 Índice

- [Motivación](#motivación)
- [Características principales](#características-principales)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Desarrollo](#desarrollo)
- [Arquitectura del proyecto](#arquitectura-del-proyecto)
- [Contribución](#contribución)
- [Roadmap](#roadmap)
- [Contacto](#contacto)
- [Licencia](#licencia)

---

## 🧠 Motivación

UrbanHoops nace de la pasión por el básquet y la cultura de la calle. No solo queremos vender productos, sino crear una comunidad: jugadores que buscan rendimiento, amantes del estilo urbano que quieren verse bien dentro y fuera de la cancha, y coleccionistas que valoran lanzamientos exclusivos. Aspiramos a ser la marca referente para quienes viven el baloncesto como un estilo de vida.

---

## ✨ Características principales

- Sitio web moderno con diseño minimalista y paleta de colores coherente (blanco, negro carbón, naranja intenso, azul eléctrico, gris)
- Catálogo de productos (zapatillas, ropa, accesorios) con páginas detalladas por producto
- Formulario de contacto y sección de ayuda (FAQ / Mesa de Ayuda)
- Diseño responsive para uso en móviles, tablets y desktop
- Guía de tallas, reseñas y detalles técnicos para cada producto
- Branding urbano con identidad visual fuerte
- Plataforma construida con Laravel 11 para mayor robustez y escalabilidad

---

## 📋 Requisitos

- PHP 8.2 o superior
- Laravel 12.x
- MySQL/MariaDB
- Composer
- Node.js y NPM
- Maquina virtual linux (previamente ya configurada para oracle)

---

## 💾 Configuración de Oracle Database

Debido a que el proyecto utiliza Oracle Database en una Máquina Virtual, se requieren pasos adicionales para habilitar la comunicación entre PHP (Laragon/Windows) y el servidor (Linux).

### Instalación del Oracle Instant Client
Para que PHP pueda "hablar" con Oracle, necesitas las librerías nativas en tu sistema host (Windows):

1. Descarga el **Instant Client Basic (64-bit)** de la [página oficial de Oracle](https://www.oracle.com/database/technologies/instant-client/winx64-64-downloads.html) (Versión 19c recomendada).
2. Descomprime el contenido en una ruta permanente, por ejemplo: `C:\oracle\instantclient_19_25`.
3. Agrega dicha ruta a las **Variables de Entorno (PATH)** de Windows.
4. Asegurate que la ruta agregada sea la primera en la lista.
5. En Laragon, asegúrate de activar las extensiones en el archivo `php.ini`:
   ```ini
   extension=oci8_19
   extension=pdo_oci
---

## 🛠️ Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/Yanickmaila26/UrbanHoops.git
cd UrbanHoops
```

2. **Instalar dependencias**
```bash
composer install
npm install
```

3. **Configurar entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos para usar oracle (tambien se puede usar mysql) selecciona una opcion:**

Editar `.env` con los datos de tu base de datos con oracle:
```env

DB_CONNECTION=oracle
DB_HOST=192.168.x.x        # IP de la Máquina Virtual
DB_PORT=1521               # Puerto estándar de Oracle
DB_DATABASE=orcl           # Nombre de la CDB (Comenta DB_SERVICE_NAME si se usa esta opcion) 
DB_SERVICE_NAME=TU_PDB     # Nombre de tu PDB específico (Comenta DB_DATABASE si se usa un servicio)
DB_USERNAME=tu_usuario     # Usuario con el que se va acceder
DB_PASSWORD=tu_password    # Contraseña del usuario 
```

Editar `.env` con los datos de tu base de datos mysql:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=urbanhoops
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

5. **Ejecutar migraciones**
```bash
php artisan migrate
```

6. **Compilar assets**
```bash
npm run dev
```
---

## 🔧 Desarrollo

### Comandos útiles

```bash
# Iniciar servidor de desarrollo
php artisan serve

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Crear nueva migración
php artisan make:migration nombre_migracion

# Ejecutar tests
php artisan test
```

### Logs
Los logs del sistema se encuentran en `storage/logs/laravel.log`

### Estructura del proyecto (Laravel)
```
UrbanHoops/
├── app/                 # Lógica de la aplicación
├── bootstrap/          # Archivos de arranque
├── config/            # Configuraciones
├── database/          # Migraciones y seeds
├── public/            # Punto de entrada público
├── resources/         # Vistas, assets, idiomas
├── routes/           # Rutas de la aplicación
├── storage/          # Archivos temporales y logs
├── tests/            # Pruebas automatizadas
├── vendor/           # Dependencias de Composer
└── .env.example      # Plantilla de variables de entorno
```

---

## 📁 Arquitectura del Proyecto

El proyecto ha evolucionado desde una estructura estática HTML/CSS a una aplicación completa con Laravel:

**Versión actual (Laravel)**
```
UrbanHoops/
├── app/
│   ├── Http/Controllers/     # Controladores
│   ├── Models/              # Modelos Eloquent
│   └── ...
├── resources/views/
│   ├── products/            # Vistas de productos
│   ├── contact.blade.php    # Formulario de contacto
│   └── ...
├── routes/web.php          # Rutas principales
└── public/                # Assets compilados
```

**Histórico (versión inicial)**
```
UrbanHoops/
├── index.html             # Página principal (home)
├── productos.html         # Catálogo de productos
├── detalle.html           # Detalle individual de producto
├── contacto.html          # Formulario de contacto / FAQ
├── css/
│   └── style.css         # Estilos de la web
├── recursos/
│   ├── imagenes/         # Imágenes
│   └── js/               # Scripts
└── README.md             # Documentación
```

---

## 🤝 Contribución

¡Las contribuciones son bienvenidas! Si quieres ayudar a mejorar UrbanHoops, sigue estos pasos:

1. **Fork del proyecto**
2. **Crear rama para nueva funcionalidad**
   ```bash
   git checkout -b feature/nueva-funcionalidad
   ```
3. **Hacer commits con mensajes claros**
4. **Abrir un Pull Request** explicando los cambios

Por favor, asegúrate de seguir el estilo de código existente y de probar tus cambios antes de enviarlos.

---

## 📅 Roadmap

- [ ] Integrar un chatbot para atención personalizada
- [ ] Añadir funcionalidad de carrito y checkout
- [ ] Incluir filtros avanzados en el catálogo (por talla, marca, precio)
- [ ] Implementar autenticación de usuario (registro / login)
- [ ] Añadir un blog con contenido de comunidad (jugadores, torneos, consejos)
- [ ] Sistema de reseñas y valoraciones
- [ ] Integración con pasarelas de pago
- [ ] API para aplicaciones móviles

---

## 📬 Contacto

- Sitio web: (tu futura URL de producción)
- Correo: soporte@urbanhoops.com
- Instagram / TikTok: @UrbanHoopsOfficial
- Repositorio: [https://github.com/Yanickmaila26/UrbanHoops](https://github.com/Yanickmaila26/UrbanHoops)

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT — consulta el archivo LICENSE para más detalles.

---

## Acerca de Laravel

UrbanHoops está construido con [Laravel](https://laravel.com), un framework de PHP expresivo y elegante. Laravel facilita tareas comunes en proyectos web como:

- [Motor de rutas simple y rápido](https://laravel.com/docs/routing)
- [Contenedor de inyección de dependencias potente](https://laravel.com/docs/container)
- Múltiples backends para [sesiones](https://laravel.com/docs/session) y [caché](https://laravel.com/docs/cache)
- [ORM de base de datos expresivo e intuitivo](https://laravel.com/docs/eloquent)
- [Migraciones de esquema independientes de la base de datos](https://laravel.com/docs/migrations)
- [Procesamiento robusto de trabajos en segundo plano](https://laravel.com/docs/queues)
- [Difusión de eventos en tiempo real](https://laravel.com/docs/broadcasting)

Laravel es accesible, potente y proporciona las herramientas necesarias para aplicaciones grandes y robustas como UrbanHoops.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>