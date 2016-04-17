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
