<?php 
require_once "../includes/funciones.inc.php";

$vista = @$_GET['vista'].@$_POST['vista'];
if (isset($vista) && strlen($vista)>0) {
	$_SESSION['vista'] = $vista;
} else {
	$vista = @$_SESSION['vista'];
	if (!isset($vista) || strlen($vista)==0) {
		$vista = 'normal';
	}
}
?>
<div id="listadoProductos" class="products">
<?php if ($vista == 'normal') { ?>
	<ul class="categories">
<?php } else { ?>
	<table>
		<tr>
			<th>PRODUCTO</th>
			<th>DESC. UNIDAD</th>
			<th>PRECIO</th>
			<th>&nbsp;</th>
		</tr>
<?php } ?>
				<!-- Products -->
<?php 
$bnombre = @$_SESSION['bnombre'];
$lote = consultarLoteActual();

/*ECOLÓGICO | VEGANO | COMERCIO JUSTO | KM0 | GOURMET | SIN GLUTEN | SIN LACTOSA ,...*/

if (isset($_GET['ecologico'])) {
	if ($_GET['ecologico']=='true') {
		$_SESSION['ecologicoSel'] = TRUE;
	} else {
		$_SESSION['ecologicoSel'] = FALSE;
	}
} 
if (isset($_GET['vegano'])) {
	if ($_GET['vegano']=='true') {
		$_SESSION['veganoSel'] = TRUE;
	} else {
		$_SESSION['veganoSel'] = FALSE;
	}
}
if (isset($_GET['comercio_justo'])) {
	if ($_GET['comercio_justo']=='true') {
		$_SESSION['comercioJustoSel'] = TRUE;
	} else {
		$_SESSION['comercioJustoSel'] = FALSE;
	}
}
if (isset($_GET['km0'])) {
	if ($_GET['km0']=='true') {
		$_SESSION['km0Sel'] = TRUE;
	} else {
		$_SESSION['km0Sel'] = FALSE;
	}
}
if (isset($_GET['gourmet'])) {
	if ($_GET['gourmet']=='true') {
		$_SESSION['gourmetSel'] = TRUE;
	} else {
		$_SESSION['gourmetSel'] = FALSE;
	}
}
if (isset($_GET['sin_gluten'])) {
	if ($_GET['sin_gluten']=='true') {
		$_SESSION['sinGlutenSel'] = TRUE;
	} else {
		$_SESSION['sinGlutenSel'] = FALSE;
	}
}
if (isset($_GET['sin_lactosa'])) {
	if ($_GET['sin_lactosa']=='true') {
		$_SESSION['sinLactosaSel'] = TRUE;
	} else {
		$_SESSION['sinLactosaSel'] = FALSE;
	}
}

// Montando consulta

if (isset($_SESSION['idCategoria']) && @$_SESSION['idCategoria']=='-1') { //NOVEDADES
	$consulta = "select P.*, U.DESCRIPCION AS UNIDAD from PRODUCTOS P, UNIDADES U
				WHERE P.UNIDAD_MEDIDA=U.ID_UNIDAD AND P.NOVEDAD=1
				and P.ID_SUBCATEGORIA IN (SELECT ID_SUBCATEGORIA FROM SUBCATEGORIAS WHERE ACTIVO='1')
				and ACTIVO=1 AND (CANTIDAD_ILIMITADA=1 OR (P.CANTIDAD_1 + P.CANTIDAD_2)>0)";
	
	// PARCHE LOS POLLITOS
	if (@$_SESSION['SUBGRUPO']=='-1') {
		$consulta .= " AND P.ID_SUBCATEGORIA='11'";
	}
	// PARCHE LOS POLLITOS
	
} else if (isset($_SESSION['idCategoria']) && @$_SESSION['idCategoria']=='-2') { //OFERTAS
	$consulta = "select P.*, U.DESCRIPCION AS UNIDAD from PRODUCTOS P, UNIDADES U
			WHERE P.UNIDAD_MEDIDA=U.ID_UNIDAD AND P.OFERTA=1
			and P.ID_SUBCATEGORIA IN (SELECT ID_SUBCATEGORIA FROM SUBCATEGORIAS WHERE ACTIVO='1')
			and ACTIVO=1 AND (CANTIDAD_ILIMITADA=1 OR (P.CANTIDAD_1 + P.CANTIDAD_2)>0)";
	
	// PARCHE LOS POLLITOS
	if (@$_SESSION['SUBGRUPO']=='-1') {
		$consulta .= " AND P.ID_SUBCATEGORIA='11'";
	}
	// PARCHE LOS POLLITOS
	
} else {
	$consulta = "select P.*, U.DESCRIPCION AS UNIDAD from PRODUCTOS P, UNIDADES U
			WHERE P.UNIDAD_MEDIDA=U.ID_UNIDAD AND ID_CATEGORIA='".@$_SESSION['idCategoria']."'
			and P.ID_SUBCATEGORIA IN (SELECT ID_SUBCATEGORIA FROM SUBCATEGORIAS WHERE ACTIVO='1')
			and ACTIVO=1 AND (CANTIDAD_ILIMITADA=1 OR (P.CANTIDAD_1 + P.CANTIDAD_2)>0)";
	
	if (@$_SESSION['idsubcategoria']) {
		$consulta .= " AND P.ID_SUBCATEGORIA='".@$_SESSION['idsubcategoria']."'";
	}
}

