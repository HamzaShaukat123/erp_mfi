<!doctype html>
<html>
	<head>
		<!-- Basic -->
		<meta charset="UTF-8">
		<title>MFI | Software</title>
		<link rel="icon" type="image/x-icon" href="/assets/img/favicon.png">
		
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
		<link rel="stylesheet" href="/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />
		<link rel="stylesheet" href="/assets/vendor/owl.carousel/assets/owl.carousel.css" />
		<link rel="stylesheet" href="/assets/vendor/owl.carousel/assets/owl.theme.default.css" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="/assets/css/theme.css" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="/assets/css/skins/default.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="/assets/css/custom.css">

		<!-- Head Libs -->
		<script src="/assets/vendor/modernizr/modernizr.js"></script>
		<style>
			.resp-cont {
				width:60%;
			}

			@media (max-width: 768px) {
				.resp-cont {
					width:80%
				}
			}
		</style>
	</head>
	<body style="background:#fff;">
		<!-- start: page -->
			<div class="row">
				<div class="col-12 col-md-6 text-center">
					<div class="container resp-cont" style="position: relative;top: 20%;">
						<a href="/" class="logo">
							<img src="/assets/img/logo.png" height="60" alt="MFI Logo" />
						</a>					
						<h2 class="mb-0 text-primary">Welcome Back</h2>
						<p class="text-dark mb-4">Please Login To Continue</p>
						@if ($errors->has('error'))
							<div style="color: red;">
								{{ $errors->first('error') }}
							</div>
						@endif

						<form method="post" action="{{ route('userlogin') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" id="loginForm">
							@csrf							
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-text bg-light text-primary" style="border-top-left-radius:15px;border-bottom-left-radius:15px;" >
										<i class="bx bx-user text-4"></i>
									</span>
									<input class="form-control" required name="username" placeholder="username" type="text" style="border-top-right-radius:15px;border-bottom-right-radius:15px;"/>
									<input class="form-control" required name="browser_id" id="browser_id"  type="hidden" style="border-top-right-radius:15px;border-bottom-right-radius:15px;" />
								</div>
							</div>

							<div class="form-group mb-3">
								<div class="input-group">
									<span class="input-group-text bg-light text-primary" style="border-top-left-radius:15px;border-bottom-left-radius:15px;">
										<i class="bx bx-lock text-4"></i>
									</span>
									<input class="form-control" required name="password" placeholder="password" type="password" id="password" style="border-top-right-radius:15px;border-bottom-right-radius:15px;" />
								</div>
							</div>

							@if ($errors->has('not_registered'))
								<div class="form-group mb-3">
									<div class="input-group">
										<span class="input-group-text bg-light text-primary" style="border-top-left-radius:15px;border-bottom-left-radius:15px;" >
											<i class="bx bx-key text-4"></i>
										</span>
										<input class="form-control" required name="otp" placeholder="otp" type="text" style="border-top-right-radius:15px;border-bottom-right-radius:15px;" />
									</div>
								</div>
							@endif

							<div class="col-sm-12">
								<span class="mt-3 mx-2 text-start" style="display:block"> <input type="checkbox" onclick="showPassword()"> Show Password </span>
								<button type="submit" class="btn btn-primary mt-2" style="font-size: 0.9rem;padding: 8.52px 18px;border-radius:15px;width:100%">Continue</button>
							</div>
						</form>

						<p class="text-center text-muted mt-3 mb-3">&copy; Copyright 2025. All Rights Reserved.</p>
					</div>
				</div>
				<div class="col-md-6 d-none d-lg-block">
					<div class="owl-carousel owl-theme mb-0" data-plugin-carousel data-plugin-options='{ "dots": false, "nav": true, "items": 1, "autoplay": true }'>
						<div class="item"><img src="/assets/img/slide1.png" style="background-repeat: no-repeat;background-size: auto;height:100vh" alt=""></div>
						<div class="item"><img src="/assets/img/slide2.png" style="background-repeat: no-repeat;background-size: auto;height:100vh" alt=""></div>
						<div class="item"><img src="/assets/img/slide3.png" style="background-repeat: no-repeat;background-size: auto;height:100vh" alt=""></div>
						<div class="item"><img src="/assets/img/slide4.png" style="background-repeat: no-repeat;background-size: auto;height:100vh" alt=""></div>
						<div class="item"><img src="/assets/img/slide5.png" style="background-repeat: no-repeat;background-size: auto;height:100vh" alt=""></div>
						<div class="item"><img src="/assets/img/slide6.png" style="background-repeat: no-repeat;background-size: auto;height:100vh" alt=""></div>
						<div class="item"><img src="/assets/img/slide7.png" style="background-repeat: no-repeat;background-size: auto;height:100vh" alt=""></div>
						<div class="item"><img src="/assets/img/slide8.png" style="background-repeat: no-repeat;background-size: auto;height:100vh" alt=""></div>
						<div class="item"><img src="/assets/img/slide9.png" style="background-repeat: no-repeat;background-size: auto;height:100vh" alt=""></div>
					</div>
				</div>
			</div>

		<!-- end: page -->

		<!-- Vendor -->
		<script src="/assets/vendor/jquery/jquery.js"></script>
		<script src="/assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="/assets/vendor/popper/umd/popper.min.js"></script>
		<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
		<script src="/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="/assets/vendor/common/common.js"></script>
		<script src="/assets/vendor/nanoscroller/nanoscroller.js"></script>
		<script src="/assets/vendor/magnific-popup/jquery.magnific-popup.js"></script>
		<script src="/assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
		<script src="/assets/vendor/owl.carousel/owl.carousel.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js"></script>
		<!-- Specific Page Vendor -->

		<!-- Theme Base, Components and Settings -->
		<script src="/assets/js/theme.js"></script>

		<!-- Theme Custom -->
		<script src="/assets/js/custom.js"></script>

		<!-- Theme Initialization Files -->
		<script src="/assets/js/theme.init.js"></script>

		<script>
        	function showPassword() {
				var x = document.getElementById("password");
				if (x.type === "password") {
					x.type = "text";
				} else {
					x.type = "password";
				}
			}

			document.getElementById('loginForm').addEventListener('submit', function(event) {
				event.preventDefault();  // Prevents the form from submitting immediately
				const fpPromise = FingerprintJS.load();
				fpPromise.then(fp => {
					fp.get().then(result => {
						const visitorId = result.visitorId;
						$('#browser_id').val(visitorId);
						document.getElementById('loginForm').submit(); // Trigger form submission
					});
				});
			});
    	</script>
	</body>
</html>