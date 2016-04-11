<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce
  por JMC

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  $trimestre = $_GET['trimestre'];
  $action = $_GET['action'];
  $oID = $_GET['oID'];

if ( ($_GET['Year']) && (tep_not_null($_GET['Year'])) ) {
	$Year = $_GET['Year'];
} else {
	$Year = date("Y");
}

// crea la factura
	if ($action=="facturar"){
	$get_serie = $_GET['serie'];
	$IVA = $_GET['IVA'];
	$IVAENVIO = $_GET['IVAENVIO'];
	// function facturar ($oID, $get_serie) {
		facturar ($oID, $get_serie, $Year, $IVA, $IVAENVIO);
//		tep_href_link ("pdf_invoice.php" , oID=6279

        
	}

	// Check if the form is submitted
//   if  (!$_GET['submitted']  && !$_GET['psubmitted'] ) 

   if ($_GET['submitted'] !== "CSV" )
   {

  require(DIR_WS_INCLUDES . 'template_top.php');
?>


<!-- body_text //-->
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td colspan=1>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo  "Ventas"; ?></td>
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <form name="fventas"  method="get" action="<?php echo $PHP_SELF; ?>">
              <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="left" rowspan="2" class="menuBoxHeading">
                    <input type="radio" name="report" value="1" <?php if ($srView == 1) echo "checked"; ?>><?php echo "Anual"; ?><br>
                    <input type="radio" name="report" value="2" <?php if ($srView == 2) echo "checked"; ?>><?php echo "Trimestral"; ?><br>
                  </td>
                  <td align="left" colspan="2" class="menuBoxHeading">
<?php 
	echo "Trimestre"; 
	
?>
<br>
                    <select name="trimestre" size="1">
<?php
      for ($i = 1; $i < 5; $i++) {
?>
                      <option<?php if ($trimestre == $i) echo " selected"; ?> value="<?php echo $i; ?>"><?php echo "Trimestre " . $i; ?></option>
<?php
      }
?>
                    </select>
                    <select name="Year" size="1">
<?php

    for ($i = 10; $i >= 0; $i--) {
?>
                      <option<?php if ($Year == date("Y") -$i) echo " selected"; ?>><?php echo
date("Y") - $i; ?></option><?php
    }
?>
                    </select>
                </tr>

                <tr>
                  <td colspan="1" class="menuBoxHeading" align="left">
                    <input type="submit"  name="submitted" value="<?php echo "Pantalla"; ?>">
                    <input type="submit"  name="submitted" value="<?php echo "CSV"; ?>">
                  </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
        <tr>
          <td width=100% valign=top>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td valign="top">
                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
				  <form name="ffacturar" method="get" action="<?php echo $PHP_SELF; ?>">
                    <tr class="dataTableHeadingRow">
                      <td class="dataTableHeadingContent" align="center"><?php echo  "Factura"; ?></td>
<?php /*
					  <td class="dataTableHeadingContent" align="center"><?php echo  "Fecha pago"; ?></td>
                      <td class="dataTableHeadingContent" align="center"><?php echo  "Fecha pedido"; ?></td>
*/ ?>
					  <td class="dataTableHeadingContent" align="center"><?php echo  "F. factura"; ?></td>
                      <td class="dataTableHeadingContent" align="center"><?php echo  "Id";?></td>
					  <td class="dataTableHeadingContent" align="center"><?php echo  "NIF";?></td>
					  <td class="dataTableHeadingContent" align="center"><?php echo  "email";?></td>
                      <td class="dataTableHeadingContent" align="center"><?php echo  "País"; ?></td>
                      <td class="dataTableHeadingContent" align="center"><?php echo  "Zona"; ?></td>
                      <td class="dataTableHeadingContent" align="center"><?php echo  "Estado";?></td>
                      <td class="dataTableHeadingContent" align="center"><?php echo  "Cuenta";?></td>
                      <td class="dataTableHeadingContent" align="right"><?php echo  "Productos";?></td>
                      <td class="dataTableHeadingContent" align="right"><?php echo  "Envio";?></td>
					  <td class="dataTableHeadingContent" align="right"><?php echo  "IVA Envio";?></td>
					  <td class="dataTableHeadingContent" align="right"><?php echo  "Comision";?></td>
					  <td class="dataTableHeadingContent" align="right"><?php echo  "Base";?></td>
                      <td class="dataTableHeadingContent" align="right"><?php echo  "% IVA";?></td>
                      <td class="dataTableHeadingContent" align="right"><?php echo  "IVA";?></td>
                      <td class="dataTableHeadingContent" align="right"><?php echo  "Total";?></td>
                    </tr>
<?php
// Fin echo no CSV
}
//////////////////////////////
$delim =  chr(9) ;
switch ($trimestre) {
	case 1:
		$mesinicio =1;
		$mesfinal =3;
		break;
	case 2:
		$mesinicio =4;
		$mesfinal =6;
		break;
	case 3:
		$mesinicio =7;
		$mesfinal =9;
		break;
	case 4:
		$mesinicio =10;
		$mesfinal =12;
		break;
		default:
}

