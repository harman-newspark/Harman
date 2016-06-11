<?php
require __DIR__ . '/PayPal-PHP-SDK/tests/bootstrap.php';
require __DIR__ . '/PayPal-PHP-SDK/vendor/autoload.php';

use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\Details;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;

//if (isset($_REQUEST['name'])) {
//
//    $name = strip_tags($_REQUEST['name']);
//    $email = $_REQUEST['email_address'];
//    $phone = $_REQUEST['phone_number'];
//    $comments = $_REQUEST['comments'];
//
//    $to = 'info@allamericanverified.com';
//
//    $subject = 'Contact Request - allamericanverified.com';
//    $headers = "From: noreply@allamericanverified.com";
//    $headers .= "MIME-Version: 1.0\r\n";
//    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
//
//    $message = '<html><body>';
//    $message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
//    $message .= "<tr style='background: #eee;'><td><strong>Name:</strong> </td><td>" . $name . "</td></tr>";
//    $message .= "<tr><td><strong>Email:</strong> </td><td>" . strip_tags($email) . "</td></tr>";
//    $message .= "<tr><td><strong>Phone Number:</strong> </td><td>" . strip_tags($phone) . "</td></tr>";
//    $message .= "<tr><td><strong>Comments: </strong> </td><td>" . strip_tags($comments) . "</td></tr>";
//    $message .= "</table>";
//    $message .= "</body></html>";
//
//    if (mail($to, $subject, $message, $headers)) {
//        header("Location:index.php?status=email-sent#join-us");
//    }
//}

