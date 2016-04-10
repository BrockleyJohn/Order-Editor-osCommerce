<style type="text/css"><!--
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
background-image: url('../ext/jquery/ui/redmond/images/ui-icons_469bdd_256x240.png');
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
   
   #customerInfoBlock {
   position: relative;
   top: 0;
   left: 0;
   width: 70%;
   
   
   }
   
   
   
  #customerInfoBlock ul {
  font-family: Verdana, Arial, sans-serif; 
  font-size: 10px; color: #000000;
  margin-left: 0;
  padding-left: 0;
  
   }
   
   #customerInfoBlock li {
   list-style-type: none;
   display: table-row;
   margin-top: 1;
   padding: 0 0 0 5px;
   
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
   
   
   #dhtmltooltip {
   position: absolute;
   width: 300px;
   border: 2px solid black;
   padding: 2px;
   background-color: lightyellow;
   visibility: hidden;
   z-index: 100;
   }
   
    #infoLeftColumn {
	top: 0;
    left: 0;
    float: left;
	width: auto;
	margin-right: 10px;
	
	
	}
	
	#infoRightColumn {
	width: auto;
	margin-left: 10px;
	
	
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