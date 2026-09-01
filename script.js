function checkForm() {
    var inputs = document.querySelectorAll("form input[required], form textarea[required], form select[required]");

    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].value.trim() == "") {
            alert("Please fill all required fields");
            inputs[i].focus();
            return false;
        }
    }

    return true;
}

function confirmDelete() {
    return confirm("Are you sure you want to delete this?");
}

function previewImages(input) {
    var area = document.getElementById("preview");

    if (!area) return;

    area.innerHTML = "";

    if (input.files.length > 5) {
        alert("You can upload maximum 5 images");
        input.value = "";
        return;
    }

    for (var i = 0; i < input.files.length; i++) {
        var reader = new FileReader();

        reader.onload = function(e) {
            var img = document.createElement("img");
            img.src = e.target.result;
            img.style.width = "150px";
            img.style.height = "100px";
            img.style.objectFit = "cover";
            img.style.margin = "5px";
            area.appendChild(img);
        };

        reader.readAsDataURL(input.files[i]);
    }
}

function searchTable(inputId, tableId) {
    var input = document.getElementById(inputId);
    var table = document.getElementById(tableId);

    if (!input || !table) return;

    input.addEventListener("keyup", function() {
        var value = this.value.toLowerCase();
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            rows[i].style.display =
                rows[i].innerText.toLowerCase().includes(value) ? "" : "none";
        }
    });
}