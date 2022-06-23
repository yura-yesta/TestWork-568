function clear_all() {
    document.querySelectorAll('.form_cust').forEach(element => {
        element.value = ''
    })
    clear_img()
}

function clear_img() {
    document.getElementById('cust_img').src = ''
    document.getElementById('myfile').value = ''
    document.getElementById('delete_img').value = true
}

function readFile(input) {

    let reader = new FileReader();

    reader.onload = function (e) {

        document.getElementById('cust_img').src = e.target.result
    }

    reader.readAsDataURL(input.files[0])
}