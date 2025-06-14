<?php
// Start the session
session_start();

















?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
.sidebar {
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
    min-height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    z-index: 1000;
}
.sidebar .nav-link {
    color: #bdc3c7;
    padding: 15px 25px;
    border-radius: 0;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}
.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
    border-left-color: #3498db;
}
.main-content {
    margin-left: 250px;
    padding: 0;
}
.top-navbar {
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 1rem 2rem;
    margin-bottom: 2rem;
}
.content-wrapper {
    padding: 0 2rem 2rem;
}
.card {
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border-radius: 15px;
}
.page-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 0.5rem;
}
.breadcrumb {
    background: none;
    padding: 0;
}
.permission-item {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}
.activity-timeline {
    position: relative;
}
.form-control-plaintext {
    font-weight: 600;
    color: #2c3e50 !important;
}
</style>

</head>
<body class="bg-light">

<?php include('sidemenu.php'); ?>

<!-- Account Page Content (insert between navbar and footer) -->
<div class="main-content">
    <!-- Top Navbar -->
    <div class="top-navbar d-flex justify-content-between align-items-center">
        <div>
            <h2 class="page-title mb-0">Account Settings</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin_dashboard.html" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Account</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-link text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-lg me-2"></i>Admin
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content-wrapper">
        <div class="row">
            <!-- Profile Overview -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="profile-avatar mx-auto mb-3" style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-shield fa-3x text-white"></i>
                        </div>
                        <h4 class="mb-1"><?php echo $_SESSION['full_name'] ?></h4>
                        <p class="text-muted mb-3"> 
                            <?php 
                                if ($_SESSION['role_type'] == 'admin') {
                                    echo 'Super Administrator';
                                } elseif ($_SESSION['role_type'] == 'seller') {
                                    echo 'Individual Vendor';
                                } else {
                                    echo $_SESSION['role_type']; // Fallbacks to original value
                                }
                            ?>
                        </p>
                        <span class="badge bg-success mb-3">Active</span>
                        <div class="d-grid">
                            <button class="btn btn-outline-primary">Change Avatar</button>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card mt-4">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">Account Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="mb-1 text-primary">156</h4>
                                    <small class="text-muted">Login Sessions</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-1 text-success">98.5%</h4>
                                <small class="text-muted">Uptime</small>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Last Login:</span>
                            <span>Today, 09:45 AM</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-muted">Account Created:</span>
                            <span><?php echo $_SESSION['registration_date'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Personal Information</h5>
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Full Name</label>
                                <div class="form-control-plaintext"><?php echo $_SESSION['full_name'] ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Email Address</label>
                                <div class="form-control-plaintext"><?php echo $_SESSION['email'] ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Phone Number</label>
                                <div class="form-control-plaintext"><?php echo $_SESSION['phone_number'] ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Role</label>
                                <div class="form-control-plaintext">
                                    <?php 
                                        if ($_SESSION['role_type'] == 'admin') {
                                            echo '<span class="badge bg-danger">Admin</span>';
                                        } elseif ($_SESSION['role_type'] == 'seller') {
                                            echo '<span class="badge bg-success">Seller</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">' . $_SESSION['role_type'] . '</span>';
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <?php 
                                    if ($_SESSION['role_type'] == 'admin') {
                                        echo 'Department';
                                    } elseif ($_SESSION['role_type'] == 'seller') {
                                        echo 'Username';
                                    } else {
                                        echo 'Department';
                                    }
                                    ?>
                                </label>
                                <div class="form-control-plaintext">
                                    <?php 
                                    if ($_SESSION['role_type'] == 'admin') {
                                        echo 'IT & Operations';
                                    } elseif ($_SESSION['role_type'] == 'seller') {
                                        echo $_SESSION['username'];
                                    } else {
                                        echo 'IT & Operations';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <?php 
                                    if ($_SESSION['role_type'] == 'admin') {
                                        echo 'Employee ID';
                                    } elseif ($_SESSION['role_type'] == 'seller') {
                                        echo 'User ID';
                                    } else {
                                        echo 'Employee ID';
                                    }
                                    ?>
                                </label>
                                <div class="form-control-plaintext">
                                    <?php echo $_SESSION['user_id'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Security Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Two-Factor Authentication</h6>
                                        <small class="text-muted">Add an extra layer of security to your account</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="twoFactor" checked>
                                        <label class="form-check-label text-success" for="twoFactor">Enabled</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Email Notifications</h6>
                                        <small class="text-muted">Receive email alerts for important activities</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                                        <label class="form-check-label text-success" for="emailNotif">Enabled</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Login Alerts</h6>
                                        <small class="text-muted">Get notified of new login attempts</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="loginAlerts">
                                        <label class="form-check-label text-muted" for="loginAlerts">Disabled</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-warning w-100">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-download me-2"></i>Download Backup Codes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions & Access -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Permissions & Access</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="permission-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-users text-primary me-2"></i>
                                            <span>User Management</span>
                                        </div>
                                        <?php 
                                        if ($_SESSION['role_type'] == 'admin') {
                                            echo '<span class="badge bg-success">Full Access</span>';
                                        } else {
                                            echo '<span class="badge bg-danger">Restricted</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="permission-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-box text-primary me-2"></i>
                                            <span>Product Management</span>
                                        </div>
                                        <span class="badge bg-success">Full Access</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="permission-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-shopping-cart text-primary me-2"></i>
                                            <span>Order Management</span>
                                        </div>
                                        <span class="badge bg-success">Full Access</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="permission-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-chart-bar text-primary me-2"></i>
                                            <span>Analytics & Reports</span>
                                        </div>
                                        <span class="badge bg-success">Full Access</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="permission-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-cog text-primary me-2"></i>
                                            <span>System Settings</span>
                                        </div>
                                        <?php 
                                        if ($_SESSION['role_type'] == 'admin') {
                                            echo '<span class="badge bg-success">Full Access</span>';
                                        } else {
                                            echo '<span class="badge bg-danger">Restricted</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="permission-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-database text-primary me-2"></i>
                                            <span>Database Access</span>
                                        </div>
                                        <?php 
                                        if ($_SESSION['role_type'] == 'admin') {
                                            echo '<span class="badge bg-warning">Limited</span>';
                                        } else {
                                            echo '<span class="badge bg-warning">Limited</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>