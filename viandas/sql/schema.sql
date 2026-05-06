-- 1. Crear la base de datos
CREATE DATABASE IF NOT EXISTS viandalibre_db;
USE viandalibre_db;

-- 2. Tabla de Usuarios (Agregado para el Login del Admin)
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Almacenará el hash de la contraseña
    rol ENUM('admin', 'staff') DEFAULT 'admin'
);

-- 3. Tabla de Categorías
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- 4. Tabla de Viandas (El catálogo)
CREATE TABLE IF NOT EXISTS viandas (
    id_vianda INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    imagen_url VARCHAR(255),
    id_categoria INT,
    disponible BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
);

-- 5. Tabla de Pedidos (Cabecera de la venta)
CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cliente_nombre VARCHAR(100) NOT NULL,
    cliente_whatsapp VARCHAR(20) NOT NULL,
    direccion_entrega VARCHAR(255) NOT NULL,
    total_pago DECIMAL(10, 2) NOT NULL,
    estado ENUM('Pendiente', 'En Cocina', 'Enviado', 'Entregado') DEFAULT 'Pendiente'
);

-- 6. Tabla de Detalle de Pedido (El desglose)
CREATE TABLE IF NOT EXISTS detalle_pedido (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_vianda INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) AS (cantidad * precio_unitario),
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_vianda) REFERENCES viandas(id_vianda)
);

-- --- DATOS DE PRUEBA Y CONFIGURACIÓN INICIAL ---

-- Insertar Categorías
INSERT INTO categorias (id_categoria, nombre) VALUES 
(1, 'Plato Principal'), 
(2, 'Ensaladas'), 
(3, 'Minutas');

-- Insertar Usuario Administrador inicial 
-- Usuario: admin | Contraseña: admin123 (encriptada con BCRYPT)
INSERT INTO usuarios (username, password, rol) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertar las 15 viandas de ejemplo
INSERT INTO viandas (nombre, descripcion, precio, imagen_url, id_categoria, disponible) VALUES 
('Milanesa con Puré', 'Clásica milanesa de carne vacuna con puré de papas natural.', 4500.00, 'milanesa_pure.png', 1, 1),
('Ñoquis Bolognesa', 'Ñoquis de papa caseros con salsa bolognesa de la casa.', 3800.00, 'ñoquis_bolo.png', 1, 1),
('Pollo al Horno', 'Cuarto trasero de pollo al horno con papas rústicas.', 4200.00, 'pollo_papas.png', 1, 1),
('Ensalada César', 'Pechuga de pollo, lechuga, croutons, queso y aderezo césar.', 3500.00, 'ensalada_cesar.png', 2, 1),
('Canelones de Verdura', 'Dos unidades de canelones con salsa mixta.', 4000.00, 'canelones.png', 1, 1),
('Tarta de Jamón y Queso', 'Porción abundante de tarta con masa integral.', 2800.00, 'tarta_jq.png', 3, 1),
('Pastel de Papa', 'Capas de carne picada sazonada y puré de papa gratinado.', 4300.00, 'pastel_papa.png', 1, 1),
('Ensalada Mixta Plus', 'Lechuga, tomate, cebolla, huevo duro y zanahoria.', 2500.00, 'ensalada_mixta.png', 2, 1),
('Suprema Napolitana', 'Suprema de pollo con salsa de tomate, jamón y muzzarella.', 4800.00, 'suprema_napo.png', 3, 1),
('Guiso de Lentejas', 'Guiso tradicional con chorizo colorado y panceta.', 3900.00, 'guiso_lentejas.png', 1, 1),
('Arroz con Pollo', 'Arroz amarillo con trozos de pollo y arvejas.', 3700.00, 'arroz_pollo.png', 1, 1),
('Wok de Vegetales', 'Mezcla de vegetales de estación salteados con soja.', 3200.00, 'wok_veg.png', 2, 1),
('Hamburguesa Completa', 'Medallón de carne 180g con lechuga, tomate y queso.', 4100.00, 'hamburguesa.png', 3, 1),
('Tortilla de Papas', 'Porción individual de tortilla de papas y cebolla.', 3000.00, 'tortilla.png', 3, 1),
('Lasagna de Carne', 'Capas de pasta, carne picada, jamón y queso con bechamel.', 4600.00, 'lasagna.png', 1, 1);