if ($bnombre) {
	$consulta .= " AND P.DESCRIPCION LIKE '%$bnombre%'";
}
if (@$_SESSION['ecologicoSel']==TRUE) {
	$consulta .= " AND P.ECOLOGICO='1'";
}
if (@$_SESSION['veganoSel']==TRUE) {
	$consulta .= " AND P.VEGANO='1'";
}
if (@$_SESSION['comercioJustoSel']==TRUE) {
	$consulta .= " AND P.COMERCIO_JUSTO='1'";
}
if (@$_SESSION['km0Sel']==TRUE) {
	$consulta .= " AND P.KM0='1'";
}
if (@$_SESSION['gourmetSel']==TRUE) {
	$consulta .= " AND P.GOURMET='1'";
}
if (@$_SESSION['sinGlutenSel']==TRUE) {
	$consulta .= " AND P.SIN_GLUTEN='1'";
}
if (@$_SESSION['sinLactosaSel']==TRUE) {
	$consulta .= " AND P.SIN_LACTOSA='1'";
}

$consulta .= " ORDER BY P.DESCRIPCION";

$productosRes = consulta($consulta);

if (numero_filas($productosRes)==0) {
	echo "<br><p>No hay productos en esta categor&iacute;a o subcategor&iacute;a.</p>";
}

//ACA SE DECIDE CUANTOS RESULTADOS MOSTRAR POR PÁGINA
$rows_per_page = consultarNumProductos ();
	
if (($numrows = numero_filas($productosRes))>0) {
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
	$productosRes=consulta($consulta);
}
				
