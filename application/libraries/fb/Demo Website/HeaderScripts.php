<script type="text/javascript" src="javascript/jquery.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
      var url = window.location.href;
      var p1 = url.lastIndexOf("/") + 1;
      var p2 = url.length;
      url = url.substring(p1, p2);
      $("table#menulinkstable [href$='" + url + "']").removeClass("menulink");
      $("table#menulinkstable [href$='" + url + "']").addClass("menulink_active");


      var i=0;
      for (i=0;i<=15;i++)
      {          
        $("img#animate_arrow").fadeTo(850,0.0);
        $("img#animate_arrow").fadeTo(850,1.00);
      }
    });
</script>