	</div>

 


</div>



    <script>
        $(function() {
 
            $("#Submit1").click(function() {
                
 
                if(theName.length > 0)
                {
                    $.ajax({
                      type: "POST",
                      url: "http://10.0.2.2/mobileajax/callajax.php",
                      data: ({ name: theName }),
                      cache: false,
                      dataType: "text",
                      success: onSuccess
                    });
                }
            });
 
            $("#resultLog").ajaxError(function(event, request, settings, exception) {
              $("#resultLog").html("Error Calling: " + settings.url + "<br />HTPP Code: " + request.status);
            });
 
            function onSuccess(data)
            {
                $("#resultLog").html("Result: " + data);
            }
 
        });
    </script>   

</body>
</html>