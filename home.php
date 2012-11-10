<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link href="/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
	<link href="/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet" />
	<link href="/css/style.css" type="text/css" rel="stylesheet" />
	
	<script type="text/javascript" src="/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
</head>
<body>
<div class="container">
	<h1>Meet ____</h1>
	<form action="/" method="post">
		<p>
			<label>What is the name of this meeting?</label>
			<input type="text" name="name" />
		</p>
		<p>
			<label>How long will the meeting last?</label>
			<div class="input-append">
				<input class="span1" type="text" name="length" />
				<span class="add-on">minutes</span>
			</div>
		</p>
		<p>
			<label>Who is invited?</label>
			
		</p>
		<p>
			<label>What is your name?</label>
			<input type="text" name="creatorName" />
			
			<label>How can we contact you?</label>
			<div class="input-prepend inline">
				<span class="add-on">e-mail</span>
				<input class="span2" type="text" name="creatorEmail" />
			</div>
			<span class="or">or</span>
			<div class="input-prepend inline">
				<span class="add-on">phone</span>
				<input class="span2" type="text" name="creatorSMS" />
			</div>
		</p>
		<input type="submit" name="Submit" value="Create!" class="btn btn-success btn-large" />
	</form>
</div>
</body>
</html>