$products_array = array(
    '1' => array(
        'Name' => 'Individual', 'Description' => 'Not Applicable', 'Price' => '250'
    )
    ,
    '2' => array(
        'Name' => 'Non Profit', 'Description' => 'Not Applicable', 'Price' => '1000'
    ),
    '3' => array(
        'Name' => 'Small Business', 'Description' => 'Less than $1 million', 'Price' => '1000'
    ),
    '4' => array(
        'Name' => 'Small Corporation 1', 'Description' => 'Greater than or equal to $1 million and less than $10 million', 'Price' => '5000'
    ),
    '5' => array(
        'Name' => 'Small Corporation 2', 'Description' => 'Greater than or equal to $10 million and less than $50 million', 'Price' => '10000'
    ),
    '6' => array(
        'Name' => 'Mid-size Corporation 1', 'Description' => 'Greater than or equal to $50 million and less than $100 million', 'Price' => '20000'
    ),
    '7' => array(
        'Name' => 'Mid-size Corporation 2', 'Description' => 'Greater than or equal to $100 million and less than $150 million', 'Price' => '30000'
    ),
    '8' => array(
        'Name' => 'Large Corporation 1', 'Description' => 'Greater than or equal to $150 million and less than $200 million', 'Price' => '40000'
    ),
    '9' => array(
        'Name' => 'Large Corporation 2', 'Description' => 'Greater than or equal to $200 million', 'Price' => '50000'
    ),
);
if (isset($_POST['join_now']) && $_POST['join_now'] == "join_now") {
    $messages = array();
    if (!isset($_POST['accepttos']) || isset($_POST['accepttos']) && $_POST['accepttos'] != "on")
       if (empty($_POST['ccnumber']))
        $messages[] = "You did not enter your card number";
    if (empty($_POST['cccvv']))
        $messages[] = "The cards CVV number is required ";

    if (isset($products_array[$_POST['product_id']]) && !empty($products_array[$_POST['product_id']])) {
        $product = $products_array[$_POST['product_id']];
        $product_name = $product['Name'];
        $product_desc = $product['Description'];
       // $product_price = $product['Price'];
        $card_type = $_POST['cctype'];
        $card_number = $_POST['ccnumber'];
        $expire_card_month = $_POST['ccexpirymonth'];
        $expire_card_year = $_POST['ccexpiryyear'];
        $cvv = $_POST['cccvv'];
        $apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential(
                'ASJpBCuPlN596MAmCpIZm02jxNAIbZY7IN4XA4ePd2rc0BhQ_PX42ZJ4IAbxC5gI9t1QaxDohwRunwfl', // ClientID
                'EFers2twcOFGgOvB8MbXPQ6iXralBOkrUmnb3HFZBSSoUXVi7J4grjDPPNWh83yNgzybpkDX11W7rYHh'      // ClientSecret
                )
        );
        $product_price=1;
        $card = new CreditCard();
        $card->setType($card_type)
                ->setNumber($card_number)
                ->setExpireMonth($expire_card_month)
                ->setExpireYear($expire_card_year)
                ->setCvv2($cvv);

//    $card->setType("visa")
//    ->setNumber("4032036798305379")
//    ->setExpireMonth("04")
//    ->setExpireYear("2020")
//    ->setCvv2("012");
//    ->setFirstName("kuldeep")
//    ->setLastName("thakur");

        $fi = new FundingInstrument();
        $fi->setCreditCard($card);

        $payer = new Payer();
        $payer->setPaymentMethod("credit_card")
                ->setFundingInstruments(array($fi));

        $item1 = new Item();
        $item1->setName("$product_name")
                ->setDescription("$product_desc")
                ->setCurrency('USD')
                ->setQuantity(1)
                ->setTax(0)
                ->setPrice($product_price);
//        $item2 = new Item();
//        $item2->setName('Granola bars')
//                ->setDescription('Granola Bars with Peanuts')
//                ->setCurrency('USD')
//                ->setQuantity(5)
//                ->setTax(0.2)
//                ->setPrice(2);

        $itemList = new ItemList();
        $itemList->setItems(array($item1));
//        $itemList->setItems(array($item1, $item2));

        $details = new Details();
        $details->setShipping(0)
                ->setTax(0)
                ->setSubtotal($product_price);

        $amount = new Amount();
        $amount->setCurrency("USD")
                ->setTotal($product_price)
                ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
                ->setItemList($itemList)
                ->setDescription("Payment description")
                ->setInvoiceNumber(uniqid());

        $payment = new Payment();
        $payment->setIntent("sale")
                ->setPayer($payer)
                ->setTransactions(array($transaction));
        $request = clone $payment;
        //print"<pre>";

        try {
            $payment->create($apiContext);
        } catch (Exception $ex) {
            //print_r('Create Payment Using Credit Card. If 500 Exception, try creating a new Credit Card using <a href="https://ppmts.custhelp.com/app/answers/detail/a_id/750">Step 4, on this link</a>, and using it.');
            //print_r($request);
           // print_r($ex->getdata());
            $messages[] =$ex->getdata();
        }
        /*
         *  $array = json_decode($ex->getdata(), true);
            foreach($array as $arrItems){
                foreach($arrItems as $arrItem){
                 echo "<pre>";
            print_r($arrItems);
             echo "</pre>";
         * 
         */
       // $msg[] .= 'Done Payment Using Credit Card';   [id] => PAY-0PH27404CV849902AKXBWI7Q
            
           
     // print_r($payment->getid());
           //   echo '<br>';
     //  print_r($payment->getstate());
    //    print_r($payment);
       $messages[] = $payment->getid();
       $messages[] = $payment->getstate();
              
    } else {
        $msg[] = "Membership not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">  
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- Note there is no responsive meta tag here -->   

        <title>All American Verified</title>
        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="css/custom.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link href="style.css" rel="stylesheet" type="text/css" />
        <script src="js/jquery-1.11.3.min.js"></script>


        <script>
            $(document).ready(function() {
                $(".products").hide();
            });
            function highlight_boxes(id) {
                $.ajax({
                    url: 'get_product_data.php',
                    type: "POST",
                    data: 'id=' + id,
                    dataType: 'json',
                    success: function(data) {
                        $(".product_name").html(data[0]);
                        $(".product_desc").html(data[1]);
                        $(".product_price").html(data[2]);
                    },
                    complete:(function() {
	$(".loader").fadeOut("slow");
})

                });
                $("#messages").fadeOut();
                $('#join-us').fadeIn();
                $('.rwd-table').fadeIn();
                $("#ccinputform").fadeIn();
            }
            function show_tos() {
                window.open('tc.php', '', 'width=800,height=600,scrollbars=yes,top=100,left=100')
                $("#accepttos").removeAttr("onclick");
            }
        </script>
        <style>
            .optionn {
                width: 100%;
                padding-left: 10px;
                box-shadow: 1px 1px 1px 1px;
                height: 30px;
                padding-top: 5px;
            }
        </style>
    </head>

    <body id="page-top" data-spy="scroll">

        <!-- Fixed navbar -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header page-scroll">
                    <!-- The mobile navbar-toggle button can be safely removed since you do not need it in a non-responsive implementation -->
                    <a class="navbar-brand" href="#"></a>
                </div>
                <!-- Note that the .navbar-collapse and .collapse classes have been removed from the #navbar -->
                <div id="navbar">
                    <ul class="nav navbar-nav">            
                        <li><a class="page-scroll first" href="#page-top">HOME</a></li>
                        <li><a class="page-scroll second" href="#who-we-are">WHO WE ARE</a></li>
                        <li><a class="page-scroll third" href="#what-we-do">WHAT WE DO</a></li>
                        <li><a class="page-scroll last" href="#join-us">JOIN US</a></li>
                        <li><a class="page-scroll last" href="#apply">Apply</a></li>
                    </ul>       
                </div><!--/.nav-collapse -->
            </div>
        </nav>



        <section id="intro" class="intro-section">
            <!--  <div class="container"> -->                   
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                    <div class="item active">
                        <img src="images/Layer49.png" alt="Layer49" class="img-responsive">
                        <div class="container">
                            <div class="carousel-caption">
                                <img src="images/star.png" class="img-responsive img-small" />

                                <div id="caption-main"><h1 class="big-text">ALL AMERICAN VERIFIED <span style="position:absolute;font-size:21px">&#0153;</span> </h1></div>
                                <div id="caption">
                                    <hr class="double-border"/><span>2015</span> <hr class="double-border"/>
                                </div>

                                <div id="caption"><p class="big-para">Strengthening consumer confidence and advancing ethical marketplace</p></div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <img src="images/layer30.png" alt="">
                        <div class="container">
                            <div class="carousel-caption">
                                <img src="images/star.png" class="img-responsive img-small" />

                                <div id="caption-main"><h1 class="big-text">ALL AMERICAN VERIFIED <span style="position:absolute;font-size:21px">&#0153;</span> </h1></div>
                                <div id="caption">
                                    <hr class="double-border"/><span>2015</span> <hr class="double-border"/>
                                </div>

                                <div id="caption"><p class="big-para">Strengthening consumer confidence and advancing ethical marketplace</p></div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <img src="images/layer31.png" alt="">
                        <div class="container">
                            <div class="carousel-caption">
                                <img src="images/star.png" class="img-responsive img-small" />

                                <div id="caption-main"><h1 class="big-text">ALL AMERICAN VERIFIED <span style="position:absolute;font-size:21px">&#0153;</span> </h1></div>
                                <div id="caption">
                                    <hr class="double-border"/><span>2015</span> <hr class="double-border"/>
                                </div>

                                <div id="caption"><p class="big-para">Strengthening consumer confidence and advancing ethical marketplace</p></div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <img src="images/layer32.png" alt="">
                        <div class="container">
                            <div class="carousel-caption">
                                <img src="images/star.png" class="img-responsive img-small" />

                                <div id="caption-main"><h1 class="big-text">ALL AMERICAN VERIFIED <span style="position:absolute;font-size:21px">&#0153;</span> </h1></div>
                                <div id="caption">
                                    <hr class="double-border"/><span>2015</span> <hr class="double-border"/>
                                </div>

                                <div id="caption"><p class="big-para">Strengthening consumer confidence and advancing ethical marketplace</p></div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <img src="images/layer33.png" alt="">
                        <div class="container">
                            <div class="carousel-caption">
                                <img src="images/star.png" class="img-responsive img-small" />

                                <div id="caption-main"><h1 class="big-text">ALL AMERICAN VERIFIED <span style="position:absolute;font-size:21px">&#0153;</span> </h1></div>
                                <div id="caption">
                                    <hr class="double-border"/><span>2015</span> <hr class="double-border"/>
                                </div>

                                <div id="caption"><p class="big-para">Strengthening consumer confidence and advancing ethical marketplace</p></div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <img src="images/layer311.png" alt="">
                        <div class="container">
                            <div class="carousel-caption">
                                <img src="images/star.png" class="img-responsive img-small" />

                                <div id="caption-main"><h1 class="big-text">ALL AMERICAN VERIFIED <span style="position:absolute;font-size:21px">&#0153;</span>  </h1></div>
                                <div id="caption">
                                    <hr class="double-border"/><span>2015</span> <hr class="double-border"/>
                                </div>

                                <div id="caption"><p class="big-para">Strengthening consumer confidence and advancing ethical marketplace</p></div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Controls -->
                <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>

            <!-- </div> -->
        </section>



        <!-- Who We Are Section -->
        <section id="who-we-are" class="who-we-are">
            <div class="container">
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1">

                        <div class="row">
                            <div class="col-xs-4">
                                <a href="#who-we-are" class="page-scroll" style="text-decoration:none">
                                    <img src="images/greystar.png" id="img-star1"  />
                                    <h2 class="blue">Who We Are</h2></a>

                            </div>
                            <div class="col-xs-4">
                                <a href="#what-we-do" class="page-scroll" style="text-decoration:none">
                                    <img src="images/greyshield.png" id="img-shield1" />
                                    <h2 class="blue">What We Do</h2> </a>
                            </div>
                            <div class="col-xs-4">
                                <a href="#join-us" class="page-scroll" style="text-decoration:none">
                                    <img src="images/greyhandshake.png" id="img-joinus1"  />
                                    <h2 class="blue">Join Us</h2>
                                </a>
                            </div>
   
                        </div>

                        <div class="row">

                            <div class="col-xs-12">
                                <p>&nbsp;</p>
                                <div class="well well-no-border well-who-we-are">
                                    <p class="align-justify less-padding">
                                        All American Verified (AAV) is a national network of America&rsquo;s businesses, large and small. AAV and its member companies share an unwavering commitment to American workforce. We believe that the most dedicated, hard-working, and skilled workers are found right here on American soil. Strategically headquartered in the heart of Washington, DC, we are able to remain proactive in an often unpredictable and increasingly challenging environment.
                                    </p>
                                </div>

                            </div>

                        </div>


                    </div>
                </div>
                <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p>
            </div>
        </section>

        <!-- What We Do -->

        <section id="what-we-do" class="what-we-do">
            <div class="container">
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1">

                        <div class="row">
                            <div class="col-xs-4">
                                <a href="#who-we-are" class="page-scroll" style="text-decoration:none">  <img src="images/greystar.png" id="img-star" class="greyed"  />
                                    <h2 class="blue">Who We Are</h2> </a>
                            </div>
                            <div class="col-xs-4">
                                <a href="#what-we-do" class="page-scroll" style="text-decoration:none">
                                    <img src="images/greyshield.png" id="img-shield" class="greyed" />
                                    <h2 class="blue">What We Do</h2> </a>
                            </div>
                            <div class="col-xs-4">
                                <a href="#join-us" class="page-scroll" style="text-decoration:none">
                                    <img src="images/greyhandshake.png" id="img-joinus" class="greyed"  />
                                    <h2 class="blue">Join Us</h2></a>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <p>&nbsp;</p>
                                <div class="well well-no-border well-what-we-do">
                                    <p class="align-justify less-padding">
                                        All American Verified safeguards American jobs for American people through advocacy, research, and financial aid while embracing a diverse and inclusive culture. We serve our member companies by strengthening consumer confidence and advancing an ethical marketplace. Operating with effective governance and high standards of ethical behavior, we seek to improve our economic and social impact.
                                    </p>
                                </div>

                            </div>

                        </div>


                    </div>
                </div>
                <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p>
            </div>

        </section>
        <!-- Our Memberships -->

        <!-- Join Us -->
        <section id="join-us" class="join-us">
            <form id="productForm" method="POST" action="#messages">

                <div class="container">
                    <table style="margin: 0 auto;width: 100%;" cellpadding="20">
                        <tr>
                            <td>


                                <select name="product_id" onchange="highlight_boxes(this.value);" style="width: 83%;text-align: left;box-shadow: 1px 1px 1px 1px;height: 50px; padding-left: 40px;" class="page-scroll">
                                    <option class="rectangle optionn" id="select_product">Please select your membership level based on your previous year's annual revenue </option>    
                                    <option class="rectangle optionn" id="first_product" value="1">Individual - not applicable</option>
                                    <option class="rectangle optionn" id="second_product" value="2">Non Profit - not applicable</option>
                                    <option class="rectangle optionn" id="third_product" value="3">Small Business - less than 1 mil</option>
                                    <option class="rectangle optionn" id="fourth_product" value="4">Small Corporation 1 - greater than or equal to 1 mil but less than 10 mil </option>
                                    <option class="rectangle optionn" id="fifth_product" value="5">Small Corporation 2 - Greater than or equal to $10 USD million and less than $50 USD million</option>
                                    <option class="rectangle optionn" id="sixth_product" value="6">Mid-size Corporation 1 - Greater than or equal to $50 USD million and less than $100 USD million</option>
                                    <option class="rectangle optionn" id="seventh_product" value="7">Mid-size Corporation 2 - Greater than or equal to $100 USD million and less than $150 USD million</option>
                                    <option class="rectangle optionn" id="eigth_product" value="8">Large Corporation 1 - Greater than or equal to $150 USD million and less than $200 USD million</option>
                                    <option class="rectangle optionn" id="ninth_product" value="9">Large Corporation 2 - Greater than or equal to $200 USD million</option>
                                </select>
                            </td></tr>

                    </table>

                    <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p>
                </div>

                <div class="container">
                 
                    <div class="row">
                        <table class="rwd-table" style="margin: 0 auto;width: 77%;display: none;">
                            <tr style="border-bottom: 1px solid white;background: #34495E;">
                                <th style="text-align: center;">Membership</th>
                                <th style="text-align: center;">Annual Revenue</th>
                                <th style="text-align: center;">Annual Dues</th>
                            </tr>
                            <tr style="background: #697F95;">
                           
                                <td><span class='product_name'></span><input type="hidden" class='product_name' name="product_name"></td>
                                <td><span class='product_desc'></span><input type="hidden" class='product_desc' name="product_description"></td>
                                <td><span class='product_price'></span><input type="hidden" class='product_price' name="product_price"></td>
                            </tr>

                        </table>


                        <div id="ccinputform" class="signupfields creditcard_payment" style="display: none;padding-bottom: 0px;
                             padding-top: 0px;">
                            <div class="Creditcard_header"><h2 style="font-size: 20px;
                                                               color: #fff;
                                                               margin-bottom: 0px;
                                                               margin-top: 0px;
                                                               padding-top: 16px;
                                                               text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
                                                               text-align: center;">Payment Method</h2></div>
                            <table width="100%" cellspacing="0" cellpadding="0" class="configtable textleft">
                                <tbody><tr class="newccinfo">
                                        <td class="fieldlabel" style="font-weight: bold;">Card Type</td>
                                        <td class="fieldarea">
                                            <select name="cctype" id="cctype" class="form-control">
                                                <option>visa</option>
                                                <option>masterCard</option>
                                                <option>Discover</option>
                                                <option>American Express</option>
                                                <option>JCB</option>
                                                <option>EnRoute</option>
                                                <option>Diners Club</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="newccinfo">
                                        <td class="fieldlabel" style="font-weight: bold;">Card Number</td>
                                        <td class="fieldarea">
                                            <input type="text" name="ccnumber" class="form-control"  size="30" value="" autocomplete="off">
                                        </td>
                                    </tr>
                                    <tr class="newccinfo">
                                        <td class="fieldlabel" style="font-weight: bold;">Expiry Date</td>
                                        <td class="fieldarea">
                                            <select name="ccexpirymonth" id="ccexpirymonth" class="form-control" style="width:45%;float: left;"> 
                                                <option>01</option>
                                                <option>02</option>
                                                <option>03</option>
                                                <option>04</option>
                                                <option>05</option>
                                                <option>06</option>
                                                <option>07</option>
                                                <option>08</option>
                                                <option>09</option>
                                                <option>10</option>
                                                <option>11</option>
                                                <option>12</option>
                                            </select>
                                            /
                                            <select name="ccexpiryyear" class="form-control" style="width:45%;float: right;">
                                                <option>2015</option>
                                                <option>2016</option>
                                                <option>2017</option>
                                                <option>2018</option>
                                                <option>2019</option>
                                                <option>2020</option>
                                                <option>2021</option>
                                                <option>2022</option>
                                                <option>2023</option>
                                                <option>2024</option>
                                                <option>2025</option>
                                                <option>2026</option>
                                                <option>2027</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fieldlabel" style="font-weight: bold;">CVV/CVC2 Number</td>
                                        <td class="fieldarea">
                                            <input type="text" name="cccvv" id="cccvv" class="form-control" value="" size="5" autocomplete="off">
                                            <!--                                        <a href="#" onclick="window.open('images/ccv.gif', '', 'width=280,height=200,scrollbars=no,top=100,left=100');
                                                            return false;">Where do I find this?</a>-->
                                        </td>
                                    </tr>
                                </tbody></table>
                        </div>
                        <br/><br/><br/>
                        <!--                        <div class="col-xs-10 col-xs-offset-1">
                                                    <h3 class="join-header">Please complete this form to be contacted by one of our representatives.</h3>
                        <?php
// if (isset($_REQUEST['status'])) {
//  if ($_REQUEST['status'] == "email-sent") {
                        ?>
                                                            <div role="alert" class="alert alert-success alert-dismissible fade in">
                                                                <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button>
                                                                <h4>Thank you!</h4>
                                                            </div>
                        
                        <?php
//                                }
//                            }
                        ?>
                        
                                                    <div class="form-group">
                                                        <div class="row"> <div class="col-xs-4">
                                                                <input type="text" class="form-control input-box" id="name" name="name" placeholder="Your Name" required>
                                                            </div>
                                                            <div class="col-xs-4">
                                                                <input type="email" class="form-control input-box" id="email_address" name="email_address" placeholder="Your Email Address" required>
                                                            </div>
                                                            <div class="col-xs-4">
                                                                <input type="text" class="form-control input-box" id="phone_number" name="phone_number" placeholder="Your Phone Number" required>
                                                            </div>
                                                        </div>
                        
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea class="form-control" id="comments" name="comments"></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-default btn-darkblue">Join Now!</button>
                        
                                                </div>-->
                    </div>
                    <input type="checkbox" name="accepttos" id="accepttos" onclick="show_tos();">
                    I have read and agree to the <a href="javascript:void(0);" id="containerPop">Membership Terms</a>
                    <br/><br/>
                    <button type="submit" class="btn btn-default btn-darkblue" onclick="var check_tc = $('#accepttos').val();
                if (check_tc == 'on') {
                    return true;
                } else {
                    return false;
                }" name="join_now" value="join_now">Join Now!</button>
                    <p>&nbsp;</p>
                    <!--<div class="has-border-bottom"></div>-->
                </div>
        </section>
    </form>
    <section class="error_section" id="messages" style="display: none;">
        <?php
        if (!empty($messages)) {
            ?><script> $("#messages").fadeIn();</script><?php
            ?>
            <div class="errorbox"><?php foreach ($messages as $message) {
                ?><li><?php echo $message; ?></li><?php
                }
                ?></div>
                <?php
            }
            ?>
    </section>
            <!----------Apply Form ------->
  
            <section id="apply" style="padding-top: 80px;">
                           <?php 
            $link = mysql_connect('localhost', 'devriva_american', 'admin123');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
    $database = mysql_select_db('devriva_americanverified',$link);
            if(isset($_POST['apply_form_submit']) && !empty($_POST['apply_form_submit'])){
                if(trim($_POST['business_name']) === '') {
			$nameError = 'Please enter your Business Name.';
			$hasError = true;
		} elseif (!preg_match("/^[a-zA-Z ]*$/",trim($_POST['business_name']))) {
                          $nameError = "Only letters and white space allowed";
                            } else {
                        $name = trim($_POST['business_name']);
                      
		}
                 if(trim($_POST['city']) === '') {
			$cityError = 'Please enter your city name.';
			$hasError = true;
		} elseif (!preg_match("/^[a-zA-Z ]*$/",trim($_POST['city']))) {
                          $cityError = "Only letters and white space allowed";
                            } else {
                        $city = trim($_POST['city']);
                      
		}
                if(trim($_POST['state']) === '') {
			$stateError = 'Please enter your state name.';
			$hasError = true;
		} elseif (!preg_match("/^[a-zA-Z ]*$/",trim($_POST['state']))) {
                          $stateError = "Only letters and white space allowed";
                            } else {
                        $state = trim($_POST['state']);
                      
		}
                   if(trim($_POST['zip']) === '')  {
			$zipError = 'Please enter your Postcode.';
			$hasError = true;
		} 
                else if (!eregi("^[0-9]{4}$", trim($_POST['zip']))) {
			$zipError = 'You entered an invalid zip.';
			$hasError = true;
		} else {
			$zip = trim($_POST['zip']);
		}
                if(trim($_POST['email']) === '')  {
			$emailError = 'Please enter your email address.';
			$hasError = true;
		} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
			$emailError = 'You entered an invalid email address.';
			$hasError = true;
		} else {
			$email = trim($_POST['email']);
		}
                if(trim($_POST['phone_number']) === '')  {
			$phoneError = 'Please enter your Phone Number.';
			$hasError = true;
		} 
                else if (!eregi("^[0-9]{10}$", trim($_POST['phone_number']))) {
			$phoneError = 'You entered an invalid Phone Number.';
			$hasError = true;
		} else {
			$phone_number = trim($_POST['phone_number']);
		}
                if(!isset($hasError)) {
             $query = ("INSERT INTO `tbl_applyform_data` (`business_name`, `business_contact`, `street_address`, `city`, `state`, `zip`, `email`, `phone_number`, `web`, `youtube`, `message`, `attachment`) VALUES
                       ('".$_POST['business_name']."','".$_POST['business_contact']."','".$_POST['street_address']."','".$_POST['city']."','".$_POST['state']."','".$_POST['zip']."','".$_POST['email']."','".$_POST['phone_number']."','".$_POST['web']."','".$_POST['youtube']."','".$_POST['message']."','".$_POST['attachment']."')");   
            $data = mysql_query($query); 
        //  echo  getcwd();
    // $target_path = "/upload/";

$target_dir = "upload/";
$target_file = $target_dir . basename($_FILES["attachment"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

    if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
          basename( $_FILES["attachment"]["name"]);
    } else {
        echo " ";
    }

              if(!$data)
{
  die('Could not enter data: ' . mysql_error());
} else {
     ?>    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
$(window).load(function() {
	$(".loader").fadeOut("slow");
})
</script>
                    <div class="loader"></div>
                 <div style="height: 50px; padding-top: 10px; font-size: 22px;color: green;"> <?php echo "Form Submit Successfully"; ?></div><?php  
    
header('Location:allamericanverifypayment/#apply');
                 
}
                } }
            ?> 
                             <div class="container">
               <div class="well well-no-border well-who-we-are" style="background:white!important;">
                   <center><h2 class="blue">Apply Now</h2></center>
                   <hr>
                                    <p class="align-justify less-padding">Please use the space below to submit your information and apply for 2015 All American Excellence Award&#0153;, a $250,000 grant to be presented to a small business that through innovation and ingenuity created in excess of twelve full-time manufacturing jobs in 2015.  The deadline to submit your application is November 30, 2015.  Membership in AAV is not an award prerequisite.  </p>

                                                        <link href="css/applyform.css" rel="stylesheet" media="screen">
                          <form id="my-contact-form" action="" method="post" class="form-horizontal"novalidate="true"  role="form" enctype="multipart/form-data"> 
<div><input name="my-contact-form" type="hidden" value="1"  class="form-control"></div> 
<fieldset> 

<div class="form-group"> 
<label for="business_name" class="col-sm-3 control-label simple">Business Name :</label> 
<div class="col-sm-8"> 
<input name="business_name" type="text" value="" class="form-control"> 
<?php if($nameError != '') { ?>
                                                <span class="error"><?php echo $nameError; ?></span>
                                            <?php } ?>
</div>  
</div> 
<div class="form-group"> 
<label for="business_contact" class="col-sm-3 control-label simple">Business Contact :</label> 
<div class="col-sm-8"> 
<input name="business_contact" type="text" value="" class="form-control"> 
</div> 
</div> 
<div class="form-group"> 
<label for="street_address" class="col-sm-3 control-label simple">Street Address :</label> 
<div class="col-sm-8"> 
<textarea name="street_address" cols="30" rows="2" class="form-control"></textarea> 
</div> 
</div> 
<div class="form-group"> 
<label for="city" class="col-sm-3 control-label simple">City :</label> 
<div class="col-sm-8"> 
<input name="city" type="text" value="" class="form-control"> 
<?php if($cityError != '') { ?>
                                                <span class="error"><?php echo $cityError; ?></span>
                                            <?php } ?>
</div> 
</div> 
<div class="form-group"> 
<label for="state" class="col-sm-3 control-label simple">State :</label> 
<div class="col-sm-8"> 
<input name="state" type="text" value="" class="form-control"> 
 <?php if($stateError != '') { ?>
                                                <span class="error"><?php echo $stateError; ?></span>
                                            <?php } ?>
</div>
</div>
<div class="form-group"> 
<label for="zip" class="col-sm-3 control-label simple">Zip :</label> 
<div class="col-sm-8"> 
<input name="zip" type="text" value="" class="form-control"> 
<?php if($zipError!= '') { ?>
                                                <span class="error"><?php echo $zipError; ?></span>
                                            <?php } ?>
</div> 
</div>
<div class="form-group"> 
<label for="email" class="col-sm-3 control-label simple">Email :</label> 
<div class="col-sm-8"> 
<input name="email" type="text" value="" class="form-control"> 
 <?php if($emailError != '') { ?>
                                                <span class="error"><?php echo $emailError; ?></span>
                                            <?php } ?>
</div>
</div>
<div class="form-group"> 
<label for="phone_number" class="col-sm-3 control-label simple">Phone :</label> 
<div class="col-sm-8"> 
<input name="phone_number" type="text" value="" class="form-control"> 
  <?php if($phoneError != '') { ?>
                                                <span class="error"><?php echo $phoneError; ?></span>
                                            <?php } ?>
</div>
</div>
<div class="form-group"> 
<label for="web" class="col-sm-3 control-label simple">Web :</label> 
<div class="col-sm-8"> 
<input name="web" type="text" value="" class="form-control"> 
</div> 
</div> 
<div class="form-group"> 
<label for="youtube" class="col-sm-3 control-label simple">YouTube :</label> 
<div class="col-sm-8"> 
<input name="youtube" type="text" value="" class="form-control"> 
</div> 
</div> 
<div class="form-group"> 
<label for="message" class="col-sm-3 control-label simple">Your message :</label> 
<div class="col-sm-8"> 
<textarea id="message" name="message" cols="30" rows="4" class="form-control"></textarea> 
</div>
</div>
<div class="form-group"> <label for="attachment" class="col-sm-3 control-label simple"> </label> 
<div class="col-sm-8"> 
  <input name="attachment" type="file" value="Attachment"/> </div>
</div> 

<div class="form-group"> 
<div class="col-sm-offset-3 col-sm-8"> 
<button type="submit" name="apply_form_submit" value="1" class="btn btn-default btn-darkblue">Submit</button> 
</div> 
</div> 
</fieldset> 
</form>
               </div>

                        </div>
                        <br/><br/><br/>
   
                    
                   
                 
                    <p>&nbsp;</p>
                    <!--<div class="has-border-bottom"></div>-->
                </div>
                </section>
            
   


    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-xs-10 col-xs-offset-1">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="inline-box">  <img src="images/contact-loc.png" class="img-responsive inline-box" /> 2020 PENNSYLVANIA AVE, SUITE 500, WASHINGTON, DC 20006
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="inline-box"> <img src="images/phone.png" class="img-responsive inline-box"  />  &nbsp;<strong>Phone: </strong>(202) 239-8100 </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="inline-box"> <img src="images/fax.png" class="img-responsive inline-box"  />  &nbsp;<strong>Fax: </strong>(202) 379-4044</div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-xs-2"></div>
                        <div class="col-xs-8">
                            <p>&nbsp;</p>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-xs-3">
                                        <a class="footer-btn page-scroll" href="#intro">HOME</a>
                                    </div>
                                    <div class="col-xs-3">
                                        <a class="footer-btn page-scroll" href="#who-we-are">WHO WE ARE</a>
                                    </div>
                                    <div class="col-xs-3">
                                        <a class="footer-btn page-scroll" href="#what-we-do">WHAT WE DO</a>
                                    </div>
                                    <div class="col-xs-3">
                                        <a class="footer-btn page-scroll" href="#join-us">JOIN US</a>
                                    </div>
                                </div>
                            </div>
                            <p>&nbsp;</p>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-xs-3"></div>
                                    <div class="col-xs-6">
                                        <div class="inline-box">
                                            <img src="images/fb.png" class="img-responsive inline-box"  />  &nbsp;<img src="images/twitter.png" class="img-responsive inline-box"  />  &nbsp;<img src="images/gplus.png" class="img-responsive inline-box"  />  &nbsp;
                                        </div>
                                    </div>
                                    <div class="col-xs-3"></div>
                                </div>
                            </div>
                            <p>&nbsp;</p>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-xs-2"></div>
                                    <div class="col-xs-8">
                                        <div class="inline-box">
                                            <p class="small">&copy; All American Verified 2014. All Rights Reserved. </p>
                                        </div>
                                    </div>
                                    <div class="col-xs-2"></div>
                                </div>
                            </div>

                        </div>
                        <div class="col-xs-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    
    
   <script src="http://jqueryjs.googlecode.com/files/jquery-1.2.6.min.js" type="text/javascript"></script>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/scrolling-nav.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#contact-form").validate({
                rules: {
                    name: "required",
                    email_address: {
                        required: true,
                        email: true
                    },
                    phone_number: {
                        required: true

                    }
                },
                messages: {
                    name: "Your name is required",
                    email_address: "Please enter a valid email address",
                    phone_number: "Please enter a valid phone number"
                }
            });


            $('#img-star').hover(function() {
                $(this).attr("src", "images/goldstar.png");
                $(this).animate({
                    opacity: 1
                }, 1500, 'swing');
            }, function() {
                $('#img-star').data('timer', setTimeout(function() {
                    $(this).attr("src", "images/greystar.png");
                }, 500));
            });

            $('#img-star').mouseover(function() {
                if ($(this).data('timer')) {
                    clearTimeout($(this).data('timer'));
                }
            });

            $('#img-star').mouseleave(function() {
                $(this).attr("src", "images/greystar.png");
                $(this).animate({
                    opacity: .9
                }, 1500, 'swing');
            });

            $('#img-shield').hover(function() {
                $(this).attr("src", "images/goldshield.png");
                $(this).animate({
                    opacity: 1
                }, 1500, 'swing');
            }, function() {
                $('#img-shield').data('timer', setTimeout(function() {
                    $(this).attr("src", "images/greyshield.png");
                }, 500));
            });

            $('#img-shield').mouseover(function() {
                if ($(this).data('timer')) {
                    clearTimeout($(this).data('timer'));
                }
            });

            $('#img-shield').mouseleave(function() {
                $(this).attr("src", "images/greyshield.png");
                $(this).animate({
                    opacity: .9
                }, 1500, 'swing');
            });

            $('#img-joinus').hover(function() {
                $(this).attr("src", "images/goldhandshake.png");
                $(this).animate({
                    opacity: 1
                }, 1500, 'swing');
            }, function() {
                $('#img-joinus').data('timer', setTimeout(function() {
                    $(this).attr("src", "images/greyhandshake.png");
                }, 500));
            });

            $('#img-joinus').mouseover(function() {
                if ($(this).data('timer')) {
                    clearTimeout($(this).data('timer'));
                }
            });

            $('#img-joinus').mouseleave(function() {
                $(this).attr("src", "images/greyhandshake.png");
                $(this).animate({
                    opacity: .9
                }, 1500, 'swing');
            });

            //what we do

            $('#img-star1').hover(function() {
                $(this).attr("src", "images/goldstar.png");
                $(this).animate({
                    opacity: 1
                }, 1500, 'swing');
            }, function() {
                $('#img-star1').data('timer', setTimeout(function() {
                    $(this).attr("src", "images/greystar.png");
                }, 500));
            });

            $('#img-star1').mouseover(function() {
                if ($(this).data('timer')) {
                    clearTimeout($(this).data('timer'));
                }
            });

            $('#img-star1').mouseleave(function() {
                $(this).attr("src", "images/greystar.png");
                $(this).animate({
                    opacity: .9
                }, 1500, 'swing');
            });

            $('#img-shield1').hover(function() {
                $(this).attr("src", "images/goldshield.png");
                $(this).animate({
                    opacity: 1
                }, 1500, 'swing');
            }, function() {
                $('#img-shield1').data('timer', setTimeout(function() {
                    $(this).attr("src", "images/greyshield.png");
                }, 500));
            });

            $('#img-shield1').mouseover(function() {
                if ($(this).data('timer')) {
                    clearTimeout($(this).data('timer'));
                }
            });

            $('#img-shield1').mouseleave(function() {
                $(this).attr("src", "images/greyshield.png");
                $(this).animate({
                    opacity: .9
                }, 1500, 'swing');
            });

            $('#img-joinus1').hover(function() {
                $(this).attr("src", "images/goldhandshake.png");
                $(this).animate({
                    opacity: 1
                }, 1500, 'swing');
            }, function() {
                $('#img-joinus1').data('timer', setTimeout(function() {
                    $(this).attr("src", "images/greyhandshake.png");
                }, 500));
            });

            $('#img-joinus1').mouseover(function() {
                if ($(this).data('timer')) {
                    clearTimeout($(this).data('timer'));
                }
            });

            $('#img-joinus1').mouseleave(function() {
                $(this).attr("src", "images/greyhandshake.png");
                $(this).animate({
                    opacity: .9
                }, 1500, 'swing');
            });


        });


    </script>
