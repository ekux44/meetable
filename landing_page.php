<?php
$thankyou = false;

if( isset( $_POST['email'] ) && !empty($_POST['email']) )
{
	$email = strtolower($_POST['email']);
	require 'Database.php';
	Database::initialize();
	if( Database::select(
		'Emails',
		'count(*)',
		array(
			'where' => array(
				'email' => $email ),
			'single' => true ) ) == 0 )
		Database::insert(
			'Emails',
			array(
				'email' => $email,
				'ip' => $_SERVER['REMOTE_ADDR'],
				'timestamp' => time() ) );
	$thankyou = true;
}
if( isset( $_GET['emails'] ) )
{
	require 'Database.php';
	Database::initialize();
	$emailCount = Database::select(
		'Emails',
		'count(*)',
		array(
			'single' => true ) );
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Meetable: Schedule meetings with anyone using SMS and e-mail</title>
	
	<meta name="description" value="Schedule meetings and appointments with anyone using SMS and e-mail." />	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">	
	
	<meta property="og:title" content="Meetable" />
	<meta property="og:description" content="Schedule meetings and appointments with anyone using SMS and e-mail." />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="http://meetable.io" />
	<meta property="og:image" content="http://meetable.io/img/logo-square.jpg" />
	<meta property="og:site_name" content="Groupr" />	
	
	<link rel="shortcut icon" href="/img/favicon.ico" />
	
	<link href="/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
	<link href="/css/bootstrap-responsive.css" type="text/css" rel="stylesheet" />
	<link href='http://fonts.googleapis.com/css?family=Average+Sans' rel='stylesheet' type='text/css'>
	<style type="text/css">
	html { background: #ccc; }
	body {
		background: #ccc; /* Old browsers */
		background: -moz-linear-gradient(top,  #ffffff 0%, #cccccc 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#cccccc)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #ffffff 0%,#cccccc 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #ffffff 0%,#cccccc 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #ffffff 0%,#cccccc 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #ffffff 0%,#cccccc 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#cccccc',GradientType=0 ); /* IE6-9 */
		text-align: center;
		font-family: 'Average Sans';
	}
	
	.main {
		background: rgba(255,255,255,.75);
		border-radius: 4px;
		box-shadow: 0 0 2px rgba(0, 0, 0, 0.3), 0 3px 5px rgba(0, 0, 0, 0.2);
		position: relative;
		margin-bottom: 20px;
	}
	
	h1 {
		padding: 85px 0 0;
		margin: 0;
	}
	
	h2 {
		font-size: 2.0em;
		font-weight: normal;
		color: #333;
		margin: 0;
		padding: 60px 0 30px;
	}
	
	.email { margin-bottom: 0; }
	
	.notify-me { margin-top: -10px; }	
	
	.notify-me {
		height: 32px;
		background: #06c;
		padding: 0 10px;
		color: #fff;
		text-align: center;
		border: none;
		font-size: .8em;
		font-weight: normal;
	}
	
	.notify-me:hover { background: #0080E0; }
	
	.share { font-weight: bold; }
	
	#emailCount {
		text-align: left;
		font-size: 3.0em;
		margin: 20px 0;
		color: #ff0000;
	}
	
	@media (max-width: 979px) {
		html,body {
			min-height: 100%;
		}
		
		h1 { padding: 10px 0 0; }
		h2 { padding: 20px 0 10px; font-size: 1.5em; line-height: 1.5em; }
		
		.alert-success { width: 80%; margin: 0 auto 10px; }
		
		.row,.span4,.span8 {
			width: 100%;
			margin: 0;
			padding: 0;
		}
		
		.lead {
			font-size: 1.2em;
			padding: 20px;
			margin-bottom: 0;
		}
		
		.spacer {
			border-bottom: 1px solid #fff;
			border-top: 1px solid #aaa;
			width: 90%;
			margin: 0 auto 20px;
		}
		
		.share {
			margin-bottom: 10px;
			float: left;
			margin-left: 10px;
			margin-right: 10px;
		}
		
		.email {
			width: 150px;
		}
		
		.share-icons {
			float: left;
			margin-left: 10px;
			clear: left;
		}
		
		.fblike {
			float: right;
			margin-right: 10px;
			margin-bottom: 10px;
		}
		
		form { margin-bottom: 10px; }
	}
	
	@media (min-width: 980px) {
		html,body {
			height: 100%;
		}
		
		.lead {
			margin: 30px auto 0 40px;
			font-size: 1.1em;
			width: 80%;
		}
		
		.main {
			height: 300px;
			margin: 0 auto;
		}
		
		#coming-soon {
			position: absolute;
			left: -5px;
			top: -5px;
			background:url(/img/coming-soon.png);
			width: 91px;
			height: 91px;
		}
		
		.span8,.span4 { position: relative; height: 300px; }
		
		.divider {
			position: absolute;
			height: 300px;
			width: 1px;
			top: 0;
			left: 0;
			background: #ccc;
			border-right: 1px solid #fff;
		}
		
		.spacer { margin-top: 100px; }
				
		.fblike {
			position: absolute;
			right: 20px;
			bottom: 20px;
		}
		
		.share {
			position: absolute;
			left: 20px;
			bottom: 20px;
		}
		
		.share-icons {
			position: absolute;
			bottom: 10px;
			left: 125px;
		}
		
		.alert-success {
			width: 80%;
			margin: 20px auto;
		}
	}
	/*
	@media (min-width: 1200px) {
		.lead {
			font-size: 1.2em;
			margin: 40px auto 0 60px;
		}
	}*/
	</style>
	
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
	<script type="text/javascript">var switchTo5x=true;</script>
	<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
	<script type="text/javascript">stLight.options({publisher: "ur-9d8f7603-5c66-e8bd-e5a5-3267da1a474c"});</script>	
</head>
<body>
	<div class="container">
		<?php if( isset( $_GET['emails'] ) ) echo "<div id='emailCount'>$emailCount e-mails</div>"; ?>
		<h1>
			<img src="/img/logo.png" alt="Meetable | Simplify choosing meeting times" />
		</h1>
		<h2>Tired of your plans falling through? <em>Meetable fixes that.</em></h2>
		<div class="main">
			<div id="coming-soon"></div>
			<div class="row">
				<div class="span4">
					<p class="lead">
						Meetable is your personal assistant for coordinating meetings. It announce events and finds times when everyone is able to attend over SMS and e-mail. Meetable mobile integrates with your address-book to offer convenient social coordination for your daily life. 					
					</p>
				</div>
				<div class="span8">
					<div class="divider"></div>
					<?php if ($thankyou || isset($_GET['thankyou'])) { ?>
					<div class="alert alert-success"><strong>Thank you for your interest!</strong> We will e-mail you when we launch our service. Please pass this along to any friends who could use Meetable! The more feedback we receive the quicker we work. Any suggestions, feature requests, or questions can be directed to <a href="mailto:contact@meetable.io">contact@meetable.io</a></div>
					<?php } else { ?>
					<div class="spacer"></div>
					<?php } ?>
					<p>Sign up to be notified when we launch our <strong>FREE</strong> service</p>
					<form action="/" method="post">
						<input type="text" name="email" class="email" />
						<input type="submit" name="Submit" value="Notify Me" class="notify-me" />
					</form>
					<div class="share">
						Share Meetable
					</div>
					<div class="share-icons">
						<span class='st_facebook_large' displayText='Facebook' st_url="http://meetable.io"></span>
						<span class='st_linkedin_large' displayText='LinkedIn' st_url="http://meetable.io"></span>
						<span class='st_email_large' displayText='Email' st_url="http://meetable.io"></span>
						<span class='st_sharethis_large' displayText='ShareThis' st_url="http://meetable.io"></span>
					</div>
					<div class="fblike">
						<span class="st_twitter_hcount" displayText="Tweet" st_url="http://meetable.io"></span>
						<span class='st_fblike_hcount' displayText='Facebook Like' st_url="http://facebook.com/meetableapp"></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>