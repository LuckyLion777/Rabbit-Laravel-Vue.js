<header class="main-header">
    <!-- Logo -->
    <a class="logo" href="{{ url('/') }}">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><img src="img/logo-tiny.png" alt="HUTCH"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>HUTCH</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav role="navigation" class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a role="button" data-toggle="offcanvas" class="sidebar-toggle" href="#">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <ul class="nav navbar-nav">
            <li id="qrcode-scan-modal">
                <a href="#" data-toggle="modal" data-target="#qrModal"><i class="fa fa-qrcode"></i></a>
            </li>
        </ul>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
            
                <!-- Messages: style can be found in dropdown.less-->

                <!-- Notifications: style can be found in dropdown.less -->

                <!-- Tasks: style can be found in dropdown.less -->

                <notification-tab></notification-tab>

                <!-- User Account: style can be found in dropdown.less -->

                <!-- Control Sidebar Toggle Button -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="step8"><i class="fa fa-cogs"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="#" v-link="{ path: '/settings' }"><i class="fa fa-cog"></i> Settings</a></li>
                        <li><a href="#" v-link="{ path: '/account' }"><i class="fa fa-user"></i> Account</a></li>
                        <li><tour-link></tour-link></li>
                        <li><a href="{{ url('logout') }}" id="logout"><i class="fa fa-power-off"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="qrmodaltitle">QR Code Scanner</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container" style="width: 100%;">
            <div class="row">
                <div class="col-md-12">
                    <form class="dropzone" id="image-upload" action="qrcode/upload" enctype="multipart/form-data" method="post">
                    <div>
                        <h5>Upload QRcode Image By Click On Box</h5>
                    </div>
                    </form>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="qrscan">Scan</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
        var uploaded_image_url;
        Dropzone.options.imageUpload = {
            maxFilesize         :       1,
            maxFiles            :       1,
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,

            success: function( file, response ){
                console.log(response.success);
                uploaded_image_url = 'https://htch.us:4433/images/' + response.success;
            }
        };
</script>