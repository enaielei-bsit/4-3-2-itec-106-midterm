import Selector from "./selector";

$(() => {
    const selector = new Selector("0");
    selector.initialize((ev) => {
        const val = selector.selected.length > 0 ? selector.selected[selector.selected.length - 1] : null;
        if(val != null) {
            $(".control-button.update").attr("href", `./update.php?id=${val.value}`);
        } else 
            $(".control-button.update").attr("href", `./update.php`);
    });

    let action = "";

    $("form.table .archive").on("click", function() {
        action = "archive";
    });

    $("form.table .delete").on("click", function() {
        action = "delete";
    });

    $("form.table").on("submit", function() {
        const l = selector.selected.length;
        if(selector.selected.length > 0) {
            if(confirm(`Are you sure you want to ${action} the ${l} selected record(s)?`)) {
                return true;
            }
        }
        return false;
    });
});