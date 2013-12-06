<?php ob_start(); ?>
<h1>Dumping $_SERVER</h1>
<pre>
<?php print_r($_SERVER); ?>
</pre>
<hr/>
	<h1>Dumping $HTTP_SERVER_VARS</h1>
	<pre>
<?php print_r($HTTP_SERVER_VARS); ?>
</pre>
	<hr/>

<h1>Dumping $_ENV</h1>
<pre>
<?php print_r($_ENV); ?>
</pre>
<hr/>
<h1>Dumping $_REQUEST</h1>
<pre>
	<?php print_r($_REQUEST); ?>
</pre>
	<hr/>
<h1>Dumping $_COOKIE</h1>
<pre>
	<?php print_r($_COOKIE); ?>
</pre>
	<hr/>
<h1>Dumping $_SESSION</h1>
<pre>
	<?php print_r($_SESSION); ?>
</pre>
<hr/>
<h1>Dumping Functions</h1>
<pre>
	<?php

	$dump = get_defined_functions();
	asort($dump['user']);
	asort($dump['internal']);
	print_r($dump);

	?>
</pre>
<hr/>



<h1>Dumping Classes</h1>
<pre>
	<?php

	$dump = get_declared_classes();
	asort($dump);
	print_r($dump);
	?>
</pre>
<hr/>

<h1>Dumping Interfaces</h1>
<pre>
	<?php

	$dump = get_declared_interfaces();
	asort($dump);
	print_r($dump);
	?>
</pre>
<hr/>

<h1>Dumping Traits</h1>
<pre>
	<?php

	$dump = get_declared_traits();
	asort($dump);
	print_r($dump);
	?>
</pre>
<hr/>



<h1>Dumping Headers</h1>
<pre>
	<?php

	$dump = headers_list();
	asort($dump);
	print_r($dump);

	?>
</pre>
<hr/>
<?php ob_end_flush();  ?>