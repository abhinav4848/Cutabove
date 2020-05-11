<?php $url = $_SERVER['REQUEST_URI'] //returns something like /add-new.php?>


<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <a class="navbar-brand" href="index.php">Hello, <?php echo strtok($_SESSION['name'], " ");
    if ($_SESSION['permission'] == 'admin') {
        echo ' <span class="badge badge-danger">Admin</span>';
    } elseif ($_SESSION['permission'] == 'supervisor') {
        echo ' <span class="badge badge-primary">Supervisor</span>';
    } else {
        echo ' <span class="badge badge-success">Member</span>';
    } ?> </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <?php
            if ($_SESSION['permission'] == 'admin') {
                if (strpos($url, '/all-people.php')!== false) {
                    echo '<li class="nav-item dropdown active">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					View Members
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="add-new.php">Add New</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="#">View All</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="member-applications.php">Old Manage</a>
					</div>
					</li>';
                } elseif (strpos($url, '/edit.php')!== false or strpos($url, '/fee.php')!== false or strpos($url, '/member-workshop.php')!== false or strpos($url, '/one-person.php')!== false) {
                    echo '<li class="nav-item dropdown active">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					View Members
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">';

                    if (strpos($url, '/one-person.php')!== false) {
                        echo '<a class="dropdown-item" href="#">Details (Current)</a>';
                    } else {
                        echo '<a class="dropdown-item" href="one-person.php?id='.$row['id'].'">Details</a>';
                    }

                    if (strpos($url, '/edit.php')!== false) {
                        echo '<a class="dropdown-item" href="#">Edit this user (Current)</a>';
                    } else {
                        echo '<a class="dropdown-item" href="edit.php?id='.$row['id'].'">Edit this user</a>';
                    }

                    if (strpos($url, '/member-workshop.php')!== false) {
                        echo '<a class="dropdown-item" href="#">Assign Workshop (Current)</a>';
                    } else {
                        echo '<a class="dropdown-item" href="member-workshop.php?id='.$row['id'].'">Assign Workshop</a>';
                    }

                    if (strpos($url, '/fee.php')!== false) {
                        echo '<a class="dropdown-item" href="#">Fee (Current)</a>';
                    } else {
                        echo '<a class="dropdown-item" href="fee.php?id='.$row['id'].'">Fee</a>';
                    }

                    echo '<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="add-new.php">Add New</a>';

                    if (strpos($url, '/certificate.php')!== false) {
                        echo '<a class="dropdown-item" href="#">Certificate (Current)</a>';
                    } else {
                        echo '<a class="dropdown-item" href="certificate.php?id='.$row['id'].'">View Certificate</a>';
                    }
                    
                    echo '<div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="all-people.php?kit=0">Approval Pending</a>
					<a class="dropdown-item" href="all-people.php?kit=100">Approved</a>
					<a class="dropdown-item" href="all-people.php?kit=2">Rejected</a>
					<a class="dropdown-item" href="all-people.php?kit=3">Debarred</a>
					<a class="dropdown-item" href="all-people.php?kit=4">Completed</a>
					<a class="dropdown-item" href="all-people.php?kit=5">Discontinued</a>
					<a class="dropdown-item" href="all-people.php?kit=6">Status Unknown</a>';
                    ;
                    
                    if ($_SESSION['god']== 1) {
                        echo '<div class="dropdown-divider"></div>';
                        echo '<a class="dropdown-item text-danger" href="deletion.php?clg_reg='.$row['clg_reg'].'">Delete this member</a>';
                    }

                    echo '</div></li>';
                } else {
                    echo '<li class="nav-item">
					<a class="nav-link" href="all-people.php">View Members</a>
					</li>';
                }

                if (strpos($url, '/supervisor_workshop.php')!== false) {
                    echo '<li class="nav-item dropdown active">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Workshop
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="#">Workshop Details (Current)</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="analyse-workshops.php">Analyse Workshops</a>
					<a class="dropdown-item" href="admin-workshop.php">Admin Workshop</a>';
                    if ($_SESSION['god']== 1) {
                        echo '<div class="dropdown-divider"></div>';
                        echo '<a class="dropdown-item text-danger" href="deletion.php?wk_id='.$row['workshop_id'].'">Delete this Workshop</a>';
                    }
                    echo '</div>';
                } elseif (strpos($url, '/admin-workshop.php')!== false or strpos($url, '/analyse-workshops.php')!== false) {
                    echo '<li class="nav-item dropdown active">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Workshop
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">';
                    if (strpos($url, '/admin-workshop.php')!== false) {
                        echo '<a class="dropdown-item" href="analyse-workshops.php">Analyse Workshops</a>
						<a class="dropdown-item" href="#">Admin Workshop (Current)</a>';
                    } else {
                        echo '<a class="dropdown-item" href="#">Analyse Workshops (Current)</a>
						<a class="dropdown-item" href="admin-workshop.php">Admin Workshop</a>';
                    }
                    
                    echo '</div>';
                } else {
                    echo '<li class="nav-item';
                    if (strpos($url, '/admin-workshop.php')!== false) {
                        echo ' active';
                    }
                    echo '">
					<a class="nav-link" href="admin-workshop.php">Workshop</a>
					</li>';
                }
                
                if (strpos($url, '/all-council.php')!== false or strpos($url, '/edit-council.php')!== false or strpos($url, '/supervisor-apply-workshop.php')!== false or strpos($url, '/supervisor-attendance.php')!== false) {
                    echo '<li class="nav-item dropdown active">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">View Core</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="add-new.php">Add New</a>';
                    if (strpos($url, '/all-council.php')!== false) {
                        echo '<a class="dropdown-item" href="#">View All (Current)</a>';
                    } else {
                        echo '<a class="dropdown-item" href="all-council.php">View All</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="supervisor-apply-workshop.php?id=' . $row['council_id'] . '">Workshop</a>
						<a class="dropdown-item" href="supervisor-attendance.php?id=' . $row['council_id'] . '">Attendance</a>';
                        if ($_SESSION['god']==1) {
                            echo '<div class="dropdown-divider"></div>';
                            echo '<a class="dropdown-item text-danger" href="deletion.php?council_username='.$row['username'].'">Delete this member</a>';
                        }
                    }
                    echo '<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="edit-council.php?id=' . $_SESSION['id'] . '">Own Profile</a>
					</div>
					</li>';
                } else {
                    echo '<li class="nav-item">
					<a class="nav-link" href="all-council.php">View Core</a>
					</li>';
                }

                echo '<li class="nav-item';
                if (strpos($url, '/transaction-history.php')!== false) {
                    echo ' active';
                }
                echo '">
				<a class="nav-link" href="transaction-history.php">Transaction History</a>
				</li>';

                if (strpos($url, '/edit-council.php')!== false or strpos($url, '/member-workshop.php')!== false or strpos($url, '/one-person.php')!== false) {
                    echo '<li class="nav-item nav-link">Last edited by '.$row['edited_by'].' at '.$row['last_edited_at'].'</li>';
                }

                if (strpos($url, '/member-applications.php')!== false) {
                    echo '<form class="form-inline" method="get">
					<li class="nav-item">
					<select class="form-control" name="kit" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
					<option value="member-applications.php?kit=0"';
                    if (array_key_exists('kit', $_GET) and $_GET['kit']== 0) {
                        echo 'selected';
                    }
                    echo '>Approval Pending</option>
					<option value="member-applications.php?kit=1"';
                    if (array_key_exists('kit', $_GET) and $_GET['kit']== 1) {
                        echo 'selected';
                    } elseif (array_key_exists('kit', $_GET)) {
                    } else {
                        echo 'selected';
                    }
                    echo '>Approved</option>
					<option value="member-applications.php?kit=2"';
                    if (array_key_exists('kit', $_GET) and $_GET['kit']== 2) {
                        echo 'selected';
                    }
                    echo '>Rejected</option>
					<option value="member-applications.php?kit=3"';
                    if (array_key_exists('kit', $_GET) and $_GET['kit']== 3) {
                        echo 'selected';
                    }
                    echo '>Debarred</option>
					<option value="member-applications.php?kit=4"';
                    if (array_key_exists('kit', $_GET) and $_GET['kit']== 4) {
                        echo 'selected';
                    }
                    echo '>Completed</option>
					<option value="member-applications.php?kit=5"';
                    if (array_key_exists('kit', $_GET) and $_GET['kit']== 5) {
                        echo 'selected';
                    }
                    echo '>Discontinued</option>
					<option value="member-applications.php?kit=6"';
                    if (array_key_exists('kit', $_GET) and $_GET['kit']== 6) {
                        echo 'selected';
                    }
                    echo '>Status Unknown</option>
					</select>
					</li>
					<!--<li class="nav-item"><button type="submit" name="submit" class="btn btn-danger" value="kit">Fetch</button></li>-->
					</form>';
                }
            }
            if ($_SESSION['permission'] == 'supervisor') {
                if (strpos($url, '/council-dashboard.php')!== false or strpos($url, '/supervisor-apply-workshop.php')!== false or strpos($url, '/supervisor-attendance.php')!== false or strpos($url, '/edit-council.php')!== false) {
                    if (strpos($url, '/supervisor-apply-workshop.php')!== false) {
                        echo '<li class="nav-item active">
						<a class="nav-link" href="#">Apply for Workshops</a>
						</li>';
                    } else {
                        echo '<li class="nav-item">
						<a class="nav-link" href="supervisor-apply-workshop.php">Apply for Workshops</a>
						</li>';
                    }
                    if (strpos($url, '/supervisor-attendance.php')!== false) {
                        echo '<li class="nav-item active">
						<a class="nav-link" href="#">Attendance</a>
						</li>';
                    } else {
                        echo '<li class="nav-item">
						<a class="nav-link" href="supervisor-attendance.php">Attendance</a>
						</li>';
                    }
                    if (strpos($url, '/edit-council.php')!== false) {
                        echo '<li class="nav-item active">
						<a class="nav-link" href="#">Profile</a>
						</li>';
                    } else {
                        echo '<li class="nav-item">
						<a class="nav-link" href="edit-council.php?id='.$_SESSION['id'].'">Profile</a>
						</li>';
                    }
                }
                if (strpos($url, '/one-person.php')!== false) {
                    echo '<li class="nav-item active">
					<a class="nav-link" href="#">Details (Current)</a>
					</li>';
                } elseif (strpos($url, '/member-workshop.php')!== false) {
                    echo '<li class="nav-item">
					<a class="nav-link" href="one-person.php?id='.$row['id'].'">Details</a>
					</li>';
                }

                if (strpos($url, '/member-workshop.php')!== false) {
                    echo '<li class="nav-item active">
					<a class="nav-link" href="#">Sign Up for Workshop (Current)</a>
					</li>';
                } elseif (strpos($url, '/one-person.php')!== false) {
                    echo '<li class="nav-item">
					<a class="nav-link" href="member-workshop.php?id='.$row['id'].'">Sign Up for Workshop</a>
					</li>';
                }
            }
            if ($_SESSION['permission'] == 'member') {
                if (strpos($url, '/one-person.php')!== false) {
                    echo '<li class="nav-item active">
					<a class="nav-link" href="#">Details</a>
					</li>';
                } else {
                    echo '<li class="nav-item">
					<a class="nav-link" href="one-person.php">Details</a>
					</li>';
                }

                if (strpos($url, '/member-self-edit.php')!== false) {
                    echo '<li class="nav-item active">
					<a class="nav-link" href="#">Edit Basic Info</a>
					</li>';
                } else {
                    echo '<li class="nav-item">
					<a class="nav-link" href="member-self-edit.php">Edit Basic Info</a>
					</li>';
                }

                if (strpos($url, '/member-workshop.php')!== false) {
                    echo '<li class="nav-item active">
					<a class="nav-link" href="#">Sign Up for Workshop</a>
					</li>';
                } else {
                    echo '<li class="nav-item">
					<a class="nav-link" href="member-workshop.php">Sign Up for Workshop</a>
					</li>';
                }
            }
            if (isset($_GET['successEdit']) and $_GET['successEdit'] == 1) {
                echo '<li class="nav-item nav-link">';
                echo '<span class="badge badge-success" id="successEdit" style="opacity: 1;transition: 2s opacity;">Successfully Edited!</span>';
                echo '</li>'; ?>
            <script>
            window.onload = () => {
                window.setTimeout(() => {
                        //https://codepen.io/ssddayz/pen/zKkaBQ
                        document.getElementById('successEdit').style.opacity = '0';

                        // https://blog.teamtreehouse.com/getting-started-with-the-history-api#manipulating-the-session-history
                        let url = window.location.href;
                        newurl = url.replace('successEdit=1', '');
                        window.history.pushState({}, "Successfully Edited", newurl);
                    },
                    2000); //2 seconds
            };
            </script>
            <?php
            }
            ?>
        </ul>
        <div class="form-inline my-2 my-lg-0">
            <?php
            if (strpos($url, '/member-applications.php')!== false or strpos($url, '/all-council.php')!== false) {
                echo '<input class="form-control mr-sm-2" type="search" placeholder="Search Table" id="searchDetails">';
            }
            ?>
            <a class="nav-link" href="index.php?logout=1"><button class="btn btn-outline-danger my-2 my-sm-0"
                    type="submit">Logout</button></a>
        </div>
    </div>
</nav>