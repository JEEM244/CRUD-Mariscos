<?php
session_start(); if (empty($_SESSION["admin"])) { header("Location: index.php"); exit; }
require_once "../config/conexion.php";
$mesas=$conexion->query("SELECT * FROM mesas ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);
$platillos=$conexion->query("SELECT * FROM platillos WHERE disponible=1 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$clientes=$conexion->query("SELECT * FROM clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$pedidos=$conexion->query("SELECT p.*,m.numero AS mesa FROM pedidos p JOIN mesas m ON m.id=p.mesa_id ORDER BY p.id DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Pedidos | El Puerto</title><link rel="stylesheet" href="../assets/css/estilos.css"></head><body class="pixel-bg"><header class="topbar"><a class="pixel-logo" href="panel.php">EL PUERTO <small>ADMINISTRACIÓN</small></a><nav><a href="panel.php">Panel</a><a href="index.php?salir=1">Salir</a></nav></header><main class="wrap"><p class="eyebrow">COMANDAS / NUEVA ORDEN</p><h1>Crear pedido</h1><p class="intro">Selecciona la mesa, agrega los platillos y registra la orden.</p>
<?php if(isset($_GET["ok"])): ?><p class="notice">¡Pedido registrado correctamente!</p><?php endif; ?>
<form class="pixel-panel" method="POST" action="guardar_pedido.php" id="pedidoForm">
<div class="form-grid"><label>Mesa<select name="mesa_id" required><?php foreach($mesas as $m): ?><option value="<?= $m["id"] ?>" <?= $m["estado"]==="Ocupada"?"disabled":"" ?>>Mesa <?= $m["numero"] ?> — <?= $m["estado"] ?></option><?php endforeach; ?></select></label>
<label>Cliente (opcional)<select name="cliente_id"><option value="">Cliente sin registrar</option><?php foreach($clientes as $c): ?><option value="<?= $c["id"] ?>"><?= htmlspecialchars($c["nombre"]) ?></option><?php endforeach; ?></select></label></div>
<h2 class="section-title">AGREGAR COMIDA</h2><div id="lineas"><div class="order-line"><select name="platillo[]" required><?php foreach($platillos as $p): ?><option value="<?= $p["id"] ?>" data-precio="<?= $p["precio"] ?>"><?= htmlspecialchars($p["nombre"]) ?> — $<?= number_format($p["precio"],2) ?></option><?php endforeach; ?></select><input type="number" name="cantidad[]" value="1" min="1" max="30" required><button type="button" class="small-btn remove-line">QUITAR</button></div></div>
<button type="button" class="small-btn" id="addLine">+ AGREGAR PLATILLO</button>
<div class="total-box">TOTAL ESTIMADO <strong id="total">$0.00</strong></div><button class="pixel-button" type="submit">GUARDAR PEDIDO</button>
</form>
<h2 class="section-title">PEDIDOS RECIENTES</h2><div class="table-box"><table><thead><tr><th>Pedido</th><th>Mesa</th><th>Fecha</th><th>Estado</th><th>Total</th></tr></thead><tbody><?php foreach($pedidos as $p): ?><tr><td>#<?= $p["id"] ?></td><td><?= $p["mesa"] ?></td><td><?= $p["fecha"] ?></td><td><?= $p["estado"] ?></td><td>$<?= number_format($p["total"],2) ?></td></tr><?php endforeach; ?></tbody></table></div></main>
<script>
const lineas=document.getElementById('lineas');
function calcular(){let total=0;lineas.querySelectorAll('.order-line').forEach(l=>{let s=l.querySelector('select');let q=l.querySelector('input').value;total+=Number(s.selectedOptions[0]?.dataset.precio||0)*Number(q||0)});document.getElementById('total').textContent='$'+total.toFixed(2)}
document.getElementById('addLine').addEventListener('click',()=>{let n=lineas.querySelector('.order-line').cloneNode(true);n.querySelector('input').value=1;lineas.appendChild(n);calcular()});
lineas.addEventListener('change',calcular);lineas.addEventListener('input',calcular);lineas.addEventListener('click',e=>{if(e.target.classList.contains('remove-line')&&lineas.children.length>1){e.target.closest('.order-line').remove();calcular()}});calcular();
</script></body></html>