while ($producto = extraer_registro($productosRes)) {
	$idProducto = $producto['ID_PRODUCTO'];
	
	$nombreProd = $producto['DESCRIPCION'];
	$descProd = $producto['DESCRIPCION_LARGA'];
	
	//$precioProd = $producto['PRECIO']; //CAMBIO FACTURACION IVA
	//$precioProd = calculaPrecioConRecargo($idProducto, $precioProd);
	$precioProd = $producto['IMPORTE_SIN_IVA'];
	$iva = $producto['TIPO_IVA'];
	$precioProd = calculaPVP ($idProducto, $precioProd, $iva);
	
	$unidad = $producto['UNIDAD'];
	$descMedida = $producto['DESCRIPCION_MEDIDA'];
	//$foto = $producto['FOTO'];
	$ilimitada = $producto['CANTIDAD_ILIMITADA'];
	
	if ($ilimitada=='0' || $ilimitada==NULL) {
		$cantidad_vendida = cantidadVendidaProducto($lote, $idProducto);
		$max = ($producto['CANTIDAD_1'] + $producto['CANTIDAD_2'] - $cantidad_vendida);
	} else {
		$max = 0;
	}
	
	$cuartos = $producto['INC_CUARTOS'];
	$novedad= $producto['NOVEDAD'];
	$oferta= $producto['OFERTA'];
	$ecologico= $producto['ECOLOGICO'];
	
	$numPacks = consulta ("select count(*) as NUM from PRODUCTOS_PACK where ID_PRODUCTO_PACK='$idProducto'");
	$numPacks = extraer_registro($numPacks);
	$numPacks = $numPacks['NUM'];
	
	$minimo = $producto['PEDIDO_MINIMO'];
	
	if ($vista=='normal') {
?>
<li>
	<?php if ($ecologico == '0') { ?>
	<div class="product-noeco">
		<img src="../img/noeco.png" alt="Ecológico" title="Ecológico" />
	</div>
	<?php } ?>
	
	<?php if ($oferta == '1') { ?>
	<div class="product-oferta">
		<img src="../img/OFERTA_USER.png" alt="Oferta" title="Oferta" />
	</div>
	<?php } ?>
	
	<?php if ($novedad == '1') { ?>
	<div class="product-new">
		<img src="../img/NEW_USER.png" alt="Nuevo" title="Nuevo"/>
	</div>
	<?php } ?>
	
	<div class="product-list">
		<img alt="A mi lista" title="A mi lista" style="cursor: pointer; cursor: hand;"
			onclick="toList('<?=$idProducto?>', '<?=htmlspecialchars($nombreProd, ENT_COMPAT, 'UTF-8')?>', '<?=$unidad?>', '<?=htmlspecialchars($descMedida, ENT_COMPAT, 'UTF-8')?>', '<?=$minimo?>');"
			src="../img/TOLIST.png"/>
	</div>
	
	
	<?php
	//$nombre = strtolower(sanear_string (str_replace(" ","_",trim($nombreProd))));
	//$ruta_imagen = "../fotos/".$nombre.".jpg";
	$ruta_imagen = "../fotos/".$idProducto.".jpg";
	
	if (@file_exists ($ruta_imagen)) {?>
		<img alt="<?=$nombreProd?>" style="cursor: pointer; cursor: hand;"
			src="<?=$ruta_imagen?>" height="231" onclick="document.location='detalleProducto.php?id=<?=$idProducto?>'"
			width="383" />
	<?php } else { ?>
		<img alt="<?=$nombreProd?>" src="../img/sinfoto.gif" style="cursor: pointer; cursor: hand;"
			height="231" width="383" onclick="document.location='detalleProducto.php?id=<?=$idProducto?>'" />
	<?php } ?>
	
	<div class="product-info">
		<div class="product-desc">
			<table class="tproducto">
				<tr class="tproducto">
					<td width="38" class="tproducto">
						<?php 
						if (estaAbiertoPeriodoCompra () || @$_SESSION['ID_USUARIO']=='DEMO') { 
							if ($ilimitada || $max>0) {?>
							<img alt="Comprar" title="Comprar" class="imgcomprar" style="cursor: pointer; cursor: hand;"
							onclick="addCesta('<?=$idProducto?>', '<?=htmlspecialchars($nombreProd, ENT_COMPAT, 'UTF-8')?>', '<?=$precioProd?>', '<?=$unidad?>', '<?=htmlspecialchars($descMedida, ENT_COMPAT, 'UTF-8')?>', '<?=$max?>', '<?=$ilimitada?>',  '<?=$cuartos?>', '<?=$minimo?>');"
							src="../img/carrito2.png"/>
						<?php } else { ?>
						<img alt="Agotado" title="Agotado" src="../img/agotado.png"
							height="36" width="46" style="float: left;" />
						<?php } 
						} ?>
					</td>
					<td class="tproducto" align="right">
						<strong class="price">&nbsp;<?=$precioProd." &euro;"?>/<?=$unidad?>&nbsp;</strong>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p class="nom_product"><?=$nombreProd?></p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p class="resto_product"><?=$descMedida?></p>
					</td>
				</tr>
			</table>
		</div>
	</div><?php 					
	$_SESSION['idVoto']=$idProducto;
	include 'votaciones.php';  
	?>
</li>
<?php 
	} else {
?>
<tr>
	<td><a href="#"><span onclick="document.location='detalleProducto.php?id=<?=$idProducto?>'"><?=$nombreProd?></span></a></td>
	<td><?=$descMedida?></td>
	<td><?=$precioProd."&euro;"?>/<?=$unidad?></td>
	<td>
			<?php 
			if (estaAbiertoPeriodoCompra () || @$_SESSION['ID_USUARIO']=='DEMO') { 
				if ($ilimitada || $max>0) {?>
				<img alt="Comprar" title="Comprar" class="imgcomprar" style="cursor: pointer; cursor: hand;"
				onclick="addCesta('<?=$idProducto?>', '<?=htmlspecialchars($nombreProd, ENT_COMPAT, 'UTF-8')?>', '<?=$precioProd?>', '<?=$unidad?>', '<?=htmlspecialchars($descMedida, ENT_COMPAT, 'UTF-8')?>', '<?=$max?>', '<?=$ilimitada?>',  '<?=$cuartos?>', '<?=$minimo?>');"
				src="../img/carrito2.png"/>
			<?php } else { ?>
			<img alt="Agotado" title="Agotado" src="../img/agotado.png"
				height="36" width="46" style="float: left;" />
			<?php } 
			} ?>
	</td>
</tr>
<?php 
	}
	
} 
?>
<!-- End Products -->
<?php if ($vista == 'normal') { ?>
	</ul>
<?php } else { ?>
	</table>
<?php } ?>
</div>
<!-- FIN FOTOS DE PRODUCTOS -->

