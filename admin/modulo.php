<?php
session_start(); if(empty($_SESSION["admin"])){header("Location: index.php");exit;}
require_once "../config/conexion.php";
$definiciones=[
 "clientes"=>["Clientes","clientes",["nombre"=>"Nombre","telefono"=>"Teléfono","correo"=>"Correo","direccion"=>"Dirección"],["nombre","telefono","correo","direccion"]],
 "proveedores"=>["Proveedores","proveedores",["nombre"=>"Nombre","telefono"=>"Teléfono","correo"=>"Correo","producto"=>"Producto que suministra"],["nombre","telefono","correo","producto"]],
 "almacen"=>["Almacén","almacen",["producto"=>"Producto","categoria"=>"Categoría","existencia"=>"Existencia","unidad"=>"Unidad","costo"=>"Costo"],["producto","categoria","existencia","unidad","costo"]],
 "facturacion"=>["Facturación","facturacion",["cliente_id"=>"ID de cliente","rfc"=>"RFC","razon_social"=>"Razón social","codigo_postal"=>"Código postal","uso_cfdi"=>"Uso CFDI","total"=>"Total","estado"=>"Estado"],["cliente_id","rfc","razon_social","codigo_postal","uso_cfdi","total","estado"]]
];
$clave=$_GET["tabla"]??"clientes";if(!isset($definiciones[$clave])){die("Módulo no válido");}
[$titulo,$tabla,$campos,$columnas]=$definiciones[$clave];$mensaje="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
 $accion=$_POST["accion"]??"";
 if($accion==="eliminar"){ $id=(int)$_POST["id"]; $st=$conexion->prepare("DELETE FROM $tabla WHERE id=?");$st->execute([$id]);$mensaje="Registro eliminado."; }
 elseif($accion==="guardar"){
  $id=(int)($_POST["id"]??0);$valores=[];foreach($columnas as $c){$valores[]=$_POST[$c]??"";}
  if($id){$partes=implode("=?,", $columnas)."=?";$st=$conexion->prepare("UPDATE $tabla SET $partes WHERE id=?");$st->execute([...$valores,$id]);$mensaje="Registro actualizado.";}
  else{$marcas=implode(",",array_fill(0,count($columnas),"?"));$st=$conexion->prepare("INSERT INTO $tabla (".implode(",",$columnas).") VALUES ($marcas)");$st->execute($valores);$mensaje="Registro agregado.";}
 }
}
$editar=null;if(isset($_GET["editar"])){$st=$conexion->prepare("SELECT * FROM $tabla WHERE id=?");$st->execute([(int)$_GET["editar"]]);$editar=$st->fetch(PDO::FETCH_ASSOC);}
$registros=$conexion->query("SELECT * FROM $tabla ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $titulo ?> | El Puerto</title><link rel="stylesheet" href="../assets/css/estilos.css"></head><body class="pixel-bg"><header class="topbar"><a class="pixel-logo" href="panel.php">EL PUERTO <small>DATABASE</small></a><nav><a href="panel.php">Panel</a><a href="index.php?salir=1">Salir</a></nav></header><main class="wrap"><p class="eyebrow">BASE DE DATOS / CRUD</p><h1><?= $titulo ?></h1><p class="intro">Crear, consultar, actualizar y eliminar registros.</p>
<?php if($mensaje): ?><p class="notice"><?= $mensaje ?></p><?php endif; ?>
<form class="pixel-panel" method="POST"><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id" value="<?= $editar["id"]??0 ?>"><h2><?= $editar?"Editar registro":"Nuevo registro" ?></h2><div class="form-grid"><?php foreach($campos as $campo=>$etiqueta): ?><label><?= $etiqueta ?><?php if($campo==="estado"): ?><select name="<?= $campo ?>"><?php foreach(["Pendiente","Solicitada","Emitida"] as $op): ?><option <?= ($editar[$campo]??"Pendiente")===$op?"selected":"" ?>><?= $op ?></option><?php endforeach; ?></select><?php else: ?><input name="<?= $campo ?>" value="<?= htmlspecialchars((string)($editar[$campo]??"")) ?>" <?= in_array($campo,["nombre","producto","razon_social"])? "required":"" ?> <?= in_array($campo,["existencia","costo","total","cliente_id"])?'type="number" step="0.01"':"" ?>><?php endif; ?></label><?php endforeach; ?></div><button class="pixel-button"><?= $editar?"ACTUALIZAR":"DAR DE ALTA" ?></button><?php if($editar): ?><a class="text-link" href="modulo.php?tabla=<?= $clave ?>">Cancelar</a><?php endif; ?></form>
<h2 class="section-title">REGISTROS (<?= count($registros) ?>)</h2><div class="table-box"><table><thead><tr><th>ID</th><?php foreach($campos as $et): ?><th><?= $et ?></th><?php endforeach; ?><th>Acciones</th></tr></thead><tbody><?php foreach($registros as $r): ?><tr><td><?= $r["id"] ?></td><?php foreach($columnas as $c): ?><td><?= htmlspecialchars((string)$r[$c]) ?></td><?php endforeach; ?><td><a class="text-link" href="modulo.php?tabla=<?= $clave ?>&editar=<?= $r["id"] ?>">Editar</a><form method="POST" class="inline-form" onsubmit="return confirm('¿Eliminar este registro?')"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= $r["id"] ?>"><button class="danger-link">Eliminar</button></form></td></tr><?php endforeach; ?></tbody></table></div></main></body></html>