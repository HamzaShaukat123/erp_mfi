<div id="loader">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<header class="page-header">
	
	<div class="logo-container d-md-none">
		<a href="/" class="logo ">
			<img src="/assets/img/logo.png" width="70px" alt="MFI Logo" />
		</a>
		<div id="userbox" class="userbox" style="float:right !important;">
			<a href="#" data-bs-toggle="dropdown" style="margin-right: 20px;">
				<div class="profile-info"> 
					<span class="name">{{session('user_name')}}</span>
					<span class="role">{{session('role_name')}}</span>
				</div>
				<i class="fa custom-caret"></i>
			</a>
			<div class="dropdown-menu" >
				<ul class="list-unstyled">
					<li>
						<a role="menuitem" tabindex="-1" href="#changePassword" class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal"><i class="bx bx-lock"></i> Change Password</a>
					</li>
					<li>	
						<form action="/logout" method="POST">
							@csrf
							<button style="background: transparent;border: none;font-size: 14px;" type="submit" role="menuitem" tabindex="-1"><i class="bx bx-power-off"></i> Logout</button>
						</form>
					</li>
					@if(session('user_role')==1 || session('user_role')==2)
					<li>
						<a role="menuitem" tabindex="-1" href="{{ route('backup.database') }}"><i class="bx bx-cloud-download"></i> DB Backup</a>
					</li>
					<li>
						<a role="menuitem" tabindex="-1" href="{{ route('backup.files') }}"><i class="bx bx-files"></i> Files Backup</a>
					</li>
					@endif
				</ul>
			</div>
			<i class="fas fa-bars toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened" aria-label="Toggle sidebar"></i>
		</div>
		<div class="right-wrapper text-end d-none d-md-block">
			<div id="userbox" class="userbox" style="float:right !important;">
				<a href="#" data-bs-toggle="dropdown">
					<div class="profile-info"> 
						<span class="name">{{session('user_name')}}</span>
						<span class="role">{{session('role_name')}}</span>
					</div>
					<i class="fa custom-caret"></i>
				</a>
				<div class="dropdown-menu" >
					<ul class="list-unstyled">
						<li>
							<a role="menuitem" tabindex="-1" href="#changePassword" class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal"><i class="bx bx-lock"></i> Change Password</a>
						</li>
						<li>	
							<form action="/logout" method="POST">
								@csrf
								<button style="background: transparent;border: none;font-size: 14px;" type="submit" role="menuitem" tabindex="-1"><i class="bx bx-power-off"></i> Logout</button>
							</form>
						</li>
					</ul>
				</div>
				<i class="fas fa-bars toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened" aria-label="Toggle sidebar"></i>

			</div>
		</div>
	</div>

	<div class="logo-container d-none d-md-block">
		<div id="userbox" class="userbox" style="float:right !important;">
			<a class="btn btn-success" href="/pos"> POS System</a>

			<a href="#" data-bs-toggle="dropdown" style="margin-right: 20px;">
				<div class="profile-info"> 
					<span class="name">{{session('user_name')}}</span>
					<span class="role">{{session('role_name')}}</span>
				</div>
				<i class="fa custom-caret"></i>
			</a>
			<div class="dropdown-menu" >
				<ul class="list-unstyled">
					<li>
						<a role="menuitem" tabindex="-1" href="#changePassword" class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal"><i class="bx bx-lock"></i> Change Password</a>
					</li>
					<li>	
						<form action="/logout" method="POST">
							@csrf
							<button style="background: transparent;border: none;font-size: 14px;" type="submit" role="menuitem" tabindex="-1"><i class="bx bx-power-off"></i> Logout</button>
						</form>
					</li>
					@if(session('user_role')==1 || session('user_role')==2)
					<li>
						<a role="menuitem" tabindex="-1" href="{{ route('backup.database') }}"><i class="bx bx-cloud-download"></i> DB Backup</a>
					</li>
					<li>
						<a role="menuitem" tabindex="-1" href="{{ route('backup.files') }}"><i class="bx bx-file"></i> Files Backup</a>
					</li>
					@endif
				</ul>
			</div>
		</div>
	</div>

	<div id="changePassword" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
		<form id="changePasswordForm" method="post" action="{{ route('change-user-password') }}" 
			  style="width: 75%" enctype="multipart/form-data" 
			  onkeydown="return event.key != 'Enter';">
			@csrf
			<header class="card-header">
				<h2 class="card-title">Change Password</h2>
			</header>
			<div class="card-body">
				<div class="row form-group">    
					<div class="col-12 mb-2">
						<label>Current Password</label>
						<input type="password" class="form-control" placeholder="Current Password" id="current_password" name="current_password" required>
					</div> 
					<div class="col-12 mb-2">
						<label>New Password</label>
						<input type="password" class="form-control" placeholder="New Password" id="new_password" minlength="8" name="new_password" required>
					</div>
					<div class="col-12 mb-2">
						<label>Confirm New Password</label>
						<input type="password" class="form-control" placeholder="Confirm New Password" minlength="8" id="confirm_new_password" required>
					</div>
				</div>
			</div>
			<footer class="card-footer">
				<div class="row">
					<div class="col-md-12 text-end">
						<button type="submit" class="btn btn-primary">Change Password</button>
						<button type="button" class="btn btn-default modal-dismiss">Cancel</button>
					</div>
				</div>
			</footer>
		</form>
	</div>
</header>
