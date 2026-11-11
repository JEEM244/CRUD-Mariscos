<?php
session_start(); if(empty($_SESSION["admin"])){header("Location: index.php");exit;}
require_once "../config/conexion.php";
$mesa=(int)($_POST["mesa_id"]??0); $cliente=($_POST["cliente_id"]??"")!==""?(int)$_POST["cliente_id"]:null;
$platillos=$_POST["platillo"]??[]; $cantidades=$_POST["cantidad"]??[];
if(!$mesa || count($platillos)===0){header("Location: pedidos.php");exit;}
try{
 $conexion->beginTransaction();
 $revisar=$conexion->prepare("SELECT estado FROM mesas WHERE id=? FOR UPDATE");$revisar->execute([$mesa]);
 if($revisar->fetchColumn()!=="Libre"){throw new Exception("Mesa ocupada");}
 $alta=$conexion->prepare("INSERT INTO pedidos (mesa_id,cliente_id,total) VALUES (?,?,0)");$alta->execute([$mesa,$cliente]);$pedido=$conexion->lastInsertId();$total=0;
 $buscar=$conexion->prepare("SELECT precio FROM platillos WHERE id=? AND disponible=1");
 $detalle=$conexion->prepare("INSERT INTO detalle_pedido (pedido_id,platillo_id,cantidad,precio) VALUES (?,?,?,?)");
 foreach($platillos as $i=>$id){$cantidad=max(1,(int)($cantidades[$i]??1));$buscar->execute([(int)$id]);$precio=$buscar->fetchColumn();if($precio===false)continue;$detalle->execute([$pedido,(int)$id,$cantidad,$precio]);$total+=(float)$precio*$cantidad;}
 if($total<=0){throw new Exception("Pedido vacío");}
 $actualiza=$conexion->prepare("UPDATE pedidos SET total=? WHERE id=?");$actualiza->execute([$total,$pedido]);
 $conexion->prepare("UPDATE mesas SET estado='Ocupada' WHERE id=?")->execute([$mesa]);
 $conexion->commit();header("Location: pedidos.php?ok=1");exit;
}catch(Throwable $e){if($conexion->inTransaction())$conexion->rollBack();die("No se pudo guardar el pedido. Verifica que la mesa esté libre y vuelve a intentar.");}
?>