<!-- Icons -->
<script src="https://kit.fontawesome.com/9c2d6b042e.js"></script>

<!--Start Default CSS for each page-->
<style type="text/css">
body {
    position: relative;
}

#comments {
    display: none;
}
</style>
<!--End Default CSS for each page-->
<?php
//https://stackoverflow.com/questions/37723448/how-to-make-responsive-table-without-using-bootstrap
function mediaQueryforTable()
{?>
<style>
@media only screen and (max-width: 500px) {
    table tr td {
        text-align: center;
    }

    table,
    tbody,
    th,
    td,
    tr {
        display: block;
    }

    thead tr {
        display: none;
    }

    tr {
        border: 1px solid #ccc;
    }

    td {
        border: none;
        position: relative;
        white-space: normal;
        min-height: 40px;
        overflow: hidden;
        word-break: break-all;
    }

    td:before {
        /** For enabling the data-title part */
        position: absolute;
        /* top: 6px; used to include full text in the row by reducing its distance from top.
            Without this, and if the min row height is too less, text encroches onto next row */
        left: 6px;
        /* width is to decide how wide the text will have space to occupy. too much width will cause links to be overlapped*/
        width: 25%;
        padding-right: 10px;
        text-align: left;
        font-weight: bold;
    }

    td:before {
        content: attr(data-title);
    }
}
</style>
<?php } ?>

<?php
 # other link format: <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/darkly/bootstrap.min.css">
if ($_SESSION['theme']== 'cyborg') {
    echo '<meta name="theme-color" content="maroon">
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/cyborg/bootstrap.min.css">';

    echo '
	<style type="text/css">
	#tablediv {
		margin-top: 68px;
	}

	.jumbotron {
		background-image: url(assets/background-night.jpg);
		text-align: center;
	}

	@media only screen and (max-width: 600px) {
		#tablediv {
			margin-top: 54px;
		}
	}
	</style>';
}
if ($_SESSION['theme']== 'darkly') {
    echo '<meta name="theme-color" content="black">
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootswatch/4.1.1/darkly/bootstrap.min.css">';

    echo '
	<style type="text/css">
	#tablediv {
		margin-top: 84px;
	}

	.jumbotron {
		background-image: url(assets/background-night.jpg);
		text-align: center;
	}

	@media only screen and (max-width: 600px) {
		#tablediv {
			margin-top: 70px;
		}
	}
	</style>';
}

if ($_SESSION['theme']== 'litera') {
    echo '<meta name="theme-color" content="pink">
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/litera/bootstrap.min.css">';

    echo '
	<style type="text/css">
	#tablediv {
		margin-top: 71px;
	}

	.jumbotron {
		background-image: url(assets/background.jpg);
		text-align: center;
	}

	@media only screen and (max-width: 600px) {
		#tablediv {
			margin-top: 62px;
		}
		.badge {
			font-size: 50%;
			padding: 0.3em 0.4em;
			margin-bottom: 6px;
		}
	}
	</style>';
}
if ($_SESSION['theme']== 'normal') {
    echo '<meta name="theme-color" content="#253a53">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">';

    echo '<style type="text/css">
	#tablediv {
		margin-top: 70px;
	}

	.jumbotron {
		background-image: url(assets/background.jpg);
		text-align: center;
	}

	@media only screen and (max-width: 600px) {
		#tablediv {
			margin-top: 54px;
		}
	}
	</style>';
}
if ($_SESSION['theme']== 'no-one') {
    echo '<meta name="theme-color" content="white">
	<!--No Theme Specified-->';
}
?>