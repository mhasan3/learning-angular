<!DOCTYPE html>
<html ng-app="countryApp">
<head>
	<title>example 18</title>
</head>


<body>

<?php 
	$obj = (object) array(
		
		array(		
		'name' => 'China',
		'population' => 154654646,
		),

		array(
			'name' => 'uk',
			'population' => 87644546,
			),

		array(

			'name' => 'usa',
			'population' => 46544878,

			)


		);
	
	 
 ?>

	<ul>
		<li>
			<?php foreach ($obj as $key => $value) {
					foreach ($value as $key1 => $value1) {
						if ($value1 == 'China') {
							echo $value1;
						}
					}
			} ?>
		</li>
	</ul>

</body>

</html>