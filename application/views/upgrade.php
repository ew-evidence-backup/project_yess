<!-- Fixed navbar -->
<?php include 'includes/header.php'; ?>

<div style="background-color: #fff;">
    <table align="center">
        <tr>
            <td>
               <div>
<br>
                   <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                       <input type="hidden" name="cmd" value="_xclick-subscriptions">
                       <input type="hidden" name="item_number" value="<?php echo mktime(); ?>">
                       <input type="hidden" name="business" value="JABRAMS@YESSAY.COM">
                       <input type="hidden" name="currency_code" value="USD">
                       <input type="hidden" name="no_shipping" value="1">
                       <input type="image" src="http://www.lavasoft.com/img/misc_page/button_upgrade_now_lb.png" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
                       <input type="hidden" name="a3" value="35.00">
                       <input type="hidden" name="p3" value="1">
                       <input type="hidden" name="t3" value="M">
                       <input type="hidden" name="src" value="1">
                       <input type="hidden" name="sra" value="1">
                        <!--populate these fields with session data-->
                       <input type="hidden" name="cn" value="<?php echo isset($_SESSION['userid']); ?>">
                       <INPUT TYPE="hidden" NAME="first_name" VALUE="John">
                       <INPUT TYPE="hidden" NAME="last_name" VALUE="Doe">
                       <INPUT TYPE="hidden" NAME="address1" VALUE="9 Elm Street">
                       <INPUT TYPE="hidden" NAME="address2" VALUE="Apt 5">
                       <INPUT TYPE="hidden" NAME="city" VALUE="Berwyn">
                       <INPUT TYPE="hidden" NAME="state" VALUE="PA">
                       <INPUT TYPE="hidden" NAME="zip" VALUE="19312">
                       <INPUT TYPE="hidden" NAME="lc" VALUE="US">
                       <INPUT TYPE="hidden" NAME="email" VALUE="buyer@domain.com">
                       <INPUT TYPE="hidden" NAME="night_phone_a" VALUE="610">
                       <INPUT TYPE="hidden" NAME="night_phone_b" VALUE="555">
                       <INPUT TYPE="hidden" NAME="night_phone_c" VALUE="1234">

                       <input type="hidden" name="return" value="http://80.202.213.240/apps/tickets/buy/success/" />
                       <input type="hidden" name="cancel_return" value="http://80.202.213.240/apps/tickets/buy/cancelled/" />
                       <input type="hidden" name="notify_url" value="http://80.202.213.240/apps/tickets/buy/ipn/" />
                   </form>

               </div>
                <br>
            </td>

        </tr>

    </table>
</div>


<?php include 'includes/footer.php'; ?>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="../../../assets/js/bootstrap.min.js"></script>
</body>
</html>
