<?php require_once("./../_init.php") ?>
<?php
    $user = signedIn(null, "./../sign-in.php");
    
    $messages = [];
    $redirect = null;
    $entries = [];

    $st = query("
        select e.* from employees as e
        left join employees_archive as et
        on e.id = et.employee_id
        where et.employee_id is not null
    ");
    $entries = $st->fetchAll();
?>
<?php require_once("./../_start.php"); ?>
<a class="link-button" href="./"><< Employees</a>
<h1>Archived Employees</h1>
<em>View the stashed records.</em>
<br>
<?php render("./../messages.php", ["messages" => $messages]) ?>
<table>
    <tr>
        <th>Employee ID</th>
        <th>Family Name</th>
        <th>Given Name</th>
        <th>Middle Name</th>
        <th>Age</th>
        <th>Sex</th>
        <th>Mobile Number</th>
        <th style="width: 20%;">Address</th>
    </tr>
    <?php if(count($entries) > 0) { ?>
        <?php foreach($entries as $i => $entry) {
            $id = $entry["id"];
            $eid = $entry["employee_id"];
            $fn = $entry["family_name"];
            $gn = $entry["given_name"];
            $mn = $entry["middle_name"];
            $age = $entry["age"];
            $sex = $entry["sex"];
            $mob = $entry["mobile_number"];
            $add = $entry["address"];
        ?>
            <tr>
                <td><?= $eid ?></td>
                <td><?= $fn ?></td>
                <td><?= $gn ?></td>
                <td><?= $mn ?></td>
                <td><?= $age ?></td>
                <td><?= $sex ? "M" : "F" ?></td>
                <td><?= $mob ?></td>
                <td><?= $add ?></td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr><td colspan="8">No Entries</td></tr>
    <?php } ?>
</table>
<?php require_once("./../_end.php"); ?>
<?php if($redirect != null) { ?>
    <script>
        setTimeout(function() {
            window.location="<?= $redirect ?>";
        }, 7000);
    </script>
<?php } ?>