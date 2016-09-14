<?php
	/**
	 * Author: Pablo Abril
	 * 
	 * This is a simple service that calculates discounts for orders.
	 * Orders must be loaded in .json file with the correct structure:
	 * {
		  "id": "",
		  "customer-id": "",
		  "items": [
		    {
		      "product-id": "",
		      "quantity": "",
		      "unit-price": "",
		      "total": ""
		    }
		  ],
		  "total": ""
		}
	 */
?>
<html>
	<head> 
		<title>Discount Calculator</title>
		<link rel="stylesheet" href="public/css/style.css">
		<script type="text/javascript" src="http://code.jquery.com/jquery-3.1.0.js"></script>
    	<script type="text/javascript" src="public/js/functions.js"></script>
    </head>
    <body>
    	<div class='container'>
    		<form id='form' method="post" enctype="multipart/form-data">
	    		<div class='titlecontainer'>
	    			<h2>DISCOUNT CALCULATOR</h2>
	    			<p>Please, upload your order file to see the details</p>
	    		</div>
	    		<div class="uploadcontainer">
				    <button class="button">UPLOAD</button>
				    <input name="order" id='order' type="file" class="upload" accept=".json"/>
				</div>
				<div class='errors' id='errors'>
		    		<?=$message?>
		    	</div>
    		</form>
    	</div>
    	<div id='order_data'>
    	</div>
    </body>
</html>
