<style type="text/css"><!--

/* data table from stylesheet
.dataTableHeadingRow { background-color: #C9C9C9; }
.dataTableHeadingContent { color: #ffffff; font-weight: bold; }
.dataTableRow { background-color: #F0F1F1; }
.dataTableRowSelected { background-color: #DEE4E8; }
.dataTableRowOver { background-color: #FFFFFF; cursor: pointer; cursor: hand; }
.dataTableContent { color: #000000; }
*/
table {
    border-top-left-radius: 15px;

}

input {
  border-width: 1px;
  background-color:transparent;
  border-color:transparent;
}
input:hover {
  border-width: 1px;
  border-color:orange;
  background-color:white;
}
input[readonly]
{
  cursor:not-allowed;
}
.align-right {
  text-align:right;
  
}
.align-left {
  text-align:left;
  
}
.align-center {
  text-align:center;
}
.valign-middle {
  text-align:middle;
}

.valign-top {
  text-align:middle;
}
.valign-bottom {
  text-align:bottom;
}


select {
  border-width: 1px;
  background-color:transparent;
  border-color:transparent;
}

select:hover {
  border-width: 1px;
  border-color:orange;
  background-color:white;
}


.dataTableHeadingRow {
/*  border: 6px solid #4297d7; */
  background-color: #5c9ccc ;
  
  table {
        border-collapse: separate;
        background-clip: padding-box;
    background-color: #369;
        border: 5px solid red;
    border-radius: 5px;
  }
  
}
.dataTableHeadingContent {
  /* no va? */border-radius: 5px;background-clip: padding-box;
}

.rowOver tr:hover { 
  background: #DEE4E8;
}

#shipping_quote tr:hover { 

  cursor:pointer;
  }

tr.border_bottom td {
  border-bottom: 1px solid #B4B5B0;;
}
table { 
    border-collapse: collapse; 
}
#modal {
    display: none;
  position: fixed; /* or absolute */
  top: 40%;
  left: 50%;
    width: 64px;
    height: 64px;
    padding:5px 2px 0px;
    border: 6px solid #ababab;
    box-shadow:1px 1px 10px #ababab;
    border-radius:20px;
    background-color: white;
    z-index: 1002;
    text-align:center;
    overflow: auto;
}
#fade {
    display: none;
    position:absolute;
    top: 0%;
    left: 0%;
    width: 100%;
    height: 1000%;
    background-color: #ababab;
    z-index: 1001;
    -moz-opacity: 0.9;
    opacity: .70;
    filter: alpha(opacity=80);
}
.ui-icon-red {
background-image: url('../ext/jquery/ui/redmond/images/ui-icons_cd0a0a_256x240.png');
}
.ui-icon-white {
background-image: url('../ext/jquery/ui/redmond/images/ui-icons_469bdd_256x240.png');
float:left;
margin-right:5px;
}
.ui-icon-info {
background-image: url('../ext/jquery/ui/redmond/images/ui-icons_d8e7f3_256x240.png');
float:left;

}

  .SubTitle {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 11px;
  font-weight: bold;
  color: #29ADB8;
  }
  
  .hidden
  {
  position: absolute;
  left: -1500em;
  }

  .updateBlock div {
 float: left
  }
  
  .update1 {
  background-color: #DBF5F7;
  position: relative;
  width: 100%;
  
  height: 22px;
	  }
  
  .update2 {
  background-color: #AFE9ED;
    position: absolute;
	width: 10px;
  right: 336px;
   height: 22px;
  }
  
  .update3 {
  background-color: #97E1E8;
    position: absolute;
	width: 10px;
  right: 324px;
   height: 22px;
  }
  
  .update4 {
  background-color: #79DAE1;
  position: absolute;
  width: 200px;
  right: 122px;
   height: 22px;
  }
  
  .update5 {
  background-color: #4DCCD7;
  position: absolute;
  width: 120px;
  right: 0px;
   height: 22px;
  }
  
  .tableHeader {
  background-color: #C9C9C9;
  font-family: Verdana, Arial, sans-serif; 
  font-size: 10px; 
  color: #ffffff; 
  font-weight: bold; 
  /* height: 15px; */
 /* I want to insert a height definition but this breaks the layout in IE7.  Curses to you Microsoft, curses!!! */
 
  }
  
  #header ul {
   margin: 0;
   padding: 0;
   float: right;
   }
   
   #header li {
   display: inline;
   }

   
   #headerTitle {
   margin: auto;
   padding: 0 0 0 0;
   left: 20px;
   float: left;
     
   }
   
   #ordersMessageStack {
   left: 30px;
   width:100%;
   clear: both;
   }
   

   

	

	
<?php
  if ($order->customer['zone_id'] != '') {
?>
#customerStateInput { visibility: hidden; display: none; }
<?php
  } else {
?>
#customerStateMenu { visibility: hidden; display: none; }
<?php
  }

  if ($order->delivery['zone_id'] != '') {
?>
#deliveryStateInput { visibility: hidden; display: none; }
<?php
  } else {
?>
#deliveryStateMenu { visibility: hidden; display: none; }
<?php
  }

  if ($order->billing['zone_id'] != '') {
?>
#billingStateInput { visibility: hidden; display: none; }
<?php
  } else {
?>
#billingStateMenu { visibility: hidden; display: none; }
<?php
  }
?>
#shippingAddressEntry { visibility: <?php echo (($_SESSION['shipping_same_as_billing'] == 'on') ? 'hidden' : 'visible') ?>; display: 
<?php echo (($_SESSION['shipping_same_as_billing'] == 'on') ? 'none' : 'table-row') ?>; }

#billingAddressEntry { visibility: <?php echo (($_SESSION['billing_same_as_customer'] == 'on') ? 'hidden' : 'visible') ?>; display: 
<?php echo (($_SESSION['billing_same_as_customer'] == 'on') ? 'none' : 'table-row') ?>; }


--></style>