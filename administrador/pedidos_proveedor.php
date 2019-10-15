<?php
	compruebaSesionAdministracion();
	$generado = true;
	
	$lote = consultarLoteActual();
	if (isset($_GET['lote'])) {
		$lote = $_GET['lote'];
		$_SESSION['loteSel'] = $lote;
	} else if (isset($_SESSION['loteSel'])) {
		$lote = $_SESSION['loteSel'];
	}
	
	if ($lote == consultarLoteActual()) {
		$res = consulta("select * from PEDIDOS_PROVEEDORES WHERE LOTE='$lote'");
		if (numero_filas($res)==0) {
			$generado = false;
		}
	}
	
	$bnombreProv = @$_GET['bnombreProv'];
	if (isset($bnombreProv)) {
		if (strlen($bnombreProv)==0) {
			$_SESSION['bnombreProv'] = NULL;
			$bnombreProv = NULL;
		} else {
			$_SESSION['bnombreProv'] = $bnombreProv;
		}
	} else {
		$bnombreProv = @$_SESSION['bnombreProv'];
	}
	
	$idSubgrupo = NULL;
	if (isset($_GET['idSubgrupo'])) {
		$idSubgrupo = $_GET['idSubgrupo'];
		$_SESSION['idSubgrupoSel'] = $idSubgrupo;
	} else if (isset($_SESSION['idSubgrupoSel'])) {
		$idSubgrupo = $_SESSION['idSubgrupoSel'];
	}
	
	$estadoPed = NULL;
	if (isset($_GET['estado'])) {
		$estadoPed = $_GET['estado'];
		$_SESSION['estadoSel'] = $estadoPed;
	} else if (isset($_SESSION['estadoSel'])) {
		$estadoPed = $_SESSION['estadoSel'];
	}
	
	// Comprobar grupos del RESTO
	$restoRes = consulta("select * from SUBGRUPOS WHERE ID_SUBGRUPO not in (
			select DISTINCT(ID_SUBGRUPO) from PEDIDOS_PROVEEDORES WHERE LOTE ='$lote' AND ID_SUBGRUPO IS NOT NULL
		) and ID_SUBGRUPO in (
		    select DISTINCT (U.ID_SUBGRUPO) from PEDIDOS P, USUARIOS U where P.LOTE='$lote' and P.ID_USUARIO=U.ID_USUARIO
		) order by NOMBRE");
	$restoSubgrupos = "";
	if ($filaResto = extraer_registro($restoRes)) {
		$restoSubgrupos = $filaResto ['NOMBRE'];
	}
	while ($filaResto = extraer_registro($restoRes)) {
		$restoSubgrupos .= ", ".$filaResto ['NOMBRE'];
	}
	// Comprobar grupos TODOS
	$restoTodos = consulta("select * from SUBGRUPOS WHERE ID_SUBGRUPO in (
			select DISTINCT (U.ID_SUBGRUPO) from PEDIDOS P, USUARIOS U where P.LOTE='$lote' and P.ID_USUARIO=U.ID_USUARIO
	) order by NOMBRE");
	$todosSubgrupos = "";
	if ($filaTodos = extraer_registro($restoTodos)) {
		$todosSubgrupos = $filaTodos ['NOMBRE'];
	}
	while ($filaTodos = extraer_registro($restoTodos)) {
		$todosSubgrupos .= ", ".$filaTodos ['NOMBRE'];
	}
	
	echo "<div style=\"position:relative; top: -20px\">";
	?>
	 <script>
	  $(function() {
	    var availableTags = [
		<?php
	      $res1 = consulta("SELECT NOMBRE FROM PROVEEDORES");
	      while ($fila1 = extraer_registro($res1)) {
			echo "\"".$fila1['NOMBRE']."\",";
		  }
	      ?>
	    ];
	    $( "#bnombreProv" ).autocomplete({
	      source: availableTags
	    });
	  });
	 </script>
	 	<div id="dialogConfirmReGeneracion" title="">
			¿Desea Regenerar los datos de pedidos a proveedores para el lote <?=$lote?>? <br/><br/>
			<span style="color: red; text-decoration: underline;">Tenga en cuenta que:</span><br/>
			<ul style="font-size:large; font-weight: bold; color: red">
				<li>Se generarán de nuevo todos los datos de pedidos a proveedores para su posterior revisión.</li>
				<li>Se perderán los cambios o revisiones que se hayan realizado previamente en pedidos a proveedores.</li>
				<li>Se tendrán en cuenta los PEDIDOS REVISADOS de todos los usuarios (excepto transporte).</li>
			</ul>
		</div>
		<script>
			function openConfirmacionReGenera() {
				$("#dialogConfirmReGeneracion").dialog("open");
			}
			
			$("#dialogConfirmReGeneracion").dialog({
		      autoOpen: false,
			  height: 400,
			  width: 650,
			  modal: true,
		      buttons : {
		          "Sí" : function() {
		        	  //document.location = 'generarDatosPedidosProveedores.php?lote=<?=$lote?>&url='+document.location;
		        	  $("#dialogConfirmReGeneracion2").dialog("open");
			          $(this).dialog("close");
		          },
		          "Cancelar" : function() {
			          $(this).dialog("close");
		          }
		        }
		    });
		</script>
		<div id="dialogConfirmReGeneracion2" title="">
			Seleccione los subgrupos que desee generar a parte:<br/><br/>
			<div id="checkList">
			<?php 
				$resProv = consulta ("select * from SUBGRUPOS where ID_SUBGRUPO IN (select DISTINCT (U.ID_SUBGRUPO) from PEDIDOS P, USUARIOS U where P.LOTE='$lote' and P.ID_USUARIO=U.ID_USUARIO) order by NOMBRE");
				while ($fila = extraer_registro ($resProv)) {
				?>
					<input name="selgrupos" type="checkbox" value="<?=$fila['ID_SUBGRUPO']?>"> <?=$fila['NOMBRE']?><br/>
				<?php 
				}
			?>
			</div>
		</div>
		<script>
			function openConfirmacionReGenera2() {
				$("#dialogConfirmReGeneracion2").dialog("open");
			}
			
			$("#dialogConfirmReGeneracion2").dialog({
		      autoOpen: false,
			  height: 400,
			  width: 650,
			  modal: true,
		      buttons : {
		          "Generar" : function() {
		        	  var names = [];
		              $('#checkList input:checked').each(function() {
		                  names.push(this.value);
		              });
		        	  document.location = 'generarDatosPedidosProveedores.php?lote=<?=$lote?>&subgrupos='+names+'&url='+document.location;
			          $(this).dialog("close");
		          },
		          "Cancelar" : function() {
			          $(this).dialog("close");
		          }
		        }
		    });
		</script>
	<div id="tituloProveedores" style="position:relative; top: -20px">
	<span>
		&nbsp;&nbsp;&nbsp;LOTE:&nbsp;
		<select id="lote" name="lote" onchange="document.location='pedidos.php?lote='+this.value">
			<?=optionsLotes($lote)?>
		</select>
	</span>
	<span>&nbsp;&nbsp;&nbsp;PROVEEDOR:&nbsp;</span>
		<input type="text" id="bnombreProv" name="bnombreProv" value="<?=@$bnombreProv?>" size="40" /> 
		<input type="button" id="buscar" name="buscar" value="Buscar" onclick="document.location='pedidos.php?bnombreProv='+$('#bnombreProv').val()" />
		<input type="button" id="limpiar" name="limpiar" value="Ver Todos" onclick="document.location='pedidos.php?bnombreProv=&idPedidoProv='" />		
	<br/>
	<span>
		&nbsp;&nbsp;&nbsp;SUBGRUPO:&nbsp;
		<select name="idSubgrupo" id="idSubgrupo" onchange="document.location='pedidos.php?idSubgrupo='+this.value">
			<option value="-1000">TODOS</option>
			<?php if ($restoSubgrupos!="") { ?>
			<option value="" <?php if ($idSubgrupo=='') { echo " selected=\"true\""; } ?>>RESTO</option>
			<?php } ?>
			<?php optionsSubgruposProvLote ($idSubgrupo, $lote); ?>
		</select>
	</span>
	<span>&nbsp;&nbsp;&nbsp;ESTADO:&nbsp;
		<select name="estadoPed" id="estadoPed" onchange="document.location='pedidos.php?estado='+this.value">
			<option value="">TODOS</option>
			<option value="0" <?php if ($estadoPed=='0') { echo " selected=\"true\""; } ?>>PENDIENTE DE ENVIO</option>
			<option value="2" <?php if ($estadoPed=='2') { echo " selected=\"true\""; } ?>>ENVIADOS</option>
			<option value="1" <?php if ($estadoPed=='1') { echo " selected=\"true\""; } ?>>CERRADO</option>
		</select>
	</span>
	<br/>
	<?php if ($restoSubgrupos!="") { echo "&nbsp;&nbsp;&nbsp;RESTO: <span style=\"color:blue; font-size: x-large; text-transform: uppercase;\">".$restoSubgrupos."</span><br/>"; } ?>
	<?php if ($todosSubgrupos!="" && $todosSubgrupos!=$restoSubgrupos) { echo "&nbsp;&nbsp;&nbsp;TODOS: <span style=\"color:blue; font-size: x-large; text-transform: uppercase;\">".$todosSubgrupos."</span><br/>"; } ?>
	
	<?php  if ($lote == consultarLoteActual() && !hayPedidosProveedorFinalizados($lote)) {  ?>
		<br/><input type="button" style="float: right; top: -15px" id="regenerar" name="regenerar" value="Regenerar Pedidos a Proveedores" onclick="openConfirmacionReGenera();" /><br/>
	<?php } ?>
		
	</div>
	
	
	<?php 
	if (!$generado) {
?>
		No hay datos generados para este lote.
		<div id="dialogConfirmGeneracion" title="">
			No hay datos generados para este lote.<br/><br/>
			¿Desea Generar los datos de pedidos a proveedores para el lote <?=$lote?>?<br/>
			Se generarán todos los datos de pedidos para su posterior revisión.
		</div>
		<script>
			$(document).ready(function()
			{
				openConfirmacionGenera();
			});
			  
			function openConfirmacionGenera() {
				$("#dialogConfirmGeneracion").dialog("open");
			}
			
			$("#dialogConfirmGeneracion").dialog({
		      autoOpen: false,
			  height: 300,
			  width: 650,
			  modal: true,
		      buttons : {
		          "Sí" : function() {
		        	  //document.location = 'generarDatosPedidosProveedores.php?lote=<?=$lote?>&url='+document.location;
		        	  $("#dialogConfirmReGeneracion2").dialog("open");
			          $(this).dialog("close");
		          },
		          "Más tarde" : function() {
			          $(this).dialog("close");
		          }
		        }
		    });
		</script>
<?php 
	} else {
	
		include_once 'pedidos_proveedor_list.php';
	}
?>