<?php
	
namespace Nooper;

include_once './library/mimicry.class.php';
include_once './library/translator.class.php';
include_once './library/user.class.php';

$token='JDHUBQwxwRdmjOt4f0ejtycDD86Esj6WOhyTz-pKNIRdfFCz062PBTmQzIpQ9907Mn_saDS2jk87UEvzO7VtoNukDWRxyeBnS6CZ4D_MuvJqXTLaQYUiQ33qGSzyGgkBNAAaAFAANN';
$user=new User($token);
var_dump($user->get_users());

?>

