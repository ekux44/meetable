<!DOCTYPE html>
<html>
<head>
	<title>Meet ____</title>
	
	<link href="/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
	<link href="/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet" />
	<link href="/css/style.css" type="text/css" rel="stylesheet" />
	
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/js/header.js"></script>
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
		<div id="attendees">
			<?php foreach( (array)$data[ 'attendeeNames' ] as $k => $name ) { ?>
				<div class="row-fluid line">
					<div class="span3">
						<a href="#" class="deleteLine pull-left"><i class="icon icon-remove"></i></a>
						<input type="text" name="attendeeNames[]" class="input-medium input-name inline" value="<?php echo $data[ 'attendeeNames' ][ $k ]; ?>" placeholder="Name" />
					</div>
					<div class="span4">
						<div class="input-prepend inline">
							<span class="add-on">e-mail</span>
							<input class="input-email" type="text" name="attendeeEmails" value="<?php echo $data[ 'attendeeEmails' ][ $k ]; ?>" placeholder="E-mail Address" />
						</div>
					</div>
					<div class="span1">or</div>
					<div class="span4">
						<div class="input-prepend inline">
							<span class="add-on">phone</span>
							<input class="input-phone" type="text" name="attendeePhones" value="<?php echo $data[ 'attendeePhones' ][ $k ]; ?>" placeholder="Phone Number" />
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="row-fluid" id="new-line">
				<div class="span3">
					<a href="#" class="pull-left"><i class="icon icon-plus"></i></a>
					<input type="text" name="attendeeNames[]" class="input-medium input-name inline disabled" />
				</div>
				<div class="span4">
					<div class="input-prepend inline">
						<span class="add-on">e-mail</span>
						<input class="input-email disabled" type="text" name="attendeeEmails" />
					</div>
				</div>
				<div class="span1">or</div>
				<div class="span4">
					<div class="input-prepend inline">
						<span class="add-on">phone</span>
						<input class="input-phone disabled" type="text" name="attendeePhones" />
					</div>
				</div>
			</div>			
		</div>
		<p>
			<label>What is your name?</label>
			<input type="text" name="creatorName" class="input-name" />
			
			<label>How can we contact you?</label>
			<div class="input-prepend inline">
				<span class="add-on">e-mail</span>
				<input class="input-email" type="text" name="creatorEmail" />
			</div>
			<span class="or">or</span>
			<div class="input-prepend inline">
				<span class="add-on">phone</span>
				<input class="input-phone" type="text" name="creatorSMS" />
			</div>
		</p>
		<p>
			<label>Do you want us to find an exact time everyone agrees on or randomly pick a time in the acceptable range of times?</label>
			<div class="btn-group" data-toggle-name="narrowToOne" data-toggle="buttons-radio">
			  <button type="button" value="0" class="btn" data-toggle="button">Random</button>
			  <button type="button" value="1" class="btn" data-toggle="button">Exact</button>
			</div>
			<input type="hidden" name="narrowToOne" value="0" />
		</p>
		<div class="form-actions">
			<input type="submit" name="Submit" value="Create!" class="btn btn-success btn-large" />
		</div>
	</form>	
</div>
<div id="attendeeLineTemplate">
	<div class="row-fluid line">
		<div class="span3">
			<a href="#" class="deleteLine pull-left"><i class="icon icon-remove"></i></a>
			<input type="text" name="attendeeNames[]" class="input-medium input-name inline" placeholder="Name" />
		</div>
		<div class="span4">
			<div class="input-prepend inline">
				<span class="add-on">e-mail</span>
				<input class="input-email" type="text" name="attendeeEmails" placeholder="E-mail Address" />
			</div>
		</div>
		<div class="span1">or</div>
		<div class="span4">
			<div class="input-prepend inline">
				<span class="add-on">phone</span>
				<input class="input-phone" type="text" name="attendeePhones" placeholder="Phone Number" />
			</div>
		</div>
	</div>
</div>
</body>
</html>