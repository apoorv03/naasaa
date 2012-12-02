/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 var p=true, t = true;
 
function priceTimer(ele){

    var typingTimer = 0;
    var doneTypingInterval = 2000;
    clearTimeout(typingTimer);
    if (ele.value) {
        typingTimer = setTimeout(validatePrice(ele), doneTypingInterval);
    }
}
function intTimer(ele){

    var typingTimer = 0;
    var doneTypingInterval = 2000;
    clearTimeout(typingTimer);
    if (ele.value) {
        typingTimer = setTimeout(validateInt(ele), doneTypingInterval);
    }
}

/*****
Util Functions
*****/
function isNumber(n) {


    return !isNaN(parseFloat(n)) && isFinite(n);
}
   

function checkDec(price){

    var decimal = price.indexOf(".");
    if(decimal < 0){
        return true;
    }else if (decimal+3 < price.length){
        return false;
    }else{
        return true;
    }
}


function validationNum(n) {

    return !isNaN(parseFloat(n));
}


function validateFinite(n){
    return isFinite(n);
}

function isSpclChar(n){
    var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":<>?";
    for (var i = 0; i < n.length; i++) {
        if (iChars.indexOf(n.charAt(i)) != -1) {
            return false;
        }
    }
    return true;
}


function ValidatePostal(n) {
    // this function restricts to only numeric
    var currentValue = n.value;
    var lastEntered = currentValue.charAt((currentValue.length-1));
    if(validationNum(lastEntered) && validateFinite(lastEntered)){
        return true;
    }else{
        if(currentValue.length == 1){
            n.value = "";
        }else{
            currentValue = currentValue.substring(0,(currentValue.length-1));
            n.value = currentValue;
        }
        return true;
    }

}

function ValidateBarcode(n) {
    // this function restricts to only numeric
    var currentValue = n.value;
    var lastEntered = currentValue.charAt((currentValue.length-1));
    if(validationNum(lastEntered) && validateFinite(lastEntered)){
        return true;
    }else{
        if(currentValue.length == 1){
            n.value = "";
        }else{
            currentValue = currentValue.substring(0,(currentValue.length-1));
            n.value = currentValue;
        }
        return true;
    }

}

function ValidateText(n){
    // only allows Text ONly.. No number, No Special characters
    var currentValue = n.value;
    var lastEntered = currentValue.charAt((currentValue.length-1));
    if(!validationNum(lastEntered) && isSpclChar(lastEntered)){

        return true;
    }else{

        if(currentValue.length == 1){
            n.value = "";
        }else{
            currentValue = currentValue.substring(0,(currentValue.length-1));
            n.value = currentValue;
        }
        return true;
    }
}

function ValidateEmail(n){
    var x=n.value;
    var atpos=x.indexOf("@");
    var dotpos=x.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
    {
        alert("Not a valid e-mail address");
        return false;
    }
    return true;
}



function validatePrice(ele){
    var price = ele.value.split(' ').join('');
    var container= document.getElementById("error"+ele.id);
    p = false;
    if(price === "0" || price ==='' || price === null ){
        container.innerHTML = "<font color=\"red\"> This field is Required * </font>";
    }else if(!isNumber(price)){
        container.innerHTML = "<font color=\"red\">Numbers only</font>";
    }else if(!checkDec(price)){
        container.innerHTML = "<font color=\"red\"> Up To 2 Decimal Place Only! </font>";
    }else{
        ele.setAttribute("class", "");
        container.innerHTML = "<font color=\"Green\"> Good! </font>";
        p = true;
    }

    ele.value = price;
    checkAll();
}

function validateInt(ele){
    var container = document.getElementById("error"+ele.id);
    var threshold = ele.value.split(' ').join('');
    t=false;
    if(threshold =='' || threshold == null ){
        container.innerHTML = "<font color=\"red\">This field is Required *  </font>";
    }else if(!isNumber(threshold)){
        container.innerHTML = "<font color=\"red\">Numbers Only </font>";
    }else{
        container.innerHTML = "<font color=\"Green\">Good! </font>";
        t= true;
    }
    checkAll();
   
}


function checkAll(){
    if(p && t){
		document.getElementById("sub").disabled = false;
        
    }else{
		document.getElementById("sub").disabled = true;
			
    }
}