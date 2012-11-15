<!DOCTYPE html>
<html>
<head>
	<title>Meetable</title>
	
	<meta name="description" value="Schedule meetings and appointments with anyone using SMS and e-mail." />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<link rel="shortcut icon" href="/img/favicon.ico" />
	
	<link href="/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
	<link href="/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
	<link href="/css/style.css" type="text/css" rel="stylesheet" />
	<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Kite+One' rel='stylesheet' type='text/css'>
	
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/js/globalize.js"></script>
	<script type="text/javascript" src="/js/jquery.mousewheel.js"></script>
	<script type="text/javascript" src="/js/header.js"></script>
	<script type="text/javascript">
	
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-36250643-1']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>	
</head>
<body>
<div class="container">
	<h1><a href="/">Meetable</a></h1>
	
	<?php
		if( $success )
			echo '<div class="alert alert-success"><strong>Woo hoo!</strong> Your meeting has been created and each attendee will be contacted shortly to request a time. We will keep you updated.</div>';
		else if( $error )
			echo '<div class="alert alert-error">' . $error . '</div>';
	?>
	<form action="/new" method="post">
		<p class="lead">
			Meetable is your personal assistant for coordinating meetings. It announce events and finds times when everyone is able to attend. Meetable mobile integrates with your address-book to offer convenient social coordination for your daily life.
		</p>
		<p>
			<h3>What is this meeting about?</h3>
			<input type="text" name="name" value="<?php echo $data['name'];?>" />
		</p>
		<p>
			<h3>For how long?</h3>
			<div class="input-append">
				<input class="span1" type="text" name="length" value="<?php echo $data['length'];?>" />
				<span class="add-on">minutes</span>
			</div>
		</p>
		<p>
			<h3>When?</h3>
		</p>
		<label class="inline or">Start</label>
		<input type="text" name="start-date" value="<?php echo $data['timeRange']['start-date'];?>" class="date-picker" />
		<input type="text" name="start-time" value="<?php echo $data['timeRange']['start-time'];?>" class="time-picker" />
		<label class="inline or">End</label>
		<input type="text" name="end-date" value="<?php echo $data['timeRange']['end-date'];?>" class="date-picker" />
		<input type="text" name="end-time" value="<?php echo $data['timeRange']['end-time'];?>" class="time-picker" />
		<p>
			<h3>Who?</h3>
		</p>
		<div id="attendees">
			<?php foreach( (array)$data[ 'attendeeNames' ] as $k => $name ) { ?>
				<div class="line clearfix not-new">
					<div class="line-name">
						<a href="#" class="deleteLine pull-left"><i class="icon icon-remove"></i></a>
						<input class="input-medium input-name inline" type="text" name="attendeeNames[]" value="<?php echo $data[ 'attendeeNames' ][ $k ]; ?>" />
					</div>
					<div class="line-email">
						<div class="input-prepend inline">
							<span class="add-on">e-mail</span>
							<input class="input-email" type="text" name="attendeeEmails[]" value="<?php echo $data[ 'attendeeEmails' ][ $k ]; ?>" />
						</div>
					</div>
					<div class="line-or">or</div>
					<div class="line-phone">
						<div class="input-prepend inline">
							<span class="add-on">phone</span>
							<input class="input-phone" type="text" name="attendeePhones[]" value="<?php echo $data[ 'attendeePhones' ][ $k ]; ?>" />
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
			<h3>Who is this from?</h3>
			<p>	
				<label class="inline">Name</label>
				<input type="text" name="creatorName" class="input-name" value="<?php echo $data['creatorName'];?>" />
			</p>
			
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
			<label class="inline">Do you want to have the last say in the meeting times if it comes down to a tie? If you leave this unchecked, we will break ties for you.</label>
			<input type="checkbox" value="<?php echo $data['narrowToOne']; ?>" class="inline" name="narrowToOne" />
		</p>
		<div class="form-actions">
			<input type="submit" name="Submit" value="Create!" class="btn btn-success btn-large btn-getting-started" />
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