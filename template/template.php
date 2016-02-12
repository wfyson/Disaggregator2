<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html 	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd"
	xml:lang="en">

<?php

	$conf = iotaConf::getInstance();
	$jsdir = "sites/Disaggregator2/js";

?>

<head>
	<title>The Disaggregator Project</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="{{seo:Description}}" />
	<meta name="keywords" content="{{seo:Keywords}}" />
	<meta name="robots" content="index, follow" />
	
	<!-- CSS -->	
 	<link rel="stylesheet" type="text/css" href="/sites/Disaggregator2/template/css/bootstrap.min.css" />	 
	<link rel="stylesheet" type="text/css" href="/sites/Disaggregator2/template/css/style.css" />
        <link rel="stylesheet" type="text/css" href="/sites/Disaggregator2/template/css/hexdots.css" />

	<!-- JS -->
	<script type="text/javascript" src="/lib/tauAjax/tauAjax.lib.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>	
        <script type="text/javascript" src="/sites/Disaggregator2/js/bootstrap.min.js"></script>
</head>
<body>
<?php 

            try 
            {
                $user = tauFile::getCurrentFile()->getDecoration('user');
            }
            catch(iotaException $e)
            {
                $user = null;
            }
            
            if($user instanceof DisaggregatorPerson)
                $loggedin = true;
            else
                $loggedin = false;
?>

<div id="page">
	<nav class="navbar navbar-inverse" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-9">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="/">Disaggregator</a>
                    </div>
                    <ul class="nav navbar-nav navbar-right">
                                                   
			<?php if($loggedin){ ?>
			<li>
                            <a href="/portfolio"><span class="glyphicon glyphicon-book"></span> Portfolio</a>
                        </li>                        
                        <li>
                            <a href="/profile"><span class="glyphicon glyphicon-user"></span> Profile</a>
                        </li>
                        <li>
                            <a href="/groups"><span class="glyphicon glyphicon-eye-close"></span> Groups</a>
                        </li>
                        <li>
                            <a href="/components"><span class="glyphicon glyphicon-cog"></span> Components</a>
                        </li>
                        <li>
			    <a id="logout" href="/logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a>
			</li>
			<?php } ?>
		    </ul>

			
                </div>  
            </nav>

	<div id="content" class="container">
	[[CONTENT]]
		<div id="footer">
		
		</div>
	</div>
</div>
</body>
</html>
