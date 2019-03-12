<?php 
    if(!defined("accessChecker")) {
        die("Direct access not permitted");
    }
?>

<!DOCTYPE html>
<html>

<!-- Create a dropdown menu of options to sort the returned list by and select an option as specified in
    the GET request if it exists. If it doesn't, set the top item as the default selected -->
<div style="display: flex; align-items: center; margin-bottom: 15px">
    <p style="font-size: 1.25em; margin-right: 10px">Order By:</p>
    <form action="?" method="GET" style="margin-top: auto; margin-bottom: auto">
        <select id="orderBySelect" name="orderBySelect" style="font-size: 1.25em">
            <?php                    
                if (!isset($_GET["orderBySelect"])) {
                    $_GET["orderBySelect"] = $optionsValueArray[0];
                }

                for ($i = 0; $i < sizeof($optionsValueArray); $i++) {
                    if ($_GET["orderBySelect"] == $optionsValueArray[$i]) {
                        echo "<option value=" . $optionsValueArray[$i] . " selected>" . $optionsTextArray[$i] . "</option>";
                    } else {
                        echo "<option value=" . $optionsValueArray[$i] . ">" . $optionsTextArray[$i] . "</option>";
                    }
                }
            ?>
        </select>
    </form>
</div>

<script>
    document.getElementById("orderBySelect").addEventListener("change", function (event) {
        var selected = event.target.value;
        var url = location.protocol + '//' + location.host + location.pathname;

        if (location.search.includes("orderBySelect")) {
            var searchParams = location.search.split("&");
            for (i = 0; i < searchParams.length; i++) {
                if (searchParams[i].includes("orderBySelect")) {
                    if (i == 0) {
                        searchParams[i] = ("?orderBySelect=" + selected);
                        url += searchParams[i];
                    } else {
                        searchParams[i] = ("&orderBySelect=" + selected);
                        url += searchParams[i];
                    }
                } else {
                    if (i == 0) {
                        url += searchParams[i];
                    } else {
                        url += "&" + searchParams[i];
                    }
                }
            }
        } else {
            if (location.search.length > 1) {
                url += location.search + "&orderBySelect=" + selected;
            } else {
                url += ("?orderBySelect=" + selected);
            }
        }

        window.location.href = url;
    });
</script>

</html>