<?php
session_start();
if ($_SESSION['permission'] == 'admin') {
	include('connect-db.php');
} else {
	header("Location: index.php");
}
?>
<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<?php include 'header.php'; //css-theme detector ?>

	<title>The Help Page Page</title>
</head>
<body>
	<?php include 'navbar.php'; ?>

	<div id="tablediv" class="container-fluid">
		<table class="table table-responsive-sm">
			<thead class="table-dark">
				<th scope="row">Page Types</th>
				<th scope="row">Links</th>
				<th scope="row" colspan="2">Role</th>
			</thead>
			<tbody>
				<tr>
					<th rowspan="3" scope="row">Basic Pages</th>
					<td><a href="help.php">help.php</a></td>
					<td>Gives an overview of all pages in this site, as well as tricky stuff</td>
				</tr>

				<tr>
					<td><a href="index.php">index.php</a></td>
					<td>Members, Supervisors, Admins can login from their respective login options only</td>
				</tr>

				<tr>
					<td><a href="connect-db.php">connect-db.php</a></td>
					<td>Most important page. Has the code to interact with the database. It is imported in every page on this site.</td>
				</tr>

				<tr>
					<th rowspan="10" scope="row">Admin Pages</th>
					<td><a href="council-dashboard.php">council-dashboard.php</a></td>
					<td>Has all the options admin needs plus add member or add council option. (Both admin and supervisors are considered council)</td>
				</tr>
				<tr>
					<td><a href="all-people.php">all-people.php</a></td>
					<td>Lists out all the members. Links given as View Members</td>
				</tr>
				<tr>
					<td><a href="admin-workshop.php">admin-workshop.php</a></td>
					<td>Create new workshop with date and level. As well as see upcoming and completed workshops</td>
				</tr>
				<tr>
					<td><a href="addremove-from-workshop.php">addremove-from-workshop.php</a></td>
					<td>Here, admin can edit attendance list of workshop. Enroll people, kick out people.</td>
				</tr>
				<tr>
					<td><a href="add-new.php">add-new.php</a></td>
					<td>Add new council members or normal members</td>
				</tr>
				<tr>
					<td><a href="all-council.php">all-council.php</a></td>
					<td>Lists out all the council members. Links given as View Council</td>
				</tr>
				<tr>
					<td><a href="edit-council.php">edit-council.php</a></td>
					<td>Edit the fine details about council members. Even retire them if needed.</td>
				</tr>
				<tr>
					<td><a href="edit.php">edit.php</a></td>
					<td>Edit the fine details about members. Almost all details (basic data + attendance of workshops)</td>
				</tr>
				<tr>
					<td><a href="fee.php">fee.php</a></td>
					<td>Edit details about fee status of members</td>
				</tr>
				<tr>
					<td><a href="new-registrations.php">new-registrations.php</a></td>
					<td>Review new registrations before adding to the main database</td>
				</tr>
				<tr>
					<th rowspan="3" scope="row">Member Pages</th>
					<td><a href="one-person.php">one-person.php</a></td>
					<td>Lists out all the details of that one person</td>
				</tr>
				<tr>
					<td><a href="member-edit-self.php">member-edit-self.php</a></td>
					<td>Allows members to edit their basic details</td>
				</tr>
				<tr>
					<td><a href="member-workshop.php">member-workshop.php</a></td>
					<td>Allows members to choose a workshop for themselves</td>
				</tr>
				<tr>
					<th rowspan="2" scope="row">Supervisor Pages</th>
					<td><a href="supervisor_workshop.php">supervisor_workshop.php</a></td>
					<td>A supervisor can manage the day's workshop upon logging in. He should submit the attendance and mark complete the workshop after it is complete. Once marked complete, only admin can edit it again.</td>
				</tr>
				<tr>
					<td><a href="supervisor-dashboard.php">supervisor-dashboard.php</a></td>
					<td>Give supervisor relevant options like view all workshops for today, or edit his own details.</td>
				</tr>
			</tbody>
		</table>

		<h3>Other stuff</h3>
		<h4 id="kit">Member Kit Options</h4>
		<p>
			0 - Approval Pending (people who have just registered on the form, automatically 0 value and seen in the review new registrations section)<br />
			1 - Approved ( people who verify their details and get the kit, only these guys can apply for workshops or any club event)<br />
			2 - Rejected (rejected/not approved for whatever reason, can re apply, record need not remain.. maybe we can clear off their names after say a month)<br />
			3 - Debarred ( blacklisted people, can not rejoin/re apply)<br />
			4 - Completed ( graduated/ completed all stages)<br />
			5 - Discontinued (withdrew their membership midway)<br />
			6- Status unknown (members who are out of contact or whatever until we decide to remove them)<br />
		</p>
	</div>
</body>
</html>
