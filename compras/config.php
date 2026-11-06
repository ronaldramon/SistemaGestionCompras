<?php
// Ajusta estas credenciales (XAMPP por defecto: root sin contraseña)
$db = new PDO('mysql:host=localhost;dbname=compras;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Campo: [etiqueta, tipo (text|email|number|select|fk|estado), extra]
$MODULES = [
  'empleados' => ['t' => 'Empleados', 'table' => 'empleados', 'f' => [
      'cedula' => ['Cédula', 'text'], 'nombre' => ['Nombre', 'text'], 'apellido' => ['Apellido', 'text'],
      'email' => ['Correo', 'email'], 'departamento_id' => ['Departamento', 'fk', 'departamentos'],
      'estado' => ['Estado', 'estado']]],
  'departamentos' => ['t' => 'Departamentos', 'table' => 'departamentos', 'f' => [
      'nombre' => ['Nombre', 'text'], 'estado' => ['Estado', 'estado']]],
  'articulos' => ['t' => 'Artículos', 'table' => 'articulos', 'f' => [
      'codigo' => ['Código', 'text'], 'nombre' => ['Nombre', 'text'], 'marca' => ['Marca', 'text'],
      'unidad' => ['Unidad de medida', 'select', ['Unidad', 'Caja', 'Paquete', 'Resma', 'Libra', 'Litro', 'Metro']],
      'existencia' => ['Existencia', 'number'], 'estado' => ['Estado', 'estado']]],
  'proveedores' => ['t' => 'Proveedores', 'table' => 'proveedores', 'f' => [
      'rnc' => ['RNC', 'text'], 'nombre' => ['Nombre', 'text'], 'telefono' => ['Teléfono', 'tel'],
      'email' => ['Correo', 'email'], 'estado' => ['Estado', 'estado']]],
];

// Sidebar: [clave, icono, título, habilitado]
$SIDE = [
  ['empleados', 'bi-people-fill', 'Empleados', 1],
  ['departamentos', 'bi-diagram-3-fill', 'Departamentos', 1],
  ['articulos', 'bi-box-seam-fill', 'Artículos', 1],
  ['proveedores', 'bi-truck', 'Proveedores', 1],
  ['solicitudes', 'bi-cart-plus-fill', 'Solicitudes de artículos', 0],
  ['ordenes', 'bi-file-earmark-text-fill', 'Órdenes de compra', 0],
  ['consultas', 'bi-search', 'Consultas', 0],
  ['reportes', 'bi-bar-chart-fill', 'Reportes', 0],
];
