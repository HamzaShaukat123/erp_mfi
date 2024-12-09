<!doctype html>
<html class="fixed js flexbox flexboxlegacy no-touch csstransforms csstransforms3d no-overflowscrolling webkit chrome win js no-mobile-device custom-scroll sidebar-left-collapsed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>MFI | Software</title>
		<link rel="icon" type="image/x-icon" href="/assets/img/favicon.png">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->

		<link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="/assets/vendor/animate/animate.compat.css">
		<link rel="stylesheet" href="/assets/vendor/font-awesome/css/all.min.css" />
		<link rel="stylesheet" href="/assets/vendor/boxicons/css/boxicons.min.css" />
		<link rel="stylesheet" href="/assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
		<link rel="stylesheet" href="/assets/vendor/datatables/media/css/dataTables.bootstrap5.css" />
		<link rel="stylesheet" href="/assets/vendor/select2/css/select2.css" />
		<link rel="stylesheet" href="/assets/vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />
		<link rel="stylesheet" href="/assets/vendor/bootstrap-multiselect/css/bootstrap-multiselect.css" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="/assets/css/theme.css" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="/assets/css/skins/default.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="/assets/css/custom.css">
	</head>
	<style>
		/* Loader styles */
		#loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        #loader.hidden {
            display: none;
        }
		.cust-pad {
			padding-top: 0; /* or any other default padding */
		}
		.home-cust-pad {
			padding-top: 0; /* or any other default padding */
		}
		.sidebar-logo{
			width:30%;
		}

		@media (min-width: 768px) {
			.cust-pad {
				padding: 85px 20px 0px 20px;
			}
			.home-cust-pad {
				padding: 60px 15px 0px 15px;
			}
			.sidebar-logo{
				width:50%;
			}	
			
		}
    </style>
	<div id="timeoutModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
		<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; text-align: center;">
			<h4>Do you want to continue your session?</h4>
			<button class="btn btn-primary modal-dismiss" id="continueSession">Yes, continue</button>
			<button class="btn btn-danger" id="logoutSession">No, logout</button>
		</div>
	</div>