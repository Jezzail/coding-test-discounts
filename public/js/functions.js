$(function() {
	//trigger when you load an order file
	$("#order").change(function(e) {
	    var problems = false;
	    var error_message = '';
	    
	    //Check to load only JSON files
	    var ext = this.value.match(/\.([^\.]+)$/)[1];
	    ext = ext.toLowerCase();
	    switch(ext){
	        case 'json':
	            break;
	        default:
	        	problems = true;
	        	error_message += "Sorry, only JSON files are allowed.<br>";
	    }
	    
	    if(!problems){//if there is no problem, submit the form
	    	$('#form').submit();
	    }else{//else, show error messages
	    	$('#errors').html(error_message);
	    }
	});
	
	//when the form submits, send the file data to read the order details
	$("form#form").submit(function(){
	    var formData = new FormData(this);
	    console.log(formData);
	    $.ajax({
	        url: 'ajax.php?action=submit',
	        type: 'POST',
	        data: formData,
	        success: function (data) {
	            $('#order_data').html(data);//display the data in the div
	        },
	        cache: false,
	        contentType: false,
	        processData: false
	    });

	    return false;
	});
});

//functions that make an AJAX call to load the discounts
function discounts(){
	var order = $("#order_hidden").val();//obtaining the json data
    $.ajax({
	    data: {"order": order},
	    type: "POST",
	    url: "ajax.php?action=discount",
	})
	 .done(function( data, textStatus, jqXHR ) {
		 if(data){
			 $('#order_data').html(data);//display the data in the div
		 }else{
			 $('#errors').html("Sorry, there was an error loading the data, try again later.<br> ");
		 }
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {
		 $('#errors').html("Sorry, there was an error loading the data, try again later.<br> ");
	});
}

//functions that make an AJAX call to load the discounts
function revenue(total, customer){
    $.ajax({
	    data: {"total": total, "customer": customer},
	    type: "POST",
	    url: "ajax.php?action=revenue",
	})
	 .done(function( data, textStatus, jqXHR ) {
		 if(data){
			 $('#revenue').html(data);//display the data in the div
		 }
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {
		 $('#errors').html("Sorry, there was an error loading the data, try again later.<br> ");
	});
}