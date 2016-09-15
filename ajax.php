<?php
	//get the action
	if(!isset($_GET['action'])){
		print "ERROR";
	}
	
	switch ($_GET['action']){
		case 'submit'://when you load an order file
			//getting the customers data
			$file_customer = file_get_contents("./data/customers.json");
			$json_customer = json_decode($file_customer, true);
			
			//getting the products data
			$file_product = file_get_contents("./data/products.json");
			$json_product = json_decode($file_product, true);
			
			//getting the order data from the uploaded file
			$file_order = file_get_contents($_FILES["order"]["tmp_name"]);
			$json_order = json_decode($file_order, true);
			
			//get the customer name
			$customer = getvaluefromarray($json_customer, 'id', $json_order['customer-id'], 'name');			
			
			//create a table to display the data (Quantity, Product, Unit, Total)
			$data = "<div class='container'><table class='tableorder'><tr class='tabledark'><th colspan='4'>Customer: $customer</th></tr>";
			$data .= "<tr class='tabledark bordertop'><th class='small'>Quantity</th><th>Product</th><th class='small'>Unit</th><th class='small'>Total</th></tr>";
			$cont = 0;
			foreach($json_order['items'] as $key => $val){
				if($cont%2 == 0){//this set the class of the row
					$class = 'tableodd';
				}else $class = 'tableeven';
				$cont++;
				
				//if there is any data
				if(!empty($val) && $val['product-id']!=''){
					//get the description of the product
					$product = getvaluefromarray($json_product, 'id', $val['product-id'], 'description');
					//display the data of the item
					$data .= "<tr class='$class bordertop'><td>".$val['quantity']."</td><td>$product</td><td>".$val['unit-price']." €</td><td>".$val['total']." €</td></tr>";
				}
			}
			//and the total
			$data .= "<tr class='tabledark bordertop'><td colspan='3'>Total</td><td>".$json_order['total']." €</td></tr>";
			$data .= "</table>";
			
			//display a button that calculates the discounts, and a hidden input that contains the json data
			$data .= "<div class='uploadcontainer'>
				    <input name='discount' id='discount' type='button' class='button' value='DISCOUNT' onclick='discounts()'/>
				    <input type='hidden' id='order_hidden' name='order_hidden' value='".json_encode($json_order)."'>
				</div></div>";
			
			echo $data;
			
			break;
			
		case 'discount'://to calculte the discounts of an order
			//getting the customers data
			$file_customer = file_get_contents("./data/customers.json");
			$json_customer = json_decode($file_customer, true);
			
			//getting the products data
			$file_product = file_get_contents("./data/products.json");
			$json_product = json_decode($file_product, true);
			
			//decodding the order data from the hidden input
			$json_order = json_decode($_POST["order"], true);
			
			//add field total value of discounts, and total value after discounts
			$json_order['total_discounts'] = 0;
			$json_order['total_total'] = $json_order['total'];
			
			//here we call the functions that apply the discount, we can add more discounts and call them here
			$json_order = discountSwitches($json_order, $json_product);
			$json_order = discountTools($json_order, $json_product);
			$json_order = discountCustomer($json_order, $json_customer);//this is last because affects the total price after discounts
			
			//get the customer name
			$customer = getvaluefromarray($json_customer, 'id', $json_order['customer-id'], 'name');

			//create a table to display the data (Quantity, Product, Unit, Total)
			$data = "<div class='container'><table class='tableorder'><tr class='tabledark'><th colspan='4'>Customer: $customer</th></tr>";
			$data .= "<tr class='bordertop tabledark'><th class='small'>Quantity</th><th>Product</th><th class='small'>Unit</th><th class='small'>Total</th></tr>";
			$cont = 0;
			foreach($json_order['items'] as $key => $val){
				if($cont%2 == 0){//this set the class of the row
					$class = 'tableodd';
				}else $class = 'tableeven';
				$cont++;
				
				//if there is any data
				if(!empty($val) && $val['product-id']!=''){
					//get the description of the product
					$product = getvaluefromarray($json_product, 'id', $val['product-id'], 'description');
					
					//display the data of the item
					$data .= "<tr class='bordertop $class'><td>".$val['quantity']."</td><td>$product</td><td>".$val['unit-price']." €</td><td>".$val['total']." €</td></tr>";
					//display if it has the discount 2
					if(isset($val['discount_2']) && $val['discount_2']!=''){
						$data .= "<tr class='tablediscount'><td></td><td>Discount 6x5 switches</td><td></td><td>-".$val['discount_2']." €</td></tr>";
					}
					//display if it has the discount 3
					if(isset($val['discount_3']) && $val['discount_3']!=''){
						$data .= "<tr class='tablediscount'><td></td><td>Discount 20% tools</td><td></td><td>-".$val['discount_3']." €</td></tr>";
					}
				}
				
			}
			//display if it has the discount 3
			if(isset($json_order['discount_1']) && $json_order['discount_1']!=''){
				$data .= "<tr class='bordertop tablediscount'><td></td><td>Customer discount 10% total</td><td></td><td>-".$json_order['discount_1']." €</td></tr>";
			}
			//and the total
			$data .= "<tr class='bordertop tabledark'><td colspan='3'>Total</td><td>".$json_order['total_total']." €</td></tr>";
			$data .= "</table>";
			
			//display a button that add the total to the customer revenue
			$data .= "<div id='revenue' class='revenue'>
				<div class='uploadcontainer'>
				    <input name='revenue' type='button' class='button' value='ADD TOTAL TO CUSTOMER' onclick='revenue(\"".$json_order['total_total']."\",\"".$json_order['customer-id']."\")'/>
				</div></div></div>";
			
			echo $data;
			
			break;
			
		case 'revenue'://add the total to the revenue of the customer
			//getting the customers data
			$file_customer = file_get_contents("./data/customers.json");
			$json_customer = json_decode($file_customer, true);
			
			//loop the data
			foreach($json_customer as $index => $val) {
				if($val['id'] == $_POST["customer"]){//if the id is found, add total to the revenue
					$json_customer[$index]['revenue']+=$_POST["total"];
				}
		    }
		    
			//Then re-encode it and save it back in the file:
			$newJsonString = json_encode($json_customer);
			if(file_put_contents('./data/customers.json', $newJsonString)){
				echo "TOTAL ADDED TO THE REVENUE OF THE CUSTOMER";
			}
			
			break;
	}

	
	//function for the discount 1: A customer who has already bought for over € 1000, gets a discount of 10% on the whole order.
	function discountCustomer($json_order, $json_customer){
		//obtain the revenue of the customer.
		$revenue = getvaluefromarray($json_customer, 'id', $json_order['customer-id'], 'revenue');
		if($revenue > 1000){//if its over 1000, 10% discount
			$discount = $json_order['total_total'] * (10 / 100);
			$discount = floor($discount*100)/100;//usign this to make sure it has only 2 decimals
			$json_order['discount_1']=$discount;
			$json_order['total_discounts'] += $discount;
			$json_order['total_total'] -= $discount;
		}
		
		return $json_order;
	}
	
	//function for the discount 2: For every products of category "Switches" (id 2), when you buy five, you get a sixth for free.
	function discountSwitches($json_order, $json_product){
		foreach($json_order['items'] as $key => $val){
			$category = getvaluefromarray($json_product, 'id', $val['product-id'], 'category');
			
			if($category==2 && $val['quantity']>5){
				$multiple = floor($val['quantity']/6);
				$discount = $multiple*$val['unit-price'];
				$json_order['items'][$key]['discount_2']=$discount;
				$json_order['total_discounts'] += $discount;
				$json_order['total_total'] -= $discount;
			}
		}
		
		return $json_order;
	}
	
	//function for the discount 3: If you buy two or more products of category "Tools" (id 1), you get a 20% discount on the cheapest product.
	function discountTools($json_order, $json_product){
		//loop the items
		foreach($json_order['items'] as $key => $val){
			//obtain the category of the product
			$category = getvaluefromarray($json_product, 'id', $val['product-id'], 'category');			
			if($category==1){//if the category is 1 (Tools), we add the item data into a new array
				$array_tools[$key]=$val;
			}
		}
		//here we have all the items that are category 1
		if(count($array_tools)>=2){//if there is more than 2 items
			//using an anonymous function() starting PHP 5.3, use a sort algorithm, so the item with the cheapest price get the discount
			uasort($array_tools, function($a, $b) {
		    	return $a['unit-price'] - $b['unit-price'];
			});
		}
		//with this, we have the key of the fisrt item (cheapest unit price)
		reset($array_tools);
		$first_key = key($array_tools);
		
		//20% discount
		$discount = $json_order['items'][$first_key]['total'] * (20 / 100);
		$discount = floor($discount*100)/100;//usign this to make sure it has only 2 decimals
		$json_order['items'][$first_key]['discount_3']=$discount;
		$json_order['total_discounts'] += $discount;
		$json_order['total_total'] -= $discount;
		
		return $json_order;
	}
	
	//simple array to get the value of an indexed array from a search
	function getvaluefromarray($array, $id, $search, $return) {
	    foreach($array as $index => $val) {
	        if($val[$id] == $search) return $val[$return];
	    }
	    return FALSE;
	}
	
?>