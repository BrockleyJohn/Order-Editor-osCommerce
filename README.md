Order editor 4 oscommerce responsive
====================================
- Removed non-AJAX methods
agregado     http://www.itgeared.com/articles/1506-how-to-display-image-spinner-ajax-request/
TODO:
====
- ¡OJO! no suma fixed charge

- Admin module to install/uninstall and remove from main file
  Tablas a añadir para copiar datos:

- Agregar productos en su idioma
- Probar qué hace con los gastos de nevío al cambiar de país
- QT pro options - check if everything works
- Allow editing non-standard totals, maybe add option to choose what totals can be edited?
en normal se puede, en ajax non-AJAX
- añadir nota de que es necesario copiar la clase 'order_total.php' del catálogo al dir edit_orders
- Ver si se puede cargar dinamicamente la lista de zonas
- limitar input a numeros y puntos
- dropdowns con +/- para precios de atributos
- Estilos de tablas e inputs
- Check if a order total can be auto calculated?
- Check for newer fields on edge? ISBN?
- Add to orders.php (standard and paypal vers.)
- Rutina que compruebe si el cliente y biling es el mismo
- Opción de reordenar totales y de añadir nuevos que no sean custom 
- Centrar gif pensando
- ver si se puede mandar parte del js a un archivo aparte
- Eliminar shipping_class de la tabla orders si se elimina el metodo de envio
- Permitir metodos de envio alternativos
- ocultar columna currency value si solo hay una moneda
- espacio alrededor de submit comments

Clarificar el cambio de divisa
BUGS
====
- No se ve el status en la tabla de comentarios
- ¿borde inferior tabla totales recortado????
- Tipo de letra muy grande en comentarios
- ver qué hace la función selectRowEffect!!!!!
- Ver por qué ahora hace un reload despues de insertar comentarios (igual es por las cabeceras en JS)
- Comprobar si funciona el email - NO
- Payment method and contact information doesn't show fixed width in chrome
- Cleanup styles, functions and jscript
- Revisar eso de shipping same as billing

You can choose to download;

GOLD: https://github.com/gburton/Responsive-osCommerce/archive/v2.3.4-GOLD.zip
EDGE: https://github.com/gburton/Responsive-osCommerce/archive/master.zip

Help to move the Project forward
================================

I need your help to move this Project forward.  At the moment, this project is done on my own, as and when time can be given.  
To allow me to give more time to this Project, I need your support;

- give time for testing new code and/or getting involved in discussions
- give code to the project - create a github account, fork and start coding

If you cannot give time or code, please give a Pledgie.  Simply click on the Donate button below to donate via Paypal...

<a href='https://pledgie.com/campaigns/31724'><img alt='Click here to lend your support to: Responsive osCommerce and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/31724.png?skin_name=chrome' border='0' ></a>

Minimum PHP Version
===================

5.3

If you are on an older PHP version, you may find errors.  Update your PHP version.

Looking for Loaded?
===================

This will never become a "loaded" osCommerce.  So if you are looking for something with a lot of addons pre-installed, this is not for you.  
We are striving to make the front end 100% modular, so good add-on makers can create modules which are simple to (un)install, changing no core code.



Demo Site
=========

Check out the demo site at http://template.me.uk/2334bs3/ - please note that this might be slightly behind "Edge", or might have modules that are about to be added to "Edge".  More or less, this is how Responsive osCommerce looks out of the box.


Installation
============

Install as if this is a new osCommerce installation.
You can now play with the Modular aspect we've introduced.

Admin > Modules > Header Tags > {install}
Admin > Modules > Boxes > {install}
Admin > Modules > Content > {install}
Admin > Modules > Navbar Modules > {install} 

All of these can be sorted using the sort order, lowest is displayed first.

Database Conversion Script
==========================

To go from an older osCommerce to this one, this Script might help:
http://forums.oscommerce.com/topic/399678-234normal-to-234responsive-database-conversion-script/

Got Questions, Comments or Concerns
===================================

http://forums.oscommerce.com/topic/396152-bootstrap-3-in-234-responsive-from-the-get-go/

How to keep a clean Master copy using Github
============================================

I have put together a couple of videos.
1.  how to create a new Github account and Fork this project.
2.  how to check for new commits to this project and pull them into your own Fork.

You can find these videos at http://forums.oscommerce.com/topic/396152-bootstrap-3-in-2334-responsive-from-the-get-go/?p=1709648