// Empieza la fiesta

$pedidos= "SELECT o.orders_id, o.customers_country, o.customers_name, o.customers_id, o.delivery_country, o.delivery_state, o.billing_nif, o.date_purchased, o.orders_status, o.shipping_module, o.payment_method, o.customers_email_address, oh.orders_status_id, oh.date_added, f.fechafactura, f.serie, f.numero_factura FROM orders o ";
$pedidos .="INNER JOIN orders_status_history oh ON o.orders_id = oh.orders_id ";
$pedidos .="LEFT JOIN facturas f ON o.orders_id = f.orders_id ";

/// depende si eliges A o B
$pedidos .= "WHERE o.payment_method NOT LIKE 'Recogida%' ";
$pedidos .= "AND o.payment_method NOT LIKE 'Other%' ";

// Pedidos con pago recibido, se computan con fecha de pago, si no es un follon
$pedidos .= "AND (oh.orders_status_id =5) ";
$pedidos .= "AND oh.date_added > '" . $Year . "-" . $mesinicio . "-01 00:00:00' ";
$pedidos .= "AND oh.date_added < '" . $Year . "-" . $mesfinal . "-31 23:59:59' ";

// Por tipo de pago:
// $pedidos .= "AND (oh.payment_method ="Ingreso/Transferencia Bancaria" OR oh.payment_method ="Wire Bank Transfer") ";
// $pedidos .= "AND (oh.payment_method = "Paypal") ";

// ordena por fecha de pago
// $pedidos .= "ORDER BY serie, numero_factura, o.delivery_country, oh.date_added, o.orders_id";
$pedidos .= "ORDER BY serie, numero_factura, oh.date_added, o.orders_id";

