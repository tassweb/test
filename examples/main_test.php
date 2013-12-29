<?php
require_once("../conf.php");      
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Main Test File</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders");

// change column titles
$dg -> set_col_title("orderNumber", "Order No.");
$dg -> set_col_title("orderDate", "Order Date");
$dg -> set_col_title("shippedDate", "Shipped Date");
$dg -> set_col_title("customerNumber",  "Customer No.");
//$dg->set_query_filter(' customerNumber=141');
// hide a column
$dg -> set_col_hidden("requiredDate");

// change default caption
$dg -> set_caption("Orders List");

// set export type
$dg -> enable_export('EXCEL');

// enable integrated search
$dg -> enable_search(true);

// set query filter
//$dg->set_query_filter('customerNumber=141');
//$dg->set_query_filter("Status='Shipped'");

$dg->set_col_width("orderNumber",'200');
$dg->set_col_width("requiredDate",'300');
$dg->set_col_width("comments",'400');

$dg -> enable_edit("INLINE", "CRUD"); 

//$dg -> set_multiselect(true);
 
// second grid as detail grid. Notice it is just another regular phpGrid with properites.
$sdg = new C_DataGrid("SELECT orderNumber,productCode,quantityOrdered,priceEach FROM orderdetails", "orderNumber", "orderdetails");
$sdg->set_col_title("orderNumber", "Order No.");
$sdg->set_col_title("productCode", "Product Code");
$sdg->set_col_title("quantityOrdered", "Quantity");

//$sdg->set_query_filter('quantityOrdered=30');

$sdg->set_col_title("priceEach", "Unit Price");
$sdg->set_col_dynalink("productCode", "http://www.example.com/", "orderLineNumber", '&foo');
$sdg->set_col_format('orderNumber','integer', array('thousandsSeparator'=>'','defaultValue'=>''));
$sdg->set_col_currency('priceEach','$');

$sdg->set_group_properties('quantityOrdered');

// define master detail relationship by passing the detail grid object as the first parameter, then the foriegn key.
//$dg->set_subgrid($sdg, 'orderNumber');
$dg->set_masterdetail($sdg, 'orderNumber');   

//$dg->enable_debug(true);

$dg -> display();

?>

</body>
</html>