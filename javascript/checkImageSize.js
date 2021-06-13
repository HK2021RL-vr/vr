let fileSizeLimit = 1 * 1024 *1024;

window.onload = function() {
    document.getElementById("js_photo_submit").disabled = true;
    document.getElementById("file_input").addEventListener("change", checkSize);
}

function checkSize() {
    if(document.getElementById("file_input").files[0].size <= fileSizeLimit) {
        document.getElementById("js_photo_submit").disabled = false;
        document.getElementById("notice").innerHTML ="";
    }
    else {
        document.getElementById("js_photo_submit").disabled = true;
        document.getElementById("notice").innerHTML ="Fail on <strong>liiga suur</strong>";
    }
}