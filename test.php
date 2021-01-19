<?php
//$ip = "172.16.5.101";
$page = "index.cgi";
$ip = $_GET["ip"]
?>
<body id="frame">
<form id="login" target="frame" method="post" action="http://<?php echo $ip ?>/login.cgi?uri=/<?php echo $page?>">
<input type="hidden" name="username" value="admin"/>
<input type="hidden" name="password" value="client.acceso-2005-"/>
</form>

<iframe  name="frame" width="100%" height="100%"></iframe>

<script type="text/javascript">
// submit the form into iframe for login into remote site
document.getElementById('login').submit();

// once you're logged in, change the source url (if needed)
var iframe = document.getElementById('frame');
iframe.onload = function() {
if (iframe.src != "http://<?php echo $ip . "/" . $page ?>") {
iframe.src="http://<?php echo $ip . "/" . $page ?>";
}
}
window.setTimeout(function(){

        // Move to a new location or you can do something else
        window.location.href = "http://<?php echo $ip . "/" . $page ?>";

    }, 5000);
</script>

</body>
