from shopping.content import _constants
def create_product_sample(config, offer_id, **overwrites):
	product = {"offerId":"63","title":"Fuente de Poder ASUS ROG","description":"<p class=\"detailsInfo_right_title\">Strix 750W 80 PLUS Gold, 20+4 pin ATX, 150mm, 750W<\/p>","link":"https:\/\/vmtec.com.mx\/tienda\/index.php\/producto\/fuente-de-poder-asus-rog","imageLink":"https:\/\/vmtec.com.mx\/tienda\/wp-content\/uploads\/2021\/09\/CP-ASUS-90YE00A0-B0NA00-1.jpg","contentLanguage":"es","targetCountry":"MX","channel":"online","availability":"in stock","condition":"new","googleProductCategory":"autos","gtin":"90YE00A0-B0NA00","price":{"value":"2609","currency":"MXN"},"shipping":{"country":"MX","service":"Standard shipping","price":{"value":"250.00","currency":"MXN"}},"shippingWeight":{"value":"20","unit":"grams"}}
	product.update(overwrites)
	return product
