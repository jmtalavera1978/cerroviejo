// <!--
/**
 * Permite seleccionar una categoría y actualizar la lista de subcategorias
 */
function seleccionarCategoria (categoria, idSubCategorias) {
	 var parametros = {
             "categoria" : categoria
     };
     $.ajax({
         data:  parametros,
         url:   'recargaSubcategorias.php',
         type:  'post',
         beforeSend: function () {
             $("#"+idSubCategorias).html("<option>Recargando...</option>");
         },
         success:  function (response) {
             $("#"+idSubCategorias).html(response);
         }
     });
     seleccionarClasificacion ($('#'+idSubCategorias).val(), 'clasificacion');
}

/**
 * Permite seleccionar una subcategoría y actualizar la lista de clasificacion
 */
function seleccionarClasificacion (subcategoria, idClase) {
	 var parametros = {
             "categoria" : $('#categorias').val(),
             "subcategoria" : subcategoria
     };
     $.ajax({
         data:  parametros,
         url:   'recargaClasificacion.php',
         type:  'post',
         beforeSend: function () {
             $("#"+idClase).html("<option>Recargando...</option>");
         },
         success:  function (response) {
             $("#"+idClase).html(response);
         }
     });
}
// -->