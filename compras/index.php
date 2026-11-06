<?php
require 'config.php';
$m = $_GET['m'] ?? 'empleados';
if (!isset($MODULES[$m])) $m = 'empleados';
$M = $MODULES[$m]; $T = $M['table']; $q = trim($_GET['q'] ?? '');

$sel = ['t.*']; $txt = []; $opts = [];
foreach ($M['f'] as $k => $f) {
    if ($f[1] === 'fk') {
        $sel[] = "(SELECT nombre FROM {$f[2]} x WHERE x.id=t.$k) AS {$k}_txt";
        $opts[$k] = $db->query("SELECT id,nombre FROM {$f[2]} WHERE estado=1 ORDER BY nombre")->fetchAll();
    } elseif ($f[1] !== 'estado') $txt[] = "t.$k";
}
$sql = 'SELECT ' . implode(',', $sel) . " FROM $T t"; $p = [];
if ($q !== '') { $sql .= " WHERE CONCAT_WS(' '," . implode(',', $txt) . ") LIKE ?"; $p[] = "%$q%"; }
$st = $db->prepare($sql . ' ORDER BY t.id DESC'); $st->execute($p); $rows = $st->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($M['t']) ?> | Sistema de Compras</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
</head>
<body>
<header class="topbar">
  <i class="bi bi-bag-check-fill logo"></i>
  <nav>
    <a href="?m=empleados">Empleados</a><a href="?m=articulos">Artículos</a><a href="?m=proveedores">Proveedores</a>
    <a class="off" title="Próximamente">Solicitudes</a><a class="off" title="Próximamente">Órdenes de compra</a>
    <a href="?m=<?= e($m) ?>" class="create" onclick="openForm();return false;"><i class="bi bi-plus-lg"></i> Crear nuevo</a>
  </nav>
</header>

<aside class="sidebar">
  <?php foreach ($SIDE as [$k, $ic, $title, $on]): ?>
    <a <?= $on ? 'href="?m=' . $k . '"' : '' ?> class="<?= $k === $m ? 'active ' : '' ?><?= $on ? '' : 'off' ?>"
       title="<?= e($title . ($on ? '' : ' (próximamente)')) ?>"><i class="bi <?= $ic ?>"></i></a>
  <?php endforeach; ?>
</aside>

<main class="content">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1><?= e($M['t']) ?></h1>
    <div><button class="btn btn-outline-navy"><i class="bi bi-question-circle-fill"></i> Ayuda</button>
         <button class="btn btn-outline-navy"><i class="bi bi-gear-fill"></i> Ajustes</button></div>
  </div>

  <div class="panel mb-3">
    <form class="d-flex gap-2 align-items-center" method="get">
      <input type="hidden" name="m" value="<?= e($m) ?>">
      <input class="form-control search" name="q" placeholder="Buscar" value="<?= e($q) ?>">
      <button class="btn btn-outline-navy">Buscar</button>
      <a class="btn btn-outline-navy" href="?m=<?= e($m) ?>">Restablecer</a>
      <button type="button" class="btn btn-navy ms-auto" onclick="openForm()">Crear nuevo registro</button>
    </form>
  </div>

  <div class="panel table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr>
        <th><input type="checkbox" class="form-check-input" id="all"></th><th>ID</th>
        <?php foreach ($M['f'] as $f): ?><th><?= e($f[0]) ?></th><?php endforeach; ?><th></th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><input type="checkbox" class="form-check-input row-check"></td><td><?= $r['id'] ?></td>
          <?php foreach ($M['f'] as $k => $f): ?>
            <td><?php if ($f[1] === 'estado'): ?>
                  <span class="pill <?= $r[$k] ? '' : 'pill-off' ?>"><?= $r[$k] ? 'Activo' : 'Inactivo' ?></span>
                <?php elseif ($f[1] === 'fk'): ?><?= e($r[$k . '_txt']) ?>
                <?php else: ?><?= e($r[$k]) ?><?php endif; ?></td>
          <?php endforeach; ?>
          <td class="text-end text-nowrap">
            <button class="btn btn-sm" onclick="openForm(<?= $r['id'] ?>)" title="Editar"><i class="bi bi-pencil-square"></i></button>
            <button class="btn btn-sm text-danger" onclick="del(<?= $r['id'] ?>)" title="Eliminar"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
      <?php endforeach; if (!$rows): ?>
        <tr><td colspan="<?= count($M['f']) + 3 ?>" class="text-center text-muted py-4">Sin registros</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<div class="modal fade" id="modal" tabindex="-1"><div class="modal-dialog"><form class="modal-content" id="form">
  <div class="modal-header"><h5 class="modal-title" id="mtitle"></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input type="hidden" name="id">
    <?php foreach ($M['f'] as $k => $f): $req = in_array($f[1], ['email', 'tel']) ? '' : 'required'; ?>
      <div class="mb-3"><label class="form-label fw-semibold"><?= e($f[0]) ?></label>
      <?php if ($f[1] === 'fk'): ?>
        <select name="<?= $k ?>" class="form-select" required><option value="">Seleccione…</option>
          <?php foreach ($opts[$k] as $o): ?><option value="<?= $o['id'] ?>"><?= e($o['nombre']) ?></option><?php endforeach; ?></select>
      <?php elseif ($f[1] === 'select'): ?>
        <select name="<?= $k ?>" class="form-select" required><option value="">Seleccione…</option>
          <?php foreach ($f[2] as $o): ?><option><?= e($o) ?></option><?php endforeach; ?></select>
      <?php elseif ($f[1] === 'estado'): ?>
        <select name="<?= $k ?>" class="form-select"><option value="1">Activo</option><option value="0">Inactivo</option></select>
      <?php else: ?>
        <input name="<?= $k ?>" type="<?= $f[1] ?>" class="form-control" <?= $f[1] === 'number' ? 'min="0"' : '' ?> <?= $req ?>>
      <?php endif; ?></div>
    <?php endforeach; ?>
    <div class="text-danger small" id="err"></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-outline-navy" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-navy">Guardar</button></div>
</form></div></div>

<script>const MOD = <?= json_encode($m) ?>, TITLE = <?= json_encode($M['t']) ?>;</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="app.js"></script>
</body></html>