<script type="text/javascript">
   
    $(document).ready( function() {
   
        // When site loaded, load the Popupbox First
          $("body").click(function(){
            unloadPopupBox();
          });
          $("#containerPop").click(function(e){
            e.stopPropagation();
          });
          $("#popup_box").click(function(e){
            e.stopPropagation();
          });
        $('#popupBoxClose').click( function() {           
            unloadPopupBox();
        });
       
        $('#containerPop').click( function() {
            loadPopupBox();
        });

        function unloadPopupBox() {    // TO Unload the Popupbox
            $('#popup_box').fadeOut("slow");
            $("#containerPop").css({ // this is just for style       
                "opacity": "1" 
            });
        }   
       
        function loadPopupBox() {    // To Load the Popupbox
            $('#popup_box').fadeIn("slow");
            $("#containerPop").css({ // this is just for style
                "opacity": "0.3" 
            });        
        }       
    });
</script>    
<div id="popup_box">
    <div class="popupheading">Membership Terms <a id="popupBoxClose">close</a></div>
  <section class="join-us" style="text-align: justify!important;  background: #fff;   padding: 0px 20px;">
            <div>
                These Membership Terms and Conditions apply to the All American Verified website located at www.allamericanverified.com, and all associated sites linked to www.allamericanverified.com by All American Verified, its subsidiaries and affiliates (collectively, the "Site") and constitute an agreement between All American Verified, LLC (AAV) and New Member. The Site is the property of AAV.  BY USING THE SITE AND BY JOINING AS NEW MEMBER, YOU AGREE TO THESE TERMS; IF YOU DO NOT AGREE, DO NOT USE THE SITE OR JOIN AAV.
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1">

                        <div class="row">
                            <div>
                                
                                    <h2 class="blue">Membership Dues </h2>