$resultados = tep_db_query($pedidos);
 $num_rows = tep_db_num_rows($resultados);

 $data =array();
 	while ($row_orders = tep_db_fetch_array($resultados)) {
	 //start one loop
	 
//bucle 1
$aviso ="";
$idpais = tep_get_country_id ($row_orders["customers_country"]);
$pais =tep_get_country_iso_code_2 ($idpais);

// Calcula % IVA

if ($row_orders["date_purchased"] < "2012-08-31 23:59:59"){
	$aplicarIVA = 18;
	}else{
	$aplicarIVA = 21;
}

switch ($pais) {
	case "AT" :
	case "BE" :
	case "FI" :
	case "FR" :
	case "DE" :
	case "IE" :
	case "IT" :
	case "LU" :
	case "NL" :
	case "DK" :
	case "PL" :
	case "PT" :
	case "SE" :
	case "CH" :
	case "GB" :
		if (!$row_orders["billing_nif"] ) {
		$porcentajeiva = $aplicarIVA;
		}else{
		$porcentajeiva = 0;
		}
		
		$zona = "UE";
		$serie= $zona;
		break;
	case "ES" :
			switch ($row_orders["delivery_state"]) {
			case "Santa Cruz de Tenerife" :
			case "Ceuta" :
			case "Melilla" :
			case "Las Palmas" :
				$porcentajeiva = 0;
				$zona = "Canarias";
				$serie= "ES";
				break;
			default:
				$zona = "Peninsula";
				$porcentajeiva = $aplicarIVA;
				$serie= "ES";
			}
		break;
	default :
		$porcentajeiva = "0" ;
		$zona = "NO UE";
		$serie= "RW";
		break;
}


if ($row_orders["payment_method"] == "Ingreso/Transferencia Bancaria"){
	$formapago ="Banco";
	}elseif ($row_orders["payment_method"] == "PayPal"){
	$formapago ="PayPal";
	}elseif ($row_orders["payment_method"] == "Wire Bank Transfer"){
	$formapago ="Banco";
	}elseif ($row_orders["payment_method"] == "Other"){
	$formapago ="Otros";
	}else{
	$formapago ="Efectivo";
}

// Segundo bucle
$gastosenvio = 0;
$totalfactura = 0;
$totalproductos= 0;
$iva = 0;
$comisiones=0;

$detalles = "SELECT orders_id, value, class, sort_order FROM orders_total WHERE orders_id = " . $row_orders["orders_id"];

$resultadodetalles = tep_db_query ($detalles);
	while ($row_resultados = tep_db_fetch_array($resultadodetalles)) {

$clase = $row_resultados["class"];	

switch ($clase) {
	case "ot_subtotal":
		$totalproductos = $totalproductos + $row_resultados["value"];	
		break;
	case "ot_discount":
		$totalproductos = $totalproductos + $row_resultados["value"];	
		break;
	case "ot_total":
		$totalfactura = $row_resultados["value"];
		break;
	case "ot_shipping":
		$gastosenvio = $gastosenvio +  $row_resultados["value"];
		break;
	case "ot_paypal_fee":
		$comisiones = $comisiones +  $row_resultados["value"];
		break;
	case "ot_surcharge":
		$comisiones = $comisiones +  $row_resultados["value"];
		break;
	default:
		$gastosenvio = $gastosenvio +  $row_resultados["value"];
		break;
		}
	}
	
	
// Si el envío es correo certificado no aplica IVA al total
// para españa
// si orders_status_id == 3 AND  left (tracking_id,2) = "CD"
// para europa no lleva iva el envio por correos??
// si orders_status_id == 3 AND  left (tracking_id,2) = "RR", si es PQ si lleva IVA
// ver que hacer si no hay MARCAR EN ROJO



$envios = "SELECT tracking_id FROM  orders_status_history WHERE orders_id =" .  $row_orders["orders_id"] . " AND orders_status_id=3";

$filas = tep_db_query($envios);
$query_result_array = tep_db_fetch_array($filas);

// extract the selected values from the results
$prefijo = substr($query_result_array['tracking_id'],0,2);
$sufijo = substr($query_result_array['tracking_id'],-2);
// echo $prefijo . ";" . $sufijo . " <br>";
	
//	$totalproductos
//	$gastosenvio
//	$comisiones
//	$totalfactura
//		$porcentajeiva = $aplicarIVA;
//		$zona = "UE";
//		$serie= $zona;
// $zona = "Canarias";
// $zona = "Peninsula";
// $zona = "NO UE";
// $zona = "UE";


switch ($zona) {
	case "Canarias":
			$base= $totalproductos;
			$IVAenvio= "Excl.";
	break;
	case "Peninsula":
//		if ($prefijo =="CD"  OR  $prefijo == "") {
		if ($row_orders["shipping_module"]=="correoscert_Normal") {
		// el envio no lleva IVA
			$base= $totalproductos;
			$IVAenvio= "Excl.";
			$AHORRO = $AHORRO  + ($gastosenvio *21 /100);
		}else{
			$base= $totalproductos + $gastosenvio ;
			$IVAenvio= "Sí";
		}
	break;
	case "UE":
		if ($prefijo == "RR" OR  $prefijo == "") {
			$base= $totalproductos ;
			$IVAenvio= "Excl.";
			$AHORRO = $AHORRO  + ($gastosenvio *21 /100);
		} else {
			$base= $totalproductos + $gastosenvio ;
			if ($porcentajeiva = 0) {
			$IVAenvio= "Sí";
			}else{
			$IVAenvio= "Excl.";
			}
		}
	break;
	case "NO UE":
			$base= $totalproductos ;
			$IVAenvio= "-";

	break;
}
			$base = $base / (1+ $porcentajeiva/100);
			$total_iva = $base * $porcentajeiva/100;




   if ($_GET['submitted']!=="CSV" ){
?>
					<tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='dataTableRow'">
<?
// Si el pedido no tiene NIF y el cliente si lo marca en rojo

if ($row_orders["billing_nif"]){
	$nif = $row_orders["billing_nif"];
}else{
	if  ($pais === "ES") {
	// echo $oID . "= " . $pais;
		$aviso = 1;
	
// $nif = $row_orders["billing_nif"];
$orders_query_raw = "select customers_nif FROM " . TABLE_CUSTOMERS . " where customers_id = " . $row_orders["customers_id"] ;
$orders_query = tep_db_query($orders_query_raw);

		while ($orders = tep_db_fetch_array($orders_query)) {
			$nif = "<font color='red'>" . $orders['customers_nif'] . "</font>" ;
		}

}


}
// Boton crear factura
$factura ="";
if ($row_orders["numero_factura"]){
$factura = str_pad($row_orders["numero_factura"], 6, "0", STR_PAD_LEFT);
$factura =($row_orders["serie"]) . tep_year ($row_orders["date_added"]) . "." . $factura;
}else{

		// $factura ='<input type="submit"  name="facturar" value="' . $row_orders["orders_id"] . '">';
		// puede ser tipo button?
		if ($aviso ==1){
			$factura =tep_image(DIR_WS_ICONS . 'cross.gif', 'Empaquetar');
		}else{
			if ($IVAenvio == "Sí") {
			$vatenv=$porcentajeiva;
			}else{
			$vatenv=0;
			}
			$factura ='<a href="' . tep_href_link('stats_ventas.php', tep_get_all_get_params(array('oID', 'action', 'serie', 'IVA')) . 'oID=' . $row_orders['orders_id'] . '&serie=' . $serie . '&IVA=' . $porcentajeiva . '&IVAENVIO=' . $vatenv .'&action=facturar' ) . '">' . tep_image(DIR_WS_ICONS . 'edit.png', 'Empaquetar') . $icon . '</a>&nbsp;&nbsp;';
		}

}


if ($formapago == "Banco"){

	}elseif ($formapago == "PayPal"){
		$color="green";
	}elseif ($formapago == "Banco"){
		$color="blue";
	}elseif ($formapago == "Otros"){

	}else{
	$color ="black";
}
if  ($pais != "ES") { 
$nif = "----";
}

$cabecera = array ('Factura',
                   'F. factura',
                   'Id',
                   'NIF',
                   'Email',
                   'País',
                   'Zona',
                   'Estado',
                   'Cuenta',
                   'Productos',
                   'Envio',
                   'IVA Envio',
                   'Comision',
                   'Base',
                   '% IVA',
                   'IVA',
                   'Total');



?>
					  <td class="dataTableContent" align="center"><?php echo $factura ; ?></td>
					  <td class="dataTableContent" align="center"><?php echo tep_date_short($row_orders["fechafactura"]); ?></td>
            <td class="dataTableContent" align="center"><font color="<?php echo $formapago;?>"><?php echo $row_orders["orders_id"]; ?></font></td>
					  <td class="dataTableContent" align="center"><?php echo $nif . $order->products['shipping_method']; ?></td>
					  <td class="dataTableContent" align="center"><?php echo $row_orders["customers_name"] ; ?></td>
					  <td class="dataTableContent" align="center"><?php echo $pais; ?></td>
					  <td class="dataTableContent" align="center"><?php echo $zona; ?></td>
					  <td class="dataTableContent" align="center"><?php echo $row_orders["orders_status"]; ?></td>
					  <td class="dataTableContent" align="left"><?php echo $formapago; ?></td>
					  <td class="dataTableContent" align="right"><?php echo $currencies->format($totalproductos); ?></td>
					  <td class="dataTableContent" align="right"><?php echo $currencies->format($gastosenvio);?></td>
					  <td class="dataTableContent" align="center"><?php 
					  if ($row_orders["shipping_module"]=="correoscert_Normal") {
              echo '<font color="green">';
					  }
					  echo $IVAenvio; 
					  if ($row_orders["shipping_module"]=="correoscert_Normal") {
              echo '</font>';
					  }					  
					  
					  
					  ?></td>

					  <td class="dataTableContent" align="right"><?php echo $currencies->format($comisiones); ?></td>
					  <td class="dataTableContent" align="right"><?php echo $currencies->format($base); ?></td>
					  <td class="dataTableContent" align="right"><?php echo $porcentajeiva . " %"; ?></td>
					  <td class="dataTableContent" align="right"><?php echo $currencies->format($total_iva) ; ?></td>
					  <td class="dataTableContent" align="right"><?php echo $currencies->format($totalfactura) ; ?></td>
                    </tr>
<?php
} else {
  $data2 = array( $factura,
                tep_date_short($row_orders["fechafactura"]),
                $row_orders["orders_id"],
                $nif,
                $row_orders["customers_name"],
                $pais,
                $zona,
                $row_orders["orders_status"],
                $formapago,
                $currencies->format($totalproductos),
                $currencies->format($gastosenvio),
                $IVAenvio,
                $currencies->format($comisiones),
                $currencies->format($base),
                $porcentajeiva . " %",
                $currencies->format($total_iva),
                $currencies->format($totalfactura)          
              );
}



$csv_output .= "\n";
$Tbase = $Tbase + $base;
$TIVA = $TIVA + $total_iva;
$TGastos = $TGastos + $gastosenvio;
$Ttotal = $Ttotal + $totalfactura;
}


   if ($_GET['submitted']!=="CSV" ){
       ?>
				</table>
			</td>
		</tr>
	</table>
<?php

echo "Base:            " . tep_decimal_esp ($Tbase) . " € <br />";
echo "IVA:             " . tep_decimal_esp($TIVA) . " € <br />";
echo "Gastos de envio: " . tep_decimal_esp($TGastos) . " € <br />";
echo "Total:           " . tep_decimal_esp($Ttotal) . " € <br /><br />";
echo "IVA ahorrado en envíos certificados: " . tep_decimal_esp ($AHORRO) . " € <br />";
   }

   if ($_GET['submitted']=="CSV" ){
   header("Content-Type: application/force-download\n");
header("Cache-Control: no-cache, must-revalidate");   
header("Pragma: public");
header("Content-Disposition: attachment; filename=rel_" . $tipo .  "s_" . date("Ymd_H_i") . ".txt");
print $csv_output;
  exit;
}




