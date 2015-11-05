<?php
/**
 * @Author Evin Weissenberg
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!--    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">-->

    <title>Yessay</title>

    <!-- Bootstrap core CSS -->
    <link href="../../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../../assets/css/main.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../../../assets/css/sticky-footer-navbar.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]>
    <script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
<div style="background-color: #6ed7be;">
   <table width="864" align="center">
       <tr>
           <td>
               <img src="/assets/images/logoo.png" alt=""/> 
           </td>
           <td align="right" class="navigation_options">

               <a href="/upgrade">Upgrade Now</a> | <a href="/how_it_works">How It Works</a> | <?php if(isset($_SESSION['data']->ID)){ ?> Hi,
                   <a href="/login"> <?php echo $_SESSION['data']->FirstName; ?>
               </a>,
                   <a href="/logout">Logout</a> <?php } else
               {?> <a href="/login">Login</a>
               <?php
               }?>
           </td>
       </tr>
   </table>
</div>
