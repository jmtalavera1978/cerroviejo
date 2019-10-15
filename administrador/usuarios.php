<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once "../template/head.inc.php"; ?>
</head>
<body>
    <?php require_once "../template/cabeceraAdministrador.inc.php"; ?>
    <script>
	  $(function() {
	    var availableTags = [
		<?php
	      $res1 = consulta("SELECT ID_USUARIO FROM USUARIOS");
	      while ($fila1 = extraer_registro($res1)) {
			echo "\"".$fila1['ID_USUARIO']."\",";
		  }
	      ?>
	    ];
	    $( "#bnombreUsu" ).autocomplete({
	      source: availableTags
	    });
	  });
	 </script>
	<section>
		<form method="post" action="index.php">
		<div id="contenidoAdmin">
			<?php
				$seleccionado = @$_GET['idTipoUsuario'];
				if (isset($seleccionado)) {
					$_SESSION['idTipoUsuario'] = $seleccionado;
				} else {
					$seleccionado = @$_SESSION['idTipoUsuario'];
					
					if (!isset($seleccionado)) {
						$seleccionado = "USUARIO";
						$_SESSION['idTipoUsuario'] = "USUARIO";
					}
				}
				
				/*$idUsuario = @$_GET['idUsuario'];
				if (isset($idUsuario)) {
					if (strlen($idUsuario)==0) {
						$_SESSION['idUsuario'] = NULL;
						$idUsuario = NULL;
					} else {
						$_SESSION['idUsuario'] = $idUsuario;
					}
				} else {
					$idUsuario = @$_SESSION['idUsuario'];
				}*/
				$bnombreUsu = @$_GET['bnombreUsu'];
				if (isset($bnombreUsu)) {
					if (strlen($bnombreUsu)==0) {
						$_SESSION['bnombreUsu'] = NULL;
						$bnombreUsu = NULL;
					} else {
						$_SESSION['bnombreUsu'] = $bnombreUsu;
					}
				} else {
					$bnombreUsu = @$_SESSION['bnombreUsu'];
				}
						
				$tipoSaldo = @$_GET['tipoSaldo'];
				if (isset($tipoSaldo)) {
					$_SESSION['tipoSaldo'] = $tipoSaldo;
				} else {
					$tipoSaldo = @$_SESSION['tipoSaldo'];
				}
			
				$consulta1 = "SELECT SUM(SALDO) as SALDO_GLOBAL FROM USUARIOS WHERE TIPO_USUARIO='USUARIO'";
				if (isset($tipoSaldo) && $tipoSaldo==1) {
					$consulta1 .= " AND SALDO>0";
				}else if (isset($tipoSaldo) && $tipoSaldo==2) {
					$consulta1 .= " AND SALDO<0";
				}
				$resSaldoGlobal = consulta($consulta1);
				$resSaldoGlobal = extraer_registro($resSaldoGlobal);
				$resSaldoGlobal = $resSaldoGlobal['SALDO_GLOBAL'];
			?>
			<div style="position: relative; text-align: right;"><span><b>SALDO CONFIRMADO USUARIOS:</b>&nbsp;&nbsp;&nbsp;<?=round($resSaldoGlobal, 2)?> &euro;</span></div>
			
			<h1 class="cal" style="margin-bottom: -20px;">Usuarios</h1>
			<?php
			if (isset($_SESSION['mensaje_generico'])) {
				echo "<h5 style=\"text-align: left;\">".$_SESSION['mensaje_generico']."</h5>";
				$_SESSION['mensaje_generico'] = NULL;
			} 
			?>
			<div id="tituloProveedores">
				<span>&nbsp;TIPO DE USUARIO:&nbsp;</span>
				<select name="usuarios" id="usuarios" onchange="document.location='usuarios.php?idTipoUsuario='+this.value;">
					<option value="-1">Seleccione un tipo de Usuario...</option>
					<?php optionsTiposUsuarios ($seleccionado); ?>
				</select>
				<span>&nbsp;&nbsp;&nbsp;SALDO:&nbsp;</span>
				<select name="tipoSaldo" id="tipoSaldo" onchange="document.location='usuarios.php?tipoSaldo='+this.value;">
					<option value="">Seleccione un tipo de saldo...</option>
					<option value="1" <?php if ($tipoSaldo=='1') echo "selected=\"selected\"" ; ?>>Saldo positivo</option>
					<option value="2"<?php if ($tipoSaldo=='2') echo "selected=\"selected\"" ; ?>>Saldo Negativo</option>
				</select>
				<br/>
				<span>&nbsp;USUARIO:&nbsp;</span>
				<!--  <select id="idUsuario" name="idUsuario" onchange="document.location='usuarios.php?idUsuario='+this.value">
					<option value="">Seleccione un usuario...</option>
					<!?=optionsUsuariosPorTipo($seleccionado, $idUsuario)?!>
				</select> -->
				<input type="text" id="bnombreUsu" name="bnombreUsu" value="<?=@$bnombreUsu?>" size="8" /> 
				<input type="button" id="buscar" name="buscar" value="Buscar" onclick="document.location='usuarios.php?bnombreUsu='+$('#bnombreUsu').val()" />
				<input type="button" id="limpiar" name="limpiar" value="Limpiar" onclick="document.location='usuarios.php?bnombreUsu='" />
			</div>
			
			<div id="listadoProductos">
				<?php 
					$consulta = "SELECT USUARIOS.*, SUBGRUPOS.NOMBRE AS SUBGRUPO FROM USUARIOS, SUBGRUPOS WHERE USUARIOS.ID_SUBGRUPO = SUBGRUPOS.ID_SUBGRUPO ";
					if (isset($_SESSION['idTipoUsuario']) && $_SESSION['idTipoUsuario']!=-1) {
						$consulta .= " AND TIPO_USUARIO='".$_SESSION['idTipoUsuario']."'";
					}
					if (isset($tipoSaldo) && $tipoSaldo==1) {
						$consulta .= " AND SALDO>0";
					}else if (isset($tipoSaldo) && $tipoSaldo==2) {
						$consulta .= " AND SALDO<0";
					}
					if (isset($idUsuario) && strlen($idUsuario)>0) {
						$consulta .= " AND ID_USUARIO='$idUsuario'";
					}
					if (isset($bnombreUsu) && strlen($bnombreUsu)>0) {
						$consulta .= " AND ID_USUARIO='$bnombreUsu'";
					}
					$consulta .= " ORDER BY ID_USUARIO ";
					
					$resUsuarios = consulta($consulta);
				?>
				<table class="tablaResultados" style="margin-bottom: -30px;">
					<thead>
						<tr>
							<th>COD. USUARIO</th>
							<th>SUBGRUPO</th>
							<th align="center">TIPO USUARIO</th>
							<th align="center">NOMBRE</th>
							<th align="center">APELLIDOS</th>
							<th align="center" title="SALDO CONFIRMADO">SALDO CONFIR.</th>
							<th align="center" title="SALDO TRAS PEDIDOS NO FINALIZADOS">SALDO TOTAL</th>
							<th align="center">ACTIVO</th>
							<th align="center">&nbsp;</th>
							<th align="center">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (numero_filas($resUsuarios)==0) {
	?>
						<tr>
							<td colspan="10">No hay usuarios de este tipo</td>
						</tr>
	<?php 
					} else {
						
						//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA , EN EL EJEMPLO PONGO 15
						$rows_per_page= consultarPaginacion ();
						
						if (($numrows = numero_filas($resUsuarios))>0) {
							//AL PRINCIPIO COMPRUEBO SI HICIERON CLICK EN ALGUNA PÁGINA
							if(isset($_GET['page']))
							{
								$page= $_GET['page'];
							}
							else
							{
								//SI NO DIGO Q ES LA PRIMERA PÁGINA
								$page=1;
							}
								
							//CALCULO LA ULTIMA PÁGINA
							$lastpage= ceil($numrows / $rows_per_page);
								
							//COMPRUEBO QUE EL VALOR DE LA PÁGINA SEA CORRECTO Y SI ES LA ULTIMA PÁGINA
							$page=(int)$page;
								
							if($page > $lastpage)
							{
								$page= $lastpage;
							}
								
							if($page < 1)
							{
								$page=1;
							}
								
							//CREO LA SENTENCIA LIMIT PARA AÑADIR A LA CONSULTA QUE DEFINITIVA
							$limit= 'LIMIT '. ($page -1) * $rows_per_page . ',' .$rows_per_page;
								
							//REALIZO LA CONSULTA QUE VA A MOSTRAR LOS DATOS (ES LA ANTERIO + EL $limit)
							$consulta .=" $limit";
							$resUsuarios=consulta($consulta);
						}

						while ($filaUsuarios = extraer_registro($resUsuarios)) {
							$saldoPendiente = calculaSaldoPendienteUsuario ($filaUsuarios['ID_USUARIO']);
							$saldoPendiente = round(($filaUsuarios['SALDO'] - $saldoPendiente), 2);
	?>
						<tr>
							<td><?=$filaUsuarios['ID_USUARIO']?></td>
							<td align="center"><?=$filaUsuarios['SUBGRUPO']?></td>
							<td align="center"><?=$filaUsuarios['TIPO_USUARIO']?></td>
							<td align="center"><?=$filaUsuarios['NOMBRE']?></td>
							<td align="center"><?=$filaUsuarios['APELLIDOS']?></td>
							<td align="center" nowrap><?=$filaUsuarios['SALDO']?> &euro;</td>
							<td align="center" nowrap><?=$saldoPendiente?> &euro;</td>
							<td align="center">
								<input type="checkbox" readonly="readonly" disabled="disabled" <?=$filaUsuarios['ACTIVO'] == 1 ? 'checked=\"checked\"' : ''?>/>
							</td>
							
							<td align="center"><a title="Cálculo Saldo Usuario" href="historicoSaldoUsuario.php?idUsuario=<?=$filaUsuarios['ID_USUARIO']?>&pageUser=<?=$page?>"><img src="../img/CONTABILIDAD.png" alt="calcular saldo" width="32"/></a></td>
							
							<td align="center"><a title="Editar Usuario" href="editarUsuario.php?idUsuario=<?=$filaUsuarios['ID_USUARIO']?>"><img src="../img/EDITAR.png" alt="editar" width="32"/></a></td>
						</tr>
	<?php 
						}
	?>
					</tbody>
				</table>
			<br/>
			<?php 
			//UNA VEZ Q MUESTRO LOS DATOS TENGO Q MOSTRAR EL BLOQUE DE PAGINACIÓN SIEMPRE Y CUANDO HAYA MÁS DE UNA PÁGINA
			echo "<div style='clear:both;'></div>";
			
			if(@$numrows != 0)
			{
				$nextpage= $page +1;
				$prevpage= $page -1;
				 
				?><div class="grid-12 grid paginationCV" style="position:relative; margin-top: 10px; margin-bottom: 10px"><ul><?php
			           //SI ES LA PRIMERA PÁGINA DESHABILITO EL BOTON DE PREVIOUS, MUESTRO EL 1 COMO ACTIVO Y MUESTRO EL RESTO DE PÁGINAS
			           if ($page == 1) 
			           {
			            ?>
			              <li class="previous-off">&larr;</li>
			              <li class="active">1</li> 
			         <?php
			              for($i= $page+1; $i<= $lastpage ; $i++)
			              {?>
			                <li><a href="usuarios.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
			        <?php }
			           
			           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
			            if($lastpage >$page )
			            {?>      
			                <li class="next"><a href="usuarios.php?page=<?php echo $nextpage;?>" >&rarr;</a></li><?php
			            }
			            else
			            {?>
			                <li class="next-off">&rarr;</li>
			        <?php
			            }
			        } 
			        else
			        {
			     
			            //EN CAMBIO SI NO ESTAMOS EN LA PÁGINA UNO HABILITO EL BOTON DE PREVIUS Y MUESTRO LAS DEMÁS
			        ?>
			            <li class="previous"><a href="usuarios.php?page=<?php echo $prevpage;?>">&larr;</a></li><?php
			             for($i= 1; $i<= $lastpage ; $i++)
			             {
			                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
			                if($page == $i)
			                {
			            ?>       <li class="active"><?php echo $i;?></li><?php
			                }
			                else
			                {
			            ?>       <li><a href="usuarios.php?page=<?php echo $i;?>" ><?php echo $i;?></a></li><?php
			                }
			            }
			             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
			            if($lastpage >$page )
			            {   ?>   
			                <li class="next"><a href="usuarios.php?page=<?php echo $nextpage;?>">&rarr;</a></li><?php
			            }
			            else
			            {
			        ?>       <li class="next-off">&rarr;</li><?php
			            }
			        }     
			    ?></ul></div></div><?php
			    } 
			?>
				<?php } ?>
			</div>
		
		<br/><br/>
			<div id="botonera">
				
				<input id="submit2" name="submit2"  type="button" value="Actualizar Todos los Saldos" onclick="openConfirmacion()" />
				<input id="actualizaT" name="actualizaT" type="button" value="" style="display: none" onclick="$.blockUI({ message: 'Por favor espere...' });document.location='actualizaTodosSaldosUsuario.php'" />
				
				<input id="nuevo" name="nuevo"  type="button" value="Nuevo usuario" onclick="document.location='nuevoUsuario.php'" />
			</div>
			<div id="dialogConfirmApunte" title="">
				¿Desea actualizar todos los saldos de los usuarios?</div>
				
			<script>
				$(function() {
				    $( "input[type=button]" )
				      .button()
				      .click(function( event ) {
				        event.preventDefault();
				      });
				  });
				  
				function openConfirmacion() {
					$("#dialogConfirmApunte").dialog("open");
				}
				
				$("#dialogConfirmApunte").dialog({
			      autoOpen: false,
				  height: 250,
				  width: 400,
				  modal: true,
			      buttons : {
			          "Sí" : function() {
			        	  $("#actualizaT").click();
				          $(this).dialog("close");
			          },
			          "Cancelar" : function() {
				          $(this).dialog("close");
			          }
			        }
			    });
			</script>
		</form>
	</section>
</body>
</html>