function tep_decimal_esp ($numero){
 return number_format($numero, 2, ',', '.');
}


////////////////////////////////////////////////////////////////////////////////////////////////
// Function    : tep_get_country_id
// Arguments   : country_name        country name string
// Return      : country_id
// Description : Function to retrieve the country_id based on the country's name
////////////////////////////////////////////////////////////////////////////////////////////////
    function tep_get_country_id($country_name) {
  
      $country_id_query = tep_db_query("select * from " . TABLE_COUNTRIES . " where countries_name = '" . $country_name . "'");
  
      if (!tep_db_num_rows($country_id_query)) {
        return 0;
      }
      else {
        $country_id_row = tep_db_fetch_array($country_id_query);
        return $country_id_row['countries_id'];
      }
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////
   //
   // Function    : tep_get_country_iso_code_2
    //
    // Arguments   : country_id        country id number
    //
    // Return      : country_iso_code_2
    //
    // Description : Function to retrieve the country_iso_code_2 based on the country's id
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////
    function tep_get_country_iso_code_2($country_id) {
  
      $country_iso_query = tep_db_query("select * from " . TABLE_COUNTRIES . " where countries_id = '" . $country_id . "'");
  
      if (!tep_db_num_rows($country_iso_query)) {
        return 0;
      }
      else {
        $country_iso_row = tep_db_fetch_array($country_iso_query);
        return $country_iso_row['countries_iso_code_2'];
      }
    }
	
    ////////////////////////////////////////////////////////////////////////////////////////////////
   //
   // Function    : tep_get_country_iso_code_3
    //
    // Arguments   : country_id        country id number
    //
    // Return      : country_iso_code_3
    //
    // Description : Function to retrieve the country_iso_code_3 based on the country's id
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////
    function tep_get_country_iso_code_3($country_id) {
  
      $country_iso_query = tep_db_query("select * from " . TABLE_COUNTRIES . " where countries_id = '" . $country_id . "'");
  
      if (!tep_db_num_rows($country_iso_query)) {
        return 0;
      }
      else {
        $country_iso_row = tep_db_fetch_array($country_iso_query);
        return $country_iso_row['countries_iso_code_3'];
      }
    }
 
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    // Function    : tep_get_zone_id
    //
    // Arguments   : country_id        country id string
    //               zone_name        state/province name
    //
    // Return      : zone_id
    //
    // Description : Function to retrieve the zone_id based on the zone's name
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    function tep_get_zone_id2($country_id, $zone_name) {
  
      $zone_id_query = tep_db_query("select * from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' and zone_name = '" . $zone_name . "'");
  
      if (!tep_db_num_rows($zone_id_query)) {
        return 0;
      }
      else {
        $zone_id_row = tep_db_fetch_array($zone_id_query);
        return $zone_id_row['zone_id'];
     }
    }
    
  ////////////////////////////////////////////////////////////////////////////////////////////////

	function tep_year($raw_datetime) {
    if ( ($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == '') ) return false;

    $year = (int)substr($raw_datetime, 0, 4);

    return  $year;
  }

function facturar ($oID, $get_serie, $year, $IVA, $IVAENVIO) {
//	include(DIR_WS_CLASSES . 'order.php');
//	$order = new order($oID);
	// Busca si existe ya la factura para no duplicarla
	$factura = "SELECT * from facturas where orders_id = " . $oID;
	$facturadetalles = tep_db_query ($factura);
	if (!tep_db_num_rows($facturadetalles)) { // Si no existe una factura para ese pedido la crea

		// Busca la última factura del año y serie elegidos
		$maxfactura = "SELECT numero_factura FROM facturas WHERE serie ='" . $get_serie . "' and year_factura = " . $year . " ORDER BY numero_factura DESC LIMIT 1";
		$maxfactura_query = tep_db_query ($maxfactura);
		if (tep_db_num_rows($maxfactura_query)) {
		echo $row_numero['numero_factura'];
		while ($row_numero = tep_db_fetch_array($maxfactura_query)) {
		// $numero_factura= str_pad($row_numero['numero_factura'] +1, 6, "0", STR_PAD_LEFT);
		$numero_factura= $row_numero['numero_factura'] +1;
//			echo "PEDIDO " . $oID . " - FACTURA: " . $get_serie . $year . str_pad($row_numero['numero_factura'] +1, 6, "0", STR_PAD_LEFT);
		}
		}else{
			echo "no hay factura anterior. COMENZAMOS LA NUMERACION DEL AÑO PARA LA SERIE " . $get_serie;
			$numero_factura= 1;
//			echo "PEDIDO " . $oID . " - FACTURA: " . $get_serie . $year . str_pad($row_numero['numero_factura'] +1, 6, "0", STR_PAD_LEFT);
		}
		
		// BUSCA LA FECHA DE PAGO:
			$fechafactura = "SELECT * from orders_status_history where orders_id = " . $oID . " AND orders_status_id = 5";
			$fechafacturaquery = tep_db_query ($fechafactura);
			$fecha = tep_db_fetch_array($fechafacturaquery);
			 
				$sql_data_array = array('orders_id' => $oID,
			//							'fechafactura' => date("Y-m-d H:i:s"),
										'fechafactura' => $fecha['date_added'],
										'serie' => $get_serie,
										'numero_factura' =>  $numero_factura,
										'IVA' =>  $IVA,
										'id_factura' =>  $get_serie . $year . str_pad($numero_factura, 6, "0", STR_PAD_LEFT),
										'year_factura' => $year,
										'IVA_envio' => $IVAENVIO); // Año para la factura
					
					tep_db_perform('facturas', $sql_data_array, 'insert'); 
		global $language;
//        include ("pdf_invoice.php");
		//while ($row_fecha = tep_db_fetch_array($fecha_query)) {
			
			// Establece la serie
			// Busca el año
//				$año = tep_year ($row_fecha['date_added']);
			// Busca la factura mayor en ese año y serie
		//$factura =($row_orders["serie"]) . tep_year ($row_orders["date_added"]) . "." . $factura;

		//}
	}else{
	// Si ya existe la factura da un error
	// trigger_error ( string $error_msg [, int $error_type = E_USER_NOTICE ] )
	trigger_error("El pedido " . $oid . " ya está facturado");
	}
}

?>


<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