<div style="clear: both;"></div>

<!-- PAGINACION -->				
<?php 
	//UNA VEZ Q MUESTRO LOS DATOS TENGO Q MOSTRAR EL BLOQUE DE PAGINACIÓN SIEMPRE Y CUANDO HAYA MÁS DE UNA PÁGINA
	if($numrows != 0)
	{
		$nextpage= $page +1;
		$prevpage= $page -1;
		 
		?><div class="grid-12 grid paginationCV"><br><ul><?php
           //SI ES LA PRIMERA PÁGINA DESHABILITO EL BOTON DE PREVIOUS, MUESTRO EL 1 COMO ACTIVO Y MUESTRO EL RESTO DE PÁGINAS
           if ($page == 1) 
           {
            ?>
              <li><a rel="prev" href="#">&larr;</a></li>
				<li><a class="current" href="#">1</a></li> 
         <?php
              for($i= $page+1; $i<= $lastpage ; $i++)
              {?>
                <li><a
			href="compraPorCategoria.php?page=<?php echo $i;?>#listadoProductos"><?php echo $i;?></a></li>
        <?php }
           
           //Y SI LA ULTIMA PÁGINA ES MAYOR QUE LA ACTUAL MUESTRO EL BOTON NEXT O LO DESHABILITO
            if($lastpage >$page )
            {?>      
                <li class="next"><a
			href="compraPorCategoria.php?page=<?php echo $nextpage;?>#listadoProductos">&rarr;</a></li><?php
            }
            else
            {?>
                <li><a rel="next" href="#">&rarr;</a></li>
        <?php
            }
        } 
        else
        {
     
            //EN CAMBIO SI NO ESTAMOS EN LA PÁGINA UNO HABILITO EL BOTON DE PREVIUS Y MUESTRO LAS DEMÁS
        ?>
            <li class="previous"><a
			href="compraPorCategoria.php?page=<?php echo $prevpage;?>#listadoProductos">&larr;
			</a></li><?php
             for($i= 1; $i<= $lastpage ; $i++)
             {
                           //COMPRUEBO SI ES LA PÁGINA ACTIVA O NO
                if($page == $i)
                {
            ?>       <li><a class="current" href=""><?php echo $i;?></a></li><?php
                }
                else
                {
            ?>       <li><a
		href="compraPorCategoria.php?page=<?php echo $i;?>#listadoProductos"><?php echo $i;?></a></li><?php
                }
            }
             //Y SI NO ES LA ÚLTIMA PÁGINA ACTIVO EL BOTON NEXT     
            if($lastpage >$page )
            {   ?>   
                <li class="next"><a
			href="compraPorCategoria.php?page=<?php echo $nextpage;?>#listadoProductos">
			&raquo;</a></li><?php
            }
            else
            {
        ?>       <li><a rel="next" href="">&rarr;</a></li><?php
            }
        }     
    ?></ul>
    </div>
<?php
    } 
?>
<!-- FIN PAGINACION -->