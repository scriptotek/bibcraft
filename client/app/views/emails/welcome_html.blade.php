<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Velkommen til Bibcraft</h2>

		<p>
			For å aktivere kontoen og opprette ditt eget passord, 
			besøk {{ URL::action('LibrariansController@getActivate', array($token)) }}
		</p>

		<p>
			Hilsen Orc Master
		</p>
	</body>
</html>