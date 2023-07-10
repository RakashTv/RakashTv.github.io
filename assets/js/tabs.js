function openTab(event, tab){
    let i, tabcontent, tablinks;
    tablinks = document.getElementsByClassName("tab-links");
    for(i = 0; i < tablinks.length; i++){
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    tabcontent = document.getElementsByClassName("tab-content");
    for(i = 0; i < tabcontent.length; i++){
        tabcontent[i].style.display = "none";
    }

    document.getElementById(tab).style.display = "block";
    event.currentTarget.className += " active";
}

document.getElementById("default-open").click();