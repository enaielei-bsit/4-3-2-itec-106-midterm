<?php require_once("./../_init.php") ?>
<?php
    $user = signedIn(null, "./../sign-in.php");

    $messages = [];
    $redirect = null;
    if(check($_POST, "submit", "create")) {
        $emp = $_POST["employee"];
        keep($emp, "id", "family_name", "given_name", "middle_name", "age", "sex", "mobile_number", "address");
        $app = function($k, $v) {
            $v = trim($v);
            if($k == "sex") $v = ($v == "1") ? "1" : "0";
            if($k == "age") $v = (int) $v;
            return $v;
        };
        apply($emp, $app);
        $subtitles = [];

        if(empty($emp["id"])) array_push($subtitles, "Employee ID must not be empty.");
        else {
            if(strlen($emp["id"]) != 8 || !is_numeric($emp["id"]))
                array_push($subtitles, "Employee ID must be 8 digits.");
            else {
                $query = query("select id from employees where employee_id = ?", [$emp["id"]]);
                if($query->rowCount() > 0)
                    array_push($subtitles, "Employee ID already exists."); 
            }
        }

        if(empty($emp["family_name"])) array_push($subtitles, "Family Name must not be empty.");

        if(empty($emp["given_name"])) array_push($subtitles, "Given Name must not be empty.");

        if(empty($emp["mobile_number"])) array_push($subtitles, "Mobile Number must not be empty.");
        elseif(strlen($emp["mobile_number"]) != 11 || !is_numeric($emp["mobile_number"]))
            array_push($subtitles, "Mobile Number must be 11 digits.");
        
        if(empty($emp["address"])) array_push($subtitles, "Address must not be empty.");

        if(!empty($subtitles)) array_push($messages, [
            "type" => "negative",
            "title" => "Employee Registration Failed",
            "subtitles" => $subtitles
        ]);
        else {
            try {
                $emp["employee_id"] = pop($emp, "id");
                
                $keys = array_keys($emp);
                $columns = join(", ", $keys);
                $values = join(", ", array_map(fn($e) => ":{$e}", $keys));
                query("insert into employees ({$columns}) values ({$values})", $emp);

                array_push($messages, [
                    "type" => "positive",
                    "title" => "Employee Registration Succeeded",
                    "subtitle" => "An employee has been registered."
                ]);
            } catch (\Throwable $th) {
                echo var_dump($th);
                array_push($messages, [
                    "type" => "negative",
                    "title" => "Employee Registration Failed",
                    "subtitle" => "Something went wrong while registering the employee."
                ]);
            }
        }
    }
?>
<?php require_once("./../_start.php"); ?>
<a class="link-button" href="./index.php"><< Employees</a>
<h1>Create Employee</h1>
<em>Register an employee record.</em>
<br>
<?php render("./../messages.php", ["messages" => $messages]) ?>
<form action="./create.php" method="post">
    <div class="field">
        <label for="employee_id">
            Employee ID <?php render("./../hint.php", ["char" => "*",
                "title" => "Required and must be a unique 8-digit number"]); ?>
        </label>
        <input type="text" name="employee[id]" minlength="8" maxlength="8" id="employee_id" required>
    </div>
    <div class="field">
        <label for="employee_family_name">
            Family Name <?php render("./../hint.php", ["char" => "*",
                "title" => "Required"]); ?>
        </label>
        <input type="text" name="employee[family_name]" id="employee_family_name" required>
    </div>
    <div class="field">
        <label for="employee_given_name">
            Given Name <?php render("./../hint.php", ["char" => "*",
                "title" => "Required"]); ?>
        </label>
        <input type="text" name="employee[given_name]" id="employee_given_name" required>
    </div>
    <div class="field">
        <label for="employee_middle_name">
            Middle Name
        </label>
        <input type="text" name="employee[middle_name]" id="employee_middle_name">
    </div>
    <div class="field">
        <label for="employee_age">
            Age <?php render("./../hint.php", ["char" => "*",
                "title" => "Required"]); ?>
        </label>
        <input type="number" name="employee[age]" min="0" value="0" id="employee_age" required>
    </div>
    <div class="field">
        <label>
            Sex <?php render("./../hint.php", ["char" => "*",
                "title" => "Required"]); ?>
        </label>
        <div>
            <input type="radio" name="employee[sex]" value="1" id="employee_sex_male" required> <label for="employee_sex_male">Male</label>
            <input type="radio" name="employee[sex]" value="0" id="employee_sex_female" required> <label for="employee_sex_female">Female</label>
        </div>
    </div>
    <div class="field">
        <label for="employee_mobile_number">
            Mobile Number <?php render("./../hint.php", ["char" => "*",
                "title" => "Required and must be 11 digits"]); ?>
        </label>
        <input type="text" name="employee[mobile_number]" minlength="11" maxlength="11" id="employee_mobile_number" required>
    </div>
    <div class="field">
        <label for="employee_address">
            Address <?php render("./../hint.php", ["char" => "*",
                "title" => "Required"]); ?>
        </label>
        <input type="text" name="employee[address]" id="employee_address" required>
    </div>
    <div class="field">
        <button type="submit" name="submit" value="create">Submit</button>
    </div>
</form>
<?php require_once("./../_end.php"); ?>
<?php if($redirect != null) { ?>
    <script>
        setTimeout(function() {
            window.location="<?= $redirect ?>";
        }, 7000);
    </script>
<?php } ?>