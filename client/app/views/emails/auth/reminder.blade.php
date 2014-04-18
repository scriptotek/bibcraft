<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Velkommen til Bibcraft</h2>

		<div>
			For å lage ditt eget passord, gå til {{ URL::route('LibrariansControll@getActivate', array($token)) }}.
		</div>
	</body>
</html>