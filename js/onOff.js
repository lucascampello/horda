function onOff(){
    document
        .querySelector("#aviso")
        .classList
        .toggle("hide")

    document
        .querySelector("body")
        .classList
        .toggle("hideScroll")
}

onOff();