<?php

function optionsCategorias ($selected) {
	$resCat = consulta ("select * from CATEGORIAS order by DESCRIPCION");
	while ($fila = extraer_registro ($resCat)) {
	?>
<option value="<?=$fila['ID_CATEGORIA']?>"
	<?php if ($fila['ID_CATEGORIA']==$selected) { echo " selected=\"true\""; } ?>><?=$fila['DESCRIPCION']?></option>
<?php 
  	}
}

function optionsSubCategorias ($idCategoria, $selected) {
	$resCat = consulta ("select * from SUBCATEGORIAS WHERE ID_CATEGORIA='$idCategoria' order by DESCRIPCION");
	?>
<option value="-1">Selecciones una subcategor&iacute;a...</option>
<?php 
	while ($fila = extraer_registro ($resCat)) {
		?>
<option value="<?=$fila['ID_SUBCATEGORIA']?>"
	<?php if ($fila['ID_SUBCATEGORIA']==$selected) { echo " selected=\"true\""; } ?>><?=$fila['DESCRIPCION']?></option>
<?php 
  	}
}

function optionsClasificacion ($idCategoria, $idSubcategoria, $selected) {
	$resCat = consulta ("select * from CLASE_PRODUCTO WHERE (ID_CATEGORIA='$idCategoria' 
			OR (ID_SUBCATEGORIA = '$idSubcategoria' AND ID_CATEGORIA IS NULL)
			OR (ID_SUBCATEGORIA IS NULL)) AND ACTIVO='1'
			order by DESCRIPCION");
	?>
<option value="-1">Seleccione la clasificación...</option>
<?php 
	while ($fila = extraer_registro ($resCat)) {
		?>
<option value="<?=$fila['ID_CLASE_PRODUCTO']?>"
	<?php if ($fila['ID_CLASE_PRODUCTO']==$selected) { echo " selected=\"true\""; } ?>><?=$fila['DESCRIPCION']?></option>
<?php 
  	}
}

function optionsSubCategorias2 ($idCategoria, $selected) {
	$resCat = consulta ("select * from SUBCATEGORIAS WHERE ID_CATEGORIA='$idCategoria' order by DESCRIPCION");
	?>
<option value="-1">Selecciones una subcategor&iacute;a...</option>
<?php 
	while ($fila = extraer_registro ($resCat)) {
		?>
<option value="<?=$fila['ID_SUBCATEGORIA']?>"
	<?php if ($fila['ID_SUBCATEGORIA']==$selected) { echo " selected=\"true\""; } ?>><?=$fila['DESCRIPCION']?></option>
<?php 
  	}
}


function optionsProductos ($idCategoria, $selected) {
	$resP = consulta ("select ID_PRODUCTO, DESCRIPCION from PRODUCTOS where ID_CATEGORIA='$idCategoria' order by DESCRIPCION");
	while ($fila = extraer_registro ($resP)) {
		?>
<option value="<?=$fila['ID_PRODUCTO']?>"
	<?php if ($fila['ID_PRODUCTO']==$selected) { echo " selected=\"true\""; } ?>><?=$fila['DESCRIPCION']?></option>
<?php 
  	}
}

function optionsTransporte ($selected) {
	$resP = consulta ("select ID_PRODUCTO, DESCRIPCION from PRODUCTOS where ID_SUBCATEGORIA='-10' and ACTIVO='1' order by DESCRIPCION");
	while ($fila = extraer_registro ($resP)) {
		?>
<option value="<?=$fila['ID_PRODUCTO']?>"
	<?php if ($fila['ID_PRODUCTO']==$selected) { echo " selected=\"true\""; } ?>><?=$fila['DESCRIPCION']?></option>
<?php 
  	}
}

function optionsUnidades ($selected) {
	$resUnid = consulta ("select * from UNIDADES order by DESCRIPCION");
	while ($fila = extraer_registro ($resUnid)) {
	?>
<option value="<?=$fila['ID_UNIDAD']?>"
	<?php if ($fila['ID_UNIDAD']==$selected) { echo " selected=\"true\""; } ?>><?=$fila['DESCRIPCION']?></option>
<?php 
  	}
}

function optionsProveedores ($selected) {
	$resProv = consulta ("select * from PROVEEDORES order by NOMBRE");
	while ($fila = extraer_registro ($resProv)) {
		?>
<option value="<?=$fila['ID_PROVEEDOR']?>"
	<?php if ($fila['ID_PROVEEDOR']==$selected) { echo " selected=\"true\""; } ?>><?=$fila['NOMBRE']?></option>
<?php 
  	}
}

function optionsTiposUsuarios ($seleccionado) {
?>
<option value="ADMINISTRADOR"
	<?php if ($seleccionado=='ADMINISTRADOR') echo "selected=\"selected\"" ; ?>>Administradores</option>
<option value="REPARTIDOR"
	<?php if ($seleccionado=='REPARTIDOR') echo "selected=\"selected\"" ; ?>>Repartidores</option>
<option value="USUARIO"
	<?php if ($seleccionado=='USUARIO') echo "selected=\"selected\"" ; ?>>Usuario</option>
<?php 
}

