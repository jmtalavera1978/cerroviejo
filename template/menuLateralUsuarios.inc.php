<?php
//if (estaAbiertoPeriodoCompra () || @$_SESSION['ID_USUARIO']=='DEMO') {
	$orden = @$_GET['orden'].@$_POST['orden'];
	if (isset($orden) && strlen($orden)>0) {
		@$_SESSION['categoriasList'];
		if ($orden=='1') {
			$_SESSION['orden'] = $orden;
		} else {
			$_SESSION['orden'] = NULL;
			$orden = NULL;
		}
	} else {
		$orden = @$_SESSION['orden'];
	}

	$res = @$_SESSION['categoriasList'];
	if (!isset($res) || $res==NULL) {
		if ($orden=='1') {
			$res = consulta ("select C.* from CATEGORIAS C where C.ACTIVO='1' and C.ID_CATEGORIA>0 
						and (select count(ID_PRODUCTO) from PRODUCTOS P where P.ID_CATEGORIA=C.ID_CATEGORIA and P.ACTIVO='1')>0
						order by C.DESCRIPCION");
		} else {
			$res = consulta ("select C.* from CATEGORIAS C where C.ACTIVO='1' and C.ID_CATEGORIA>0
						and (select count(ID_PRODUCTO) from PRODUCTOS P where P.ID_CATEGORIA=C.ID_CATEGORIA and P.ACTIVO='1')>0
						order by C.ID_CATEGORIA");
		}
		$_SESSION['categoriasList'] = $res;
	} 
	?>
	<div class="grid-4 grid">
	  <h2>Categorias</h2>
	
	<select name="orden" id="orden" onchange="document.location=window.location.pathname+'?orden='+this.value;">
		<option value="-1" <?=($orden=='1' ? '' : 'selected')?>>Por defecto</option>
		<option value="1" <?=($orden=='1' ? 'selected' : '')?>>Por descripci&oacute;n</option>
		</select>
	<a href="?vista=normal" title="Vista por defecto" style="float: right;"><img width="25" alt="listado" src="../img/vista_normal.png"/></a>
	<a href="?vista=lista" title="Vista en listado" style="float: right"><img width="25" alt="listado" src="../img/vista_list.png"/></a>
	
	
      <div class="box categories">
        <div class="box-content">
          <ul class="productos">
            <li id="novedadCat" <?php if ('-1'==@$_SESSION['idCategoria']) { echo " class=\"color_resaltado\"";} ?>><a href="novedades.php#listadoProductos">Novedades</a></li>
            <li id="ofertaCat" <?php if ('-2'==@$_SESSION['idCategoria']) { echo " class=\"color_resaltado\"";} ?>><a href="ofertas.php#listadoProductos">En Oferta</a></li>
            
            <?php while ($fila = extraer_registro ($res)) { ?>
		  	<li <?php if (@$_SESSION['idCategoria']==$fila['ID_CATEGORIA']) { echo " class=\"color_resaltado\"";} ?>>
		  		<a href="compraPorCategoria.php?idCategoria=<?=$fila['ID_CATEGORIA']?>#listadoProductos" <?php	if (@$_SESSION['SUBGRUPO']=='-1' && $fila['ID_CATEGORIA']!='3') { echo " style=\"color:#cccccc\" "; } ?>><?=$fila['DESCRIPCION']?></a>
		  		<?php
		  			if (@$_SESSION['idCategoria']==$fila['ID_CATEGORIA']) {
						$consultaSubcat = "select * from SUBCATEGORIAS WHERE ID_CATEGORIA='".@$_SESSION['idCategoria']."' and ACTIVO='1'";
						if ($orden=='1') {
							$consultaSubcat .= " ORDER BY DESCRIPCION";
						}
			  			$subcategorias = consulta ($consultaSubcat);
			  			if (numero_filas($subcategorias)>0) {
							echo "<ul class=\"productos-sub\">";
				  			while ($filaSub = extraer_registro ($subcategorias)) {
								echo "<li";
								if (@$_SESSION['idsubcategoria']==$filaSub['ID_SUBCATEGORIA']) { 
									echo " style=\"font-weight: bold;\"";
								} else {
									echo " style=\"font-weight: normal;\"";
								}
								echo "><a href=\"compraPorCategoria.php?idsubcategoria=".$filaSub['ID_SUBCATEGORIA']."#listadoProductos\"";
								if (@$_SESSION['SUBGRUPO']=='-1' && $filaSub['ID_SUBCATEGORIA']!='11') { echo " style=\"color:#cccccc\" "; }
								echo ">".$filaSub['DESCRIPCION']."</a></li>";
							}
							echo "</ul>";
						}
					}
		  		?>
		  	</li>
		  	<?php } ?>
		  	</ul>
        </div>
      </div>
      
      </div>
			
			<div id="contenido" class="grid-12">
				
				<div class="grids">
					<?php
					if (isset($interval)) {
						$loteActual = consultarLoteActual();
						if ($interval->format('%d')>0) {
							echo "<p class=\"message tiempo\">Periodo de compras <b>Lote $loteActual ABIERTO</b>. Queda ".$interval->format('%d día/s, %h hora/s y %i minuto/s')." para tramitar la compra.</p>";
						} else {
							echo "<p class=\"message tiempo\">Periodo de compras <b>Lote $loteActual ABIERTO</b>. Queda ".$interval->format('%h hora/s y %i minuto/s')." para tramitar la compra.</p>";
						}
					} else {
						echo "<p class=\"message tiempo\">El periodo para realizar sus compras en el <b>Portal de Pedidos está cerrado</b>.  Puede ir preparando su cesta incorporando los productos a una Lista de Compra.</p>";
					} 
					?>
					<?php if (calculaDescuentoRecargoUsuario() > 0) { ?>
                 	<p class="message descuento">Todos los precios del catálogo tienen ya aplicado esta semana un descuento especial para usted del <?=calculaDescuentoRecargoUsuario()?>% sobre la Aportaci&oacute;n al Reparto que marca CerroViejo</p>
                 	<?php } ?>
                 	<p class="message">Todos los precios de este catálogo son con <b>IVA y Aportación al Reparto incluidos</b>. <br/>El importe final de los <b>productos con peso aproximado</b> se recalculará automáticamente con el peso revisado de entrega.</p>
<?php 
//}
?>