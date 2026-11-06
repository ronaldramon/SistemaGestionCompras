<?php
require 'config.php';
header('Content-Type: application/json; charset=utf-8');
$m = $_GET['m'] ?? '';
if (!isset($MODULES[$m])) { echo json_encode(['ok' => false, 'msg' => 'Módulo inválido']); exit; }
$T = $MODULES[$m]['table']; $F = array_keys($MODULES[$m]['f']); $a = $_GET['a'] ?? '';

try {
    if ($a === 'get') {
        $s = $db->prepare("SELECT * FROM $T WHERE id=?"); $s->execute([(int)$_GET['id']]);
        echo json_encode(['ok' => true, 'data' => $s->fetch()]);
    } elseif ($a === 'save') {
        $id = (int)($_POST['id'] ?? 0); $vals = [];
        foreach ($F as $k) { $v = trim($_POST[$k] ?? ''); $vals[$k] = ($v === '' ? null : $v); }
        if ($id) {
            $set = implode(',', array_map(fn($k) => "$k=:$k", $F)); $vals['id'] = $id;
            $db->prepare("UPDATE $T SET $set WHERE id=:id")->execute($vals);
        } else {
            $db->prepare("INSERT INTO $T (" . implode(',', $F) . ') VALUES (:' . implode(',:', $F) . ')')->execute($vals);
        }
        echo json_encode(['ok' => true]);
    } elseif ($a === 'delete') {
        $db->prepare("DELETE FROM $T WHERE id=?")->execute([(int)$_POST['id']]);
        echo json_encode(['ok' => true]);
    }
} catch (PDOException $ex) {
    $msg = $ex->errorInfo[1] == 1062 ? 'Ya existe un registro con ese valor único (cédula, RNC, código o nombre).'
         : ($ex->errorInfo[1] == 1451 ? 'No se puede eliminar: el registro está en uso. Márquelo como Inactivo.' : 'Error de base de datos.');
    echo json_encode(['ok' => false, 'msg' => $msg]);
}