AAV shall set the dues, fees, and other charges and assessments for membership in AAV (collectively, "Dues"). 
New Member hereby agrees to pay the applicable Dues. The Dues initially owed by New Member are payable upon admission to membership in AAV. 
New Member's membership in AAV shall become effective upon AAV's receipt of New Member's initial dues payment ("Effective Date"). 
The first day of the month following the Effective Date shall be hereinafter referred to as the "Anniversary Date."
Successive annual Dues shall become due and payable by New Member upon each one (1) year anniversary of the Anniversary Date. Dues shall not be considered as being in arrears until thirty (30) days after the applicable due date. If New Member resigns from AAV prior to the end of any one (1) year membership period, New Member shall nonetheless be responsible for payment of Dues for such entire one (1) year membership period, and shall not be entitled to a refund of any Dues previously paid by New Member. New Member's membership in AAV shall be suspended or terminated if New Member does not pay all required Dues, in full, by the end of the grace period. 
                            </div>
                            <div>
                                                                    <h2 class="blue">Confidentiality</h2>
                                                                    In connection with New Member's membership in AAV, New Member may receive or have access to certain non-public information (including certain information of other Members), (i) which is designated by AAV as confidential, or (ii) which New Member should reasonably know, by its nature or the manner of its disclosure, to be confidential (collectively, "Confidential Information"). New Member agrees (a) to keep such Confidential Information in the strictest confidence, using the same degree of care that it exercises with respect to its own confidential information of like importance, but in no event less than reasonable care, (b) not to disclose the Confidential Information to any person or entity other than its representatives (1) who have a need to know such information, and (2) who are subject to nondisclosure obligations at least as protective of such Confidential Information as the provisions set forth herein, and (c) not to use the Confidential Information in any manner or medium whatsoever (whether now known or hereafter devised), except as required under this agreement. 
                            </div>
                            <div>
                                
                                    <h2 class="blue">Indemnification </h2>
                               New Member agrees to fully defend, indemnify, save and hold AAV and its other Members, and the officers, directors, employees agents and representatives of AAV and its other Members (collectively, "AAV Indemnitees"), harmless from any and all liabilities, claims, demands, causes of action, suits, damages, losses and expenses (including attorneys' fees, expert fees and court costs) incurred or suffered by such AAV Indemnitees arising out of, or in connection with, any third party claim, demand, or cause of action to the extent such claim, demand or cause of action is based upon or arises out of (i) any allegation that any contribution provided by New Member under this agreement infringes or otherwise violates any local or federal law or regulation, including intellectual property laws or other proprietary rights; (ii) New Member's breach of this agreement; and/or (iii) New Member's gross negligence or willful misconduct. 
                            </div>
   
                                <div>
                                
                                    <h2 class="blue">Trademark License  </h2>
                                    With the exception of individual members the following conditions apply to all New Members:  AAV hereby grants New Member a limited, worldwide, non-exclusive, royalty-free license to (i) use, reproduce, electronically distribute, transmit, broadcast and/or publicly display AAV's name and logo (collectively, the "Marks"), solely to identify New Member as a Member of AAV, in informational briefings and/or in other marketing, advertising or promotional materials of New Member, and (ii) otherwise use the Marks in any manner as New Member may request from time to time, solely with AAV's prior written consent in each instance, which consent shall not be unreasonably withheld, conditioned or delayed. The license granted in this section shall remain in effect until the effective date of termination of this agreement.  

AAV logos are the sole and exclusive property of AAV. These logos may be used only by AAV members in good standing if and only if such use is made pursuant to the terms and conditions of this limited and revocable license. Any failure by a user to comply with the terms and conditions contained herein may result in the immediate revocation of this license, in addition to any other sanctions imposed by AAV. The interpretation and enforcement (or lack thereof) of these terms and conditions, and compliance therewith, shall be made by AAV in its sole discretion. ??

The logos are made available to AAV members in good standing in camera-ready, printed form in color and/or black and white.  The logos may not be revised or altered in any way, and must be displayed in the same form as produced by AAV.  The logos must be printed in their official color or in black and white. ??

The logos may be used in a professional manner on the user's business cards, stationery, literature, advertisements, storefront window, Web site, or in any other comparable manner to signify the user's membership in AAV. Notwithstanding the foregoing, the logos may not be used in any manner that, in the sole discretion of AAV: discredits AAV or tarnishes its reputation and goodwill; is false or misleading; violates the rights of others; violates any law, regulation or other public policy; or mischaracterizes the relationship between AAV and the user, including but not limited to any use of the logos that might be reasonably construed as an endorsement, approval, sponsorship, or certification by AAV of the user, the user's business or organization, or the user's products or services, or that might be reasonably construed as support or encouragement to purchase or utilize the user's products or services. ??

Use of the logos shall create no rights for users in or to the logos or their use beyond the terms and conditions of this limited and revocable license. The logos shall remain at all times the sole and exclusive intellectual property of AAV. AAV shall have the right, from time to time, to request samples of use of the logos from which it may determine compliance with these terms and conditions. Without further notice, AAV reserves the right to prohibit use of the logos if it determines, in its sole discretion, that a user's logo usage, whether willful or negligent, is not in strict accordance with the terms and conditions of this license, otherwise could discredit AAV or tarnish its reputation and goodwill, or the user is not an AAV member in good standing. 

                                </div>
         <div>
                                
                                    <h2 class="blue">Relationship </h2>
         
New Member hereby acknowledges and agrees that it participates in AAV voluntarily, solely to advance the purposes of AAV. New Member acknowledges and agrees that each of the Members remains free, in its independent judgment, to adopt, reject, or modify any and all guidance developed by AAV. New Member shall not have the authority, actual or implied, to bind any other Member or AAV in any way, to make any commitments or representations on behalf of another Member or AAV, or to act as agent of another Member or AAV. AAV shall not have the authority, actual or implied, to bind New Member in any way or to act as agent for New Member. Except where prohibited by law, in no event will AAV be liable to New Member for any indirect, consequential, exemplary, incidental or punitive damages, including lost profits, even if AAV has been advised of the possibility of such damages.
                          
         </div>
                       <div>
                                
                                    <h2 class="blue">Term </h2>
         

This agreement shall be effective upon the Effective Date, and shall remain in full force and effect until the effective date of New Member's resignation or removal from membership in AAV in accordance with sections set forth below. 
                     
         </div>      
                             <div>
                                
                                    <h2 class="blue">Termination  </h2>
         
In addition to New Member's right to terminate this agreement, New Member may resign as a Member of AAV at any time upon written notice to AAV. Further, New Member may be removed from membership in AAV if New Member is in violation of this agreement, as determined by AAV in its sole discretion ("Removal for Cause"). Before Removal for Cause, AAV will provide written notice (via letter or email) to New Member of any such violation. If New Member fails to remedy such violation within ten (10) days of receipt of such written notice, New Member will be removed from membership effective as of the eleventh day following receipt by New Member of such written notice. This agreement shall terminate immediately upon the effective date of New Member's resignation or removal from membership in AAV. Notwithstanding the foregoing, the provisions of other sections shall survive the effective date of termination of this agreement, and New Member shall not be entitled to receive any refund for any prepaid Dues.            
         </div>   
                             <div>
                                
                                    <h2 class="blue">Additional Termination Provisions for Nonprofit Members   </h2>
         AAV does not believe that its current or planned activities include any form of activity prohibited for Section 501(c)(3) organizations. If, at any time, the nature of the activities conducted by AAV include activities prohibited for Section 501(c)(3) organizations, notwithstanding any provision of this agreement, any Member that qualifies as a Section 501(c)(3) organization may terminate its membership and such resigning Member may request a pro rata refund of any prepaid Dues. Any resignation pursuant to this Section shall be made in writing and shall include specific identification of those activities that the resigning Member believes to be prohibited for Section 501(c)(3) organizations. AAV shall first be given the opportunity to adjust its activities such that they no longer constitute activities prohibited for Section 501(c)(3) organizations and reject the Member's resignation pursuant to this Section.  Otherwise, AAV shall accept the Member's resignation pursuant to this Section and issue a pro rata refund of the Member's prepaid Dues. 
    </div>  
                               <div>
                                
                                    <h2 class="blue">Privacy </h2>
                                    AAV never shares any information about its members, other than to identify members as such to members of the media, the press, or social networking or similar sites.  By using the Site, you acknowledge and agree that Internet transmissions are never completely private or secure. You understand that any message or information you send to the Site may be read or intercepted by others, even if there is a special notice that a particular transmission (for example, credit card information) is encrypted.
          </div>  
                             <div>
                                
                                    <h2 class="blue">Consent to Email and Fax Correspondence  </h2>
                             New Member hereby consents to receive correspondence from AAV by email and fax in addition to regular or snail mail.       
                             </div>  
                            <div>
                                
                                    <h2 class="blue">Applicable Law  </h2>
                            This agreement shall be deemed a contract made under the laws of Delaware and together with the rights and obligations of the parties hereunder, shall be construed under and governed by the laws of such state, without giving effect to its conflict of laws principles. Service of process shall be made in any manner allowed by applicable law.  
                            </div> 
                               <div>
                                
                                    <h2 class="blue">Entire Agreement </h2>
                           This agreement, together with the policies, and all exhibits, attachments, documents and instruments referred to herein or therein, constitute the entire understanding between the parties hereto with respect to the subject matter hereof, and supersedes all other prior representations, agreements, discussions and understandings, both written and oral, between such parties with respect to such subject matter. 


                               </div> 
                        </div>

                        


                    </div>
                </div>
                <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p>
            </div>
        </section>
</div>
</body>
</html>
