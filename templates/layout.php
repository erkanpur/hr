<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>HRMS - <?php echo $title ?? 'Dashboard'; ?></title>
  <!-- CSS files -->
  <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet"/>
  <link href="public/css/custom.css" rel="stylesheet"/> <!-- For any custom styles -->
</head>
<body>
  <div class="page">
    <!-- Navbar -->
    <header class="navbar navbar-expand-md d-print-none">
      <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
          <a href=".">
            HRMS
          </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
          <div class="nav-item">
            <a href="#" class="nav-link d-flex lh-1 text-reset p-0">
              <span class="avatar avatar-sm" style="background-image: url(./static/avatars/000m.jpg)"></span>
              <div class="d-none d-xl-block ps-2">
                <div>Paweł Kuna</div>
                <div class="mt-1 small text-muted">UI Designer</div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </header>
    <div class="navbar-expand-md">
      <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
          <div class="container-xl">
            <ul class="navbar-nav">
              <li class="nav-item"> <!-- Removed active class -->
                <a class="nav-link" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/'); ?>" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                  </span>
                  <span class="nav-link-title">
                    Home
                  </span>
                </a>
              </li>
              <!-- NEW PERSONNEL MANAGEMENT DROPDOWN -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#navbar-personnel" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                       <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                       <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                       <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                       <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                    </svg>
                  </span>
                  <span class="nav-link-title">
                    Personnel Management
                  </span>
                </a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees'); ?>">
                    View Employees
                  </a>
                  <a class="dropdown-item" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees/add'); ?>">
                    Add Employee
                  </a>
                  <!-- Future links: View Employees, etc. -->
                </div>
              </li>
              <!-- NEW LEAVE MANAGEMENT DROPDOWN -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#navbar-leave" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-time" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                       <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"></path>
                       <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                       <path d="M15 3v4"></path>
                       <path d="M7 3v4"></path>
                       <path d="M3 11h16"></path>
                       <path d="M18 16.496v1.504l1 1"></path>
                    </svg>
                  </span>
                  <span class="nav-link-title">
                    Leave Management
                  </span>
                </a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_types'); ?>">
                    Manage Leave Types
                  </a>
                  <a class="dropdown-item" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/manage'); ?>">
                    Manage All Requests
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/apply'); ?>">
                    Apply for Leave
                  </a>
                  <a class="dropdown-item" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/history'); ?>">
                    My Leave History
                  </a>
                  <!-- Future links: Leave Requests (for managers), etc. -->
                </div>
              </li>
              <!-- NEW PERFORMANCE DROPDOWN -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#navbar-performance" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trending-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                  </span>
                  <span class="nav-link-title">
                    Performance
                  </span>
                </a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_criteria'); ?>">
                    Manage Review Criteria
                  </a>
                  <a class="dropdown-item" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_periods'); ?>">
                    Manage Review Periods
                  </a>
                  <!-- Other performance related links later -->
                </div>
              </li>
              <!-- Add more navigation items here -->
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="page-wrapper">
      <!-- Page header -->
      <div class="page-header d-print-none">
        <div class="container-xl">
          <div class="row g-2 align-items-center">
            <div class="col">
              <h2 class="page-title">
                <?php echo $title ?? 'Page Title'; ?>
              </h2>
            </div>
          </div>
        </div>
      </div>
      <!-- Page body -->
      <div class="page-body">
        <div class="container-xl">
          <!-- Flash Messages -->
          <?php if (isset($_SESSION['success_message'])): ?>
              <div class="alert alert-success alert-dismissible" role="alert">
                  <div><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php unset($_SESSION['success_message']); ?>
          <?php endif; ?>

          <?php if (isset($_SESSION['error_message'])): ?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                  <div><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php unset($_SESSION['error_message']); ?>
          <?php endif; ?>
          <!-- End Flash Messages -->
          
          <?php echo $content ?? ''; ?>
        </div>
      </div>
      <footer class="footer footer-transparent d-print-none">
        <div class="container-xl">
          <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-lg-auto ms-lg-auto">
              <ul class="list-inline list-inline-dots mb-0">
                <li class="list-inline-item"><a href="https://tabler.io/docs" target="_blank" class="link-secondary" rel="noopener">Documentation</a></li>
                <li class="list-inline-item"><a href="https://github.com/tabler/tabler" target="_blank" class="link-secondary" rel="noopener">Source code</a></li>
              </ul>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
              <ul class="list-inline list-inline-dots mb-0">
                <li class="list-inline-item">
                  Copyright &copy; <?php echo date('Y'); ?>
                  <a href="." class="link-secondary">HRMS</a>.
                  All rights reserved.
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <!-- Libs JS -->
  <!-- Tabler Core -->
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js" defer></script>
  <!-- Custom JS -->
  <script src="public/js/custom.js" defer></script>
</body>
</html>