function optionsUsuariosConPedidos ($seleccionado, $lote) {
	$usuarios = consulta("select distinct U.ID_USUARIO, U.NOMBRE, U.APELLIDOS 
			from USUARIOS U, PEDIDOS P
			WHERE U.ID_USUARIO=P.ID_USUARIO and P.ESTADO='PREPARACION' and TIPO_USUARIO='USUARIO' and LOTE='$lote' order by ID_USUARIO");
	while ($fila = extraer_registro($usuarios)) {
		$selected = "";
		if ($fila['ID_USUARIO']==$seleccionado) {
			$selected = "selected=\"selected\"";
		}
		echo "<option value=\"".$fila['ID_USUARIO']."\" $selected>".$fila['ID_USUARIO']." (".$fila['NOMBRE']." ".$fila['APELLIDOS'].")</option>";
	}
}

function optionsUsuariosConPedidosHist ($seleccionado, $lote) {
	$usuarios = consulta("select distinct U.ID_USUARIO, U.NOMBRE, U.APELLIDOS
			from USUARIOS U, PEDIDOS P
			WHERE U.ID_USUARIO=P.ID_USUARIO and TIPO_USUARIO='USUARIO' and LOTE='$lote' order by ID_USUARIO");
	while ($fila = extraer_registro($usuarios)) {
		$selected = "";
		if ($fila['ID_USUARIO']==$seleccionado) {
			$selected = "selected=\"selected\"";
		}
		echo "<option value=\"".$fila['ID_USUARIO']."\" $selected>".$fila['ID_USUARIO']." (".$fila['NOMBRE']." ".$fila['APELLIDOS'].")</option>";
	}
}

function optionsUsuariosActivos ($seleccionado) {
	$usuarios = consulta("select U.ID_USUARIO, U.NOMBRE, U.APELLIDOS from USUARIOS U WHERE U.ACTIVO=1 and TIPO_USUARIO='USUARIO' order by ID_USUARIO");
	while ($fila = extraer_registro($usuarios)) {
		$selected = "";
		if ($fila['ID_USUARIO']==$seleccionado) {
			$selected = "selected=\"selected\"";
		}
		echo "<option value=\"".$fila['ID_USUARIO']."\" $selected>".$fila['ID_USUARIO']." (".$fila['NOMBRE']." ".$fila['APELLIDOS'].")</option>";
	}
}

function optionsUsuariosPorTipo ($tipoUsuario, $seleccionado) {
	$usuarios = consulta("select U.ID_USUARIO, U.NOMBRE, U.APELLIDOS from USUARIOS U WHERE TIPO_USUARIO='$tipoUsuario' order by ID_USUARIO");
	while ($fila = extraer_registro($usuarios)) {
		$selected = "";
		if ($fila['ID_USUARIO']==$seleccionado) {
			$selected = "selected=\"selected\"";
		}
		echo "<option value=\"".$fila['ID_USUARIO']."\" $selected>".$fila['ID_USUARIO']." (".$fila['NOMBRE']." ".$fila['APELLIDOS'].")</option>";
	}
}

function optionsLotes ($seleccionado) {
	$loteInicial = consultarLoteActual();
	$lote = $loteInicial;
	while ($lote>0) { /*$lote > $loteInicial - 10 && */
		$selected = "";
		if ($lote==$seleccionado) {
			$selected = "selected=\"selected\"";
		}
		echo "<option value=\"".$lote."\" $selected>LOTE_$lote</option>";
		$lote = $lote -1;
	}
}

function optionsSubgrupos ($seleccionado) {
	$resProv = consulta ("select * from SUBGRUPOS");
	while ($fila = extraer_registro ($resProv)) {
		?>
	<option value="<?=$fila['ID_SUBGRUPO']?>"
		<?php if ($fila['ID_SUBGRUPO']==$seleccionado) { echo " selected=\"true\""; } ?>><?=$fila['NOMBRE']?></option>
		<?php 
	  	}
}

function optionsSubgruposProvLote ($seleccionado, $lote) {
	$resProv = consulta ("select * from SUBGRUPOS WHERE ID_SUBGRUPO in (
			select DISTINCT(ID_SUBGRUPO) from PEDIDOS_PROVEEDORES WHERE LOTE ='$lote'
		) order  by NOMBRE");
	while ($fila = extraer_registro ($resProv)) {
		?>
	<option value="<?=$fila['ID_SUBGRUPO']?>"
		<?php if ($fila['ID_SUBGRUPO']==$seleccionado) { echo " selected=\"true\""; } ?>><?=$fila['NOMBRE']?></option>
		<?php 
	  	}
}

function optionsHorarios($horaIni, $horaFin) {
	$resP = consulta ("select HORA_INI, HORA_FIN from HORARIOS order by HORA_INI");
	while ($fila = extraer_registro ($resP)) {
		?>
<option value="<?=$fila['HORA_INI']?>-<?=$fila['HORA_FIN']?>"
	<?php if ($fila['HORA_INI']==$horaIni && $fila['HORA_FIN']==$horaFin) { echo " selected=\"true\""; } ?>><?=$fila['HORA_INI']?> - <?=$fila['HORA_FIN']?></option>
<?php 
  	}
}

?>
