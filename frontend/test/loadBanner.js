fetch("../navbar.html")
.then(response =>response.text())
.then(data => {
document.getElementById("header").innerHTML= data;
});
