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
	<form action="/new" method="post">
		<p>
			<label>What is the name of this meeting?</label>
			<input type="text" name="name" value="<?php echo $data['name'];?>" />
		</p>
		<p>
			<label>How long will the meeting last?</label>
			<div class="input-append">
				<input class="span1" type="text" name="length" value="<?php echo $data['length'];?>" />
				<span class="add-on">minutes</span>
			</div>
		</p>
		<p>
			<label>What is the time frame you want to schedule this meeting in?</label>
			<label>Start</label>
			<input type="text" name="start" value="<?php echo $data['timeRange']['start'];?>" />
			<label>End</label>
			<input type="text" name="end" value="<?php echo $data['timeRange']['end'];?>" />
		</p>
		<p>
			<label>Who is invited?</label>
		</p>
		<div id="attendees">
			<?php foreach( (array)$data[ 'attendeeNames' ] as $k => $name ) { ?>
				<div class="line clearfix not-new">
					<div class="line-name">
						<a href="#" class="deleteLine pull-left"><i class="icon icon-remove"></i></a>
						<input class="input-medium input-name inline" type="text" name="attendeeNames[]" value="<?php echo $data[ 'attendeeNames' ][ $k ]; ?>" placeholder="Name" />
					</div>
					<div class="line-email">
						<div class="input-prepend inline">
							<span class="add-on">e-mail</span>
							<input class="input-email" type="text" name="attendeeEmails[]" value="<?php echo $data[ 'attendeeEmails' ][ $k ]; ?>" placeholder="E-mail Address" />
						</div>
					</div>
					<div class="line-or">or</div>
					<div class="line-phone">
						<div class="input-prepend inline">
							<span class="add-on">phone</span>
							<input class="input-phone" type="text" name="attendeePhones[]" value="<?php echo $data[ 'attendeePhones' ][ $k ]; ?>" placeholder="Phone Number" />
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="line clearfix" id="new-line">
				<div class="line-name">
					<a href="#" class="pull-left"><i class="icon icon-plus"></i></a>
					<input class="input-medium input-name inline disabled" type="text" />
				</div>
				<div class="line-email">
					<div class="input-prepend inline">
						<span class="add-on">e-mail</span>
						<input class="input-email disabled" type="text" />
					</div>
				</div>
				<div class="line-or">or</div>
				<div class="line-phone">
					<div class="input-prepend inline">
						<span class="add-on">phone</span>
						<input class="input-phone disabled" type="text" />
					</div>
				</div>
			</div>			
		</div>
		<p>
			<label>What is your name?</label>
			<input type="text" name="creatorName" class="input-name" value="<?php echo $data['creatorName'];?>" />
			
			<label>How can we contact you?</label>
			<div class="input-prepend inline">
				<span class="add-on">e-mail</span>
				<input class="input-email" type="text" name="creatorEmail" value="<?php echo $data['creatorEmail'];?>" />
			</div>
			<span class="or">or</span>
			<div class="input-prepend inline">
				<span class="add-on">phone</span>
				<input class="input-phone" type="text" name="creatorPhone" value="<?php echo $data['creatorPhone'];?>" />
			</div>
		</p>
		<p>
			<label>Do you want us to find an exact time everyone agrees on or randomly pick a time in the acceptable range of times?</label>
			<div class="btn-group" data-toggle-name="narrowToOne" data-toggle="buttons-radio">
			  <button type="button" value="0" class="btn" data-toggle="button">Random</button>
			  <button type="button" value="1" class="btn" data-toggle="button">Exact</button>
			</div>
			<input type="hidden" name="narrowToOne" value="<?php echo $data['narrowToOne'];?>" />
		</p>
		<div class="form-actions">
			<input type="submit" name="Submit" value="Create!" class="btn btn-success btn-large" />
		</div>
	</form>	
</div>
<div id="attendeeLineTemplate">
	<div class="clearfix line not-new">
		<div class="line-name">
			<a href="#" class="deleteLine pull-left"><i class="icon icon-remove"></i></a>
			<input class="input-medium input-name inline" type="text" name="attendeeNames[]" placeholder="Name" />
		</div>
		<div class="line-email">
			<div class="input-prepend inline">
				<span class="add-on">e-mail</span>
				<input class="input-email" type="text" name="attendeeEmails[]" placeholder="E-mail Address" />
			</div>
		</div>
		<div class="line-or">or</div>
		<div class="line-phone">
			<div class="input-prepend inline">
				<span class="add-on">phone</span>
				<input class="input-phone" type="text" name="attendeePhones[]" placeholder="Phone Number" />
			</div>
		</div>
	</div>
</div>
</body>
</html>