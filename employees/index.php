<?php require_once("./../_init.php") ?>
<?php
    $user = signedIn(null, "./../sign-in.php");
    
    $messages = [];
    $redirect = null;
    $entries = [];

    if(check($_POST, "submit", "archive")) {
        $sel = $_POST["selected"] ?? [];
        if(count($sel) > 0) {
            $values = join(", ", array_map(fn($e) => "(?)", $sel));
            try {
                $st = query("
                    insert into employees_archive (employee_id)
                    values $values
                ", $sel);

                array_push($messages, [
                    "type" => "positive",
                    "title" => "Successfully Archived Employee(s)",
                ]);

                $redirect = "./archive.php";
            } catch (\Throwable $th) {
                echo var_dump($th);
                array_push($messages, [
                    "type" => "negative",
                    "title" => "Failed to Archive Employee(s)"
                ]);
            }
        }
    } elseif(check($_POST, "submit", "delete")) {
        $sel = $_POST["selected"] ?? [];
        if(count($sel) > 0) {
            $values = join(", ", array_map(fn($e) => "?", $sel));
            try {
                $st = query("
                    delete from employees
                    where id in ($values)
                ", $sel);

                array_push($messages, [
                    "type" => "positive",
                    "title" => "Successfully Deleted Employee(s)",
                ]);
            } catch (\Throwable $th) {
                echo var_dump($th);
                array_push($messages, [
                    "type" => "negative",
                    "title" => "Failed to Delete Employee(s)"
                ]);
            }
        }
    }

    $st = query("
        select e.* from employees as e
        left join employees_archive as et
        on e.id = et.employee_id
        where et.employee_id is null
    ");
    $entries = $st->fetchAll();
?>
<?php require_once("./../_start.php"); ?>
<a class="link-button" href="./../index.php"><< Home</a>
<a class="link-button" href="./archive.php">Archive</a>
<h1>Employees</h1>
<em>Manage employee records.</em>
<br>
<?php render("./../messages.php", ["messages" => $messages]) ?>
<form class="table" action="./" method="post">
    <div class="controls">
        <a class="control-button create" href="./create.php">Create</a>
        <a class="control-button update" href="./update.php">Update</a>
        <button class="control-button archive" type="submit" name="submit" value="archive">Archive</button>
        <button class="control-button delete" type="submit" name="submit" value="delete">Delete</button>
    </div>
    <table>
        <tr>
            <th>
                <input type="checkbox" data-main-selector-id="0">
            </th>
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
                    <td>
                        <input type="checkbox" name="selected[]" value="<?= $id ?>" data-selector-id="0">
                    </td>
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
            <tr><td colspan="9">No Entries</td></tr>
        <?php } ?>
    </table>
</form>
<?php require_once("./../_end.php"); ?>
<?php if($redirect != null) { ?>
    <script>
        setTimeout(function() {
            window.location="<?= $redirect ?>";
        }, 2000);
    </script>
<?php } ?>