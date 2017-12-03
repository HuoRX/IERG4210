<?php
include_once('lib/csrf.php');
include_once('lib/auth.php');
include_once('checkout-process.php');

if (!$_SESSION['authtoken'] && !$_SESSION['nortoken']){
	header('Location: login.php');
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
  <title> ShoppingMart</title>
  <link rel="stylesheet" href="stylesheet.css" type="text/css">
</head>

<body>

  <nav>
    <a href="homepage2b.html">Home</a>
  </nav>

<div id="shoppcart">
		<p>shopping cart</p>
		<ul id="Shopping"></ul>
		<ul id="Price"></ul>
		<form method="POST" action="https://www.sandbox.paypal.com/cgi-bin/webscr" onsubmit="return cart_submit(this);">
				<input type="hidden" name="cmd" value="_cart" />
				<input type="hidden" name="upload" value="1" />
				<input type="hidden" name="business" value="huoruixin1996-company2@163.com" />
				<input type="hidden" name="currency_code" value="HKD" />
				<input type="hidden" name="charset" value="utf-8" />
				<input type="hidden" name="custom" value="0" />
				<input type="hidden" name="invoice" value="0" />
				<input type="submit" value="CheckOut">
				<ul id="orderInfo"></ul>
    </form>
</div>


<section id="categoryListPanel">
  <div>
    <p> Category </p>
    <ul  id="categoryList"></ul>
  </div>
<section id="categoryListPanel">

	<section id ="userinfo">
		<fieldset>
		<legend>User Information</legend>
		<form id="logout" method="POST" action="auth-process.php?action=logout">
			<label for="email">User Email:</label>
			<div>
			<input type="text" name="email" size="20" value="<?php echo loginstate(); ?>" readonly/>
		  </div>
			<input type="submit" value="Logout"/>
		</form>
		</fieldset>
	</section>


<section id="allProducts">
  <div>
    <ul class="table" id="productList"></ul>
  </div>
</section>

<section id="productDetail">
	<div class="table2" id="productInfo">
	</div>
</section>


<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript">

function updateHTML(){

  j=0;
	myLib.get({action:'prod_fetchall'}, function(json){
		// loop over the server response json
		//   the expected format (as shown in Firebug):
		for (var listItems = [],
				i = 0, prod; prod = json[i]; i++) {

					if(localStorage.getItem(parseInt(prod.pid))===null){}
					else{
						listItems.push('<input type="hidden" name="item_number_',j+1,'" value="'+parseInt(prod.pid)+'"/>');
						listItems.push('<input type="hidden" name="item_name_',j+1,'" value="'+prod.name.escapeHTML()+'"/>');
						listItems.push('<input type="hidden" name="quantity_',j+1,'" value="'+parseInt(localStorage.getItem(parseInt(prod.pid)))+'"/>');
						listItems.push('<input type="hidden" name="amount_',j+1,'" value="'+parseInt(prod.price)+'"/>');
						j++;
					}

		}
		el('orderInfo').innerHTML = listItems.join('');
		//alert(listItems.join(''));

	});
}

function cart_submit(form){
//alert("Yes!");
	var buyList={};
	for(var key in localStorage){
		buyList[key]=parseInt(localStorage.getItem(key));
		//combine the pid and quantity into array
	}
	//alert("2");
	updateHTML();
	myLib.processJSON(
				"checkout-process.php",                                      //para 1
				{action: "handle_checkout", list:JSON.stringify(buyList)},   //para 2
				function(returnValue){                                       //para 3
				form.custom.value=returnValue.digest;
				form.invoice.value=returnValue.invoice;
				form.submit();
				localStorage.clear();
				updateCart();
			},
				{method:"POST"});
																					//para 4
	return false;
}

function editCart(id,flag){
	if(flag==1){
		var quantity = 1+JSON.parse(localStorage.getItem(id));
		localStorage.setItem(id, JSON.stringify(quantity));
	}
	else if(flag==2){
		var quantity = JSON.parse(localStorage.getItem(id))-1;
		if(quantity==0){
			localStorage.removeItem(id);
		}
		else{
			localStorage.setItem(id, JSON.stringify(quantity));
		}
	}
	else if(flag==3){
		localStorage.removeItem(id);
	}

	totPrice=0;

	myLib.get({action:'prod_fetchall'}, function(json){
		// loop over the server response json
		//   the expected format (as shown in Firebug):
		for (var listItems = [],
				i = 0, prod; prod = json[i]; i++) {

					if(localStorage.getItem(parseInt(prod.pid))===null){}
					else{
						listItems.push('<li id="prod' , parseInt(prod.pid) ,'">' ,prod.name.escapeHTML() ,
						'Price: $', prod.price.escapeHTML(),'   Quantity:', localStorage.getItem(parseInt(prod.pid)),
						'<button type="button" onclick="editCart(',parseInt(prod.pid),',1)">+</button><button type="button" onclick="editCart(',parseInt(prod.pid),',2)">-</button><button type="button" onclick="editCart(',parseInt(prod.pid),',3)">Delete</button>');

						totPrice += localStorage.getItem(parseInt(prod.pid)) * prod.price;
					}

		}
		el('Shopping').innerHTML = listItems.join('');
		ListItems2 = [];
		ListItems2.push('Total Price: $',parseInt(totPrice));
		el('Price').innerHTML = ListItems2.join('');

	});

}

function buttonEvent(id){
	//var id = e.target.parentNode.id.replace(/^prod/, '');


	if (localStorage.getItem(id) === null) {
    localStorage.setItem(id, JSON.stringify(0));

	}

	var quantity = 1+JSON.parse(localStorage.getItem(id));

	localStorage.setItem(id, JSON.stringify(quantity));
	// Retrieve the object from storage

	totPrice=0;

	myLib.get({action:'prod_fetchall'}, function(json){
		// loop over the server response json
		//   the expected format (as shown in Firebug):
		for (var listItems = [],
				i = 0, prod; prod = json[i]; i++) {

					if(localStorage.getItem(parseInt(prod.pid))===null){}
					else{
						listItems.push('<li id="prod' , parseInt(prod.pid) ,'">' ,prod.name.escapeHTML() ,
						'Price: $', prod.price.escapeHTML(),'   Quantity:', localStorage.getItem(parseInt(prod.pid)),
						'<button type="button" onclick="editCart(',parseInt(prod.pid),',1)">+</button><button type="button" onclick="editCart(',parseInt(prod.pid),',2)">-</button><button type="button" onclick="editCart(',parseInt(prod.pid),',3)">Delete</button>');

						totPrice += localStorage.getItem(parseInt(prod.pid)) * prod.price;
					}

		}
		el('Shopping').innerHTML = listItems.join('');

		ListItems2 = [];
		ListItems2.push('Total Price: $',parseInt(totPrice));
		el('Price').innerHTML = ListItems2.join('');

		alert("Product Added!");


	});
	//updateCart();
}

(function(){

	function updateUI() {
		myLib.get({action:'cat_fetchall'}, function(json){
			// loop over the server response json
			//   the expected format (as shown in Firebug):
			for (var listItems = [],
					i = 0, cat; cat = json[i]; i++) {
				listItems.push('<li id="cat' , parseInt(cat.catid) , '">' , cat.name.escapeHTML() , '</li>');
			}
			el('categoryList').innerHTML = listItems.join('');
		});

		myLib.get({action:'prod_fetchall'}, function(json){
			// loop over the server response json
			//   the expected format (as shown in Firebug):
			for (var listItems = [],
					i = 0, prod; prod = json[i]; i++) {
				listItems.push('<li id="prod' , parseInt(prod.pid) , '">' , '<img src="incl/img/', parseInt(prod.pid), '.jpg" class="product_list"/>', prod.name.escapeHTML() ,
        'Price: $', prod.price.escapeHTML(),'<button type="button" onclick="buttonEvent','(',parseInt(prod.pid),')','">Add to Cart!</button></li>');
			}
			el('productList').innerHTML = listItems.join('');
		});

	}
	updateUI();


	function updateCart(){

		totPrice=0;

		myLib.get({action:'prod_fetchall'}, function(json){
			// loop over the server response json
			//   the expected format (as shown in Firebug):
			for (var listItems = [],
					i = 0, prod; prod = json[i]; i++) {

						if(localStorage.getItem(parseInt(prod.pid))===null){}
						else{
							listItems.push('<li id="prod' , parseInt(prod.pid) ,'">' ,prod.name.escapeHTML() ,
							'Price: $', prod.price.escapeHTML(),'Quantity:', localStorage.getItem(parseInt(prod.pid)),
							'<button type="button" onclick="editCart(',parseInt(prod.pid),',1)">+</button><button type="button" onclick="editCart(',parseInt(prod.pid),',2)">-</button><button type="button" onclick="editCart(',parseInt(prod.pid),',3)">Delete</button>');

							totPrice += localStorage.getItem(parseInt(prod.pid)) * prod.price;
						}

			}
			el('Shopping').innerHTML = listItems.join('');

			ListItems2 = [];
			ListItems2.push('Total Price: $',parseInt(totPrice));
			el('Price').innerHTML = ListItems2.join('');


		});
	}
	updateCart();



  el('categoryList').onclick = function(e) {

		//alert(e.target.parentNode.id.replace(/^cat/, ''));
		var id = e.target.id.replace(/^cat/, '');


				myLib.get({action:'cat_select',catid: id}, function(json){
				// loop over the server response json
				//   the expected format (as shown in Firebug):
				for (var listItems = [],
						i = 0, prod; prod = json[i]; i++) {
							listItems.push('<li id="prod' , parseInt(prod.pid) , '">' , '<img src="incl/img/', parseInt(prod.pid), '.jpg" class="product_list"/>', prod.name.escapeHTML() ,
							'Price: $', prod.price.escapeHTML(),'<button type="button" onclick="buttonEvent','(',parseInt(prod.pid),')','">Add to Cart!</button></li>');
					//listItems.push('<li id="prod' , parseInt(prod.pid) , '">' , '<img src="incl/img/', parseInt(prod.pid), '.jpg"/>', '<p class="center">',prod.name.escapeHTML() , ' - $', prod.price.escapeHTML(),'</p></li>');
					}
				el('productList').innerHTML = listItems.join('');
			});

			// fill in the editing form with existing values

		//handle the click on the category name
		el('productDetail').hide();
		el('categoryListPanel').show();
		el('allProducts').show();
	}

	el('productList').onclick = function(e) {

		//alert(e.target.parentNode.id.replace(/^cat/, ''));

    //alert("OK1");

		var id = e.target.parentNode.id.replace(/^prod/, '');

    //alert(id);

		myLib.get({action:'prod_select',pid: id}, function(json){
				// loop over the server response json
				//   the expected format (as shown in Firebug):
        //alert("OK2");
				for (var listItems = [],
						i = 0, prod; prod = json[i]; i++) {
        //alert("OK3");
					listItems.push('<li><img src="incl/img/', parseInt(prod.pid), '.jpg" class="product_list2"/></li>',
          '<li id="prod', parseInt(prod.pid), '">', '<p>Name:', prod.name.escapeHTML(), '</p><p>Price:$',
          prod.price.escapeHTML(), '</p><p>Description:', prod.description.escapeHTML(),'</p><button type="button" onclick="buttonEvent','(',parseInt(prod.pid),')','">Add to Cart!</button></li>');
					}

				el('productInfo').innerHTML = listItems.join('');
			});
			// fill in the editing form with existing values
			el('productDetail').show();
			el('categoryListPanel').show();
			el('allProducts').hide();
		//handle the click on the category name
	}


  el('userlogin').onclick = function(e){
		location.href="login.php";
	}
	// Put the object into storage
	//localStorage.setItem('cart_storage', JSON.stringify(testObject));



})();
</script>
</body>
</html>

<style>

ul.table{position: absolute; top:100px; left: 20%;width: 75%;height: 80%;margin: 0;padding: 0;list-style: none;overflow: auto}
ul.table li{width: 250px;height: 250px;float: left;border: 1px solid #CCC; overflow:auto}
.clear{clear: both}
img.product_list{width: 100%; height: 80%;display: block}
img.product_list2{width: 70%; height: 60%;display: block}


ul.table2{position: absolute; top:100px; left: 20%;width: 750px;height: 80%;margin: 0;padding: 0;list-style: none;overflow: auto}
ul.table2 li{width: 100%;float: left;border: 1px solid #CCC}


ul.category{width: 20%;height: 240px;float: left;overflow: auto;z-index: -1}

.hide{display:none}

#shoppcart {position: absolute; top:50px;left: 70%;width: 250px; float: right; background-color:white;z-index: 1}
#shoppcart ul{display: none}
#shoppcart:hover ul{display: block}

#userinfo {width: 350px; float: left}

.product{display: none}
.display_area{display: block}

input[type="text"] {width: 20px;}

</